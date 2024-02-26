<?php


namespace Drupal\Liquidadorepa\Form;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;


/**
 * Implementando un liquidador para el EPA Cartagena.
 */
/**
 * Generando un formulario de 4 pasos.
 *
 * @see \Drupal\Core\Form\FormBase
 */


class Liquidadorepa_FormEj extends FormBase
{
  /**
   * The session object.
   *
   * We will use this to store information that the user submits, so that it
   * persists across requests.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;
  /**
   * The cache tag invalidator service.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagInvalidator;





 /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */

  protected $mailManager;

  /**
   * The email validator.
   *
   * @var \Drupal\Component\Utility\EmailValidator
   */
  protected $emailValidator;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new form.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   *   The email validator.
   */
    /**
   * Constructs a new Session object.
   *
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session object.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $invalidator
   *   The cache tag invalidator service.
   */

  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EmailValidator $email_validator,SessionInterface $session, CacheTagsInvalidatorInterface $invalidator) {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->emailValidator = $email_validator;
    $this->session = $session;
    $this->cacheTagInvalidator = $invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $form = new static(
      $container->get('plugin.manager.mail'),
      $container->get('language_manager'),
      $container->get('email.validator'),
      $container->get('session'),
      $container->get('cache_tags.invalidator')
    );
    $form->setMessenger($container->get('messenger'));
    $form->setStringTranslation($container->get('string_translation'));

    return $form;
  }

  /**
   * @return string
   */
  public function getFormId()
  {
    return 'liquidadorepa_formej';
  }
  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */

  public function buildForm(array $form, FormStateInterface $form_state)
  {
       if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
         return self::fapiExamplePageTwo($form, $form_state);
        }
       if ($form_state->has('page_num') && $form_state->get('page_num') == 3) {
         return self::fapiExamplePageThree($form, $form_state);
        }
       if ($form_state->has('page_num') && $form_state->get('page_num') == 4) {
        return self::fapiExamplePageFour($form, $form_state);
        }


           $form_state->set('page_num', 1);
           $form['description'] = [
            '#type' => 'item',
            '#title' => $this->t('<h1>Información de Contacto</h1>'),
           ];
	         $form['s001'] = [
            '#type' => 'markup',
            '#markup' => '<hr><p><div>Los campos obligatorios estan marcados con  un (*)</div></p>',
           ];
           $form['s002'] = [
            '#type' => 'markup',
            '#markup' => '<hr><div><p> Ingresar en esta página la información de titular del tramite ambiental, siendo el titular la persona Jurídica que solicita la viabilidad.</p> </div>',
            ];

            $form['id_document_ej'] = [
              '#type' => 'textfield',
              '#required' => TRUE,
              '#title' => 'NIT',
              '#default_value' => $this->session->get('session_liquidacion.id_document_ej',''),
              ];

              $form['name_ej'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'Razón Social ',
                '#description' => 'Razón Social de la entidad.',
                '#default_value' => $this->session->get('session_liquidacion.name_ej', ''),
               ];
               $form['nombrelegal_ej'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'Nombre de Representante legal ',
                '#description' => 'Razón Social de la entidad.',
                '#default_value' => $this->session->get('session_liquidacion.nombrelegal_ej', ''),

               ];
               $form['idlegal_ej'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'N&#176; de Documento de Representante legal ',
                '#description' => 'Documento del Representante legal.',
                '#default_value' => $this->session->get('session_liquidacion.idlegal_ej', ''),
               ];
               $form['dir_correspondencia_ej'] = [
                '#type' => 'textfield',
                '#required' => TRUE,
                '#title' => 'Dirección de Correspondencia del Solicitante',
                '#default_value' => $this->session->get('session_liquidacion.dir_correspondencia_ej', ''),
               ];
               $form['email_ej'] = [
                '#type' => 'email',
               '#required' => TRUE,
                '#title' => 'Correo Electrónico',
                '#default_value' => $this->session->get('session_liquidacion.email_ej', ''),
               ];


               $form['tfijo_ej'] = [
                '#type' => 'textfield',
                '#title' => 'Teléfono fijo',
                '#default_value' => $this->session->get('session_liquidacion.tfijo_ej', ''),
                ];


                $form['tmovil_ej'] = [
                  '#type' => 'textfield',
                  '#required' => TRUE,
                  '#title' => 'Teléfono móvil',
                  '#default_value' => $this->session->get('session_liquidacion.tmovil_ej', ''),
                 ];

                /* $form['estrato'] = array(
                  '#title' => t('Estrato'),
                  '#default_value' => $this->session->get('session_liquidacion.estrato_ej', ''),
                  '#type' => 'select',
                     '#description' => 'Seleccionar el estrato de quien realiza la liquidación.',
                  '#options' => array(t('--- Seleccionar ---'), t('1'), t('2'), t('3'), t('4') , t('5') , t('6')    ),
                 );

                 $form['condicion'] = array(
                    '#title' => t('¿Se encuentra en alguna condición especial?'),
                    '#default_value' => $this->session->get('session_liquidacion.condicion_ej', ''),
                    '#type' => 'select',
                    '#options' => array(t('Ninguno'), t('Adulto mayor'), t('Habitante de la calle'), t('Mujer gestante'), t('Peligro inminente') , t('Persona en condición de discapacidad') , t('Víctima del conflicto armado') , t('Menor de edad')     ),
                   );*/
                 $form['s0'] = [
                    '#type' => 'markup',
                    '#markup' => '<hr>',
                 ];

               // Group submit handlers in an actions element with a key of "actions" so
               // that it gets styled correctly, and so that other modules may add actions
              // to the form. This is not required, but is convention.
              $form['actions'] = [
               '#type' => 'actions',
               ];

              $form['actions']['next'] = [
                '#type' => 'submit',
                '#button_type' => 'primary',
                '#value' => $this->t('Next'),
               // Custom submission handler for page 1.
                '#submit' => ['::fapiExampleMultistepFormNextSubmit'],
               // Custom validation handler for page 1.
                //'#validate' => ['::fapiExampleMultistepFormNextValidate'],
              ];








    $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr><br/>',
    ];

    return $form;
  }
  /**
   * Store a form value in the session.
   *
   * Form values are always a string. This means an empty string is a valid
   * value for when a user wants to remove a value from the session. We have to
   * handle this special case for the session object.
   *
   * @param string $key
   *   The key.
   * @param string $value
   *   The value.
   */
  protected function setSessionValue($key, $value) {
    if (empty($value)) {
      // If the value is an empty string, remove the key from the session.
      $this->session->remove($key);
    }
    else {
      $this->session->set($key, $value);
    }
  }

  /**
   * {@inheritdoc}
   */

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
     public function validateForm(array &$form, FormStateInterface $form_state)
      {

       }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {


    $codigo_liquidacion =$this->session->get('session_liquidacion.id_document_ej', '');
    $name_contrib = $this->session->get('session_liquidacion.name_ej', '');
    $barrio_liquidacion = $this->session->get('session_liquidacion.barrio_ej', '');

    $valor_liquidacion  = $this->session->get('session_liquidacion.valorLiquidacion_ej', '');

    $valor_tarifa = $this->session->get('session_liquidacion.valor_tarifa_ej', '');

    $valor_evento = $this->session->get('session_liquidacion.valor_evento_ej', '');
    $valore =$valor_evento;
    $descripcion_evento = $this->session->get('session_liquidacion.descripcion_evento_ej', '');
    $direccion_evento = $this->session->get('session_liquidacion.direccion_evento_ej', '');


    $valor_evento = number_format ($valor_evento, 2, ',', '.');

    $tipo_solicitante = "Persona Jurídica";
    $id_contribuyente = $this->session->get('session_liquidacion.id_document_ej', '');
    $numero_dias = $this->session->get('session_liquidacion.numero_dias_ej', '');
    $nombrelegal = $this->session->get('session_liquidacion.nombrelegal_ej', '');
    $idlegal = $this->session->get('session_liquidacion.idlegal_ej', '');

    $dir_correspondecia_contrib = $this->session->get('session_liquidacion.dir_correspondencia_ej', '');
    $email_cotrib = $this->session->get('session_liquidacion.email_ej', '');
    $tfijo =$this->session->get('session_liquidacion.tfijo_ej', '');
    $tmovil = $this->session->get('session_liquidacion.tmovil_ej', '');
    /*$estrato = $this->session->get('session_liquidacion.estrato_ej', '');
    $condicion = $this->session->get('session_liquidacion.condicion_ej', '');*/

        $f1 =$this->session->get('session_liquidacion.fecha_inicial_ej', '');
        $file1 = $this->session->get('session_liquidacion.soportes1_ej');
        $file2 = $this->session->get('session_liquidacion.soportes2_ej');
        $file3 = $this->session->get('session_liquidacion.soportes3_ej');
        $file4 = $this->session->get('session_liquidacion.soportes4_ej');
   /*

Creando un nodo tipo factura con los datos recibidos

**/
$my_article = Node::create(['type' => 'liquidacion']);
$my_article->set('title', $codigo_liquidacion);
$my_article->set('field_valor', $valor_liquidacion);
$my_article->set('field_barrio_liquidacion', $barrio_liquidacion);
$my_article->set('field_concepto_ambiental_liq', "Eventos");
$my_article->set('field_direccion_correspondencia', $dir_correspondecia_contrib);
$my_article->set('field_direccion_del_predio', $direccion_evento);


$my_article->set('field_valor_evento', $valore);
     $my_article->set('field_descripcion_evento', $descripcion_evento );

$my_article->set('field_tipo_de_solicitante', $tipo_solicitante);
$my_article->set('field_id_contribuyente', $id_contribuyente);

//id representante legal field_id_contribuyente
// NOmre representnte legal

$my_article->set('field_nombre_contribuyente', $name_contrib);
$my_article->set('field_email_contribuyente', $email_cotrib );
$my_article->set('field_telefono_fijo_contribuyent', $tfijo);
$my_article->set('field_telefono_movil_contribuyen', $tmovil);
$my_article->set('field_estrato_contribuyente', $estrato);
//$my_article->set('field_condicion_contribuyente', $condicion);
$my_article->set('field_comparado_factura',false);
//$my_article->set('field_codigo_liquidacion_factura', false);
$my_article->set('field_estado',FALSE);

$my_article->set('field_id_file', $file1);
$my_article->set('field_rut_file', $file2);
$my_article->set('field_ei_file', $file3);
$my_article->set('field_c_rp_legal', $file4);





$my_article->set('status', '0');
//$my_article->set('uid', $id_contribuyente);

$my_article->enforceIsNew();
  $my_article->save();

$nid = $my_article->id();
$node = \Drupal\node\Entity\Node::load($nid);; //Accedemos a la información del nodo
/** Obteniendo el field_consecutivo_liquidacion del nodo creado */
  $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
$sec ="01"."0".$consecutivo_facturas[0]["value"].date('Y');

$node->set('title', $sec); //Accedemos al campo que queremos editar/modificar
//$node->set('field_codigo_liquidacion_factura', $sec); //valor liquidacion
//$node->set('fild_valor', $sec); //Accedemos al campo que queremos editar/modificar
$html= ' <style>

.page-title {
display: none;
}
.layout.layout--threecol-section.layout--threecol-section--33-34-33 {
border: 1px solid #000;
}
.field.field--name-field-enlace-externo.field--type-link.field--label-hidden.field__item {
text-align: center;
}
.layout__region.layout__region--first {
text-align: center;
}

.barcode.barcode-codabar {
padding: 1.1em 0.6em;
border: 1px solid #ccc;
/* background: #efefef; */
width: 97%;
/* background: rgba(0,0,0,0.063); */
}
tr td, tr th {
padding: 0;
text-align: left;
border: 1px solid #000;
}
th, td {
border: 1px solid black;
}
p {
margin: 0;
}
</style>



<table>
<tbody>

 <tr>
<td rowspan="5">
EPA | Zona Liquidaciones
</td>
  </tr>
   <td colspan="3">
   <p>Establecimiento Público Ambiental EPA-Cartagena</p>
   </td>
 </tr>
 <tr>
   <td colspan="3">
   <p>Nit 806013999-2</p>
   </td>
 </tr>
 <tr>
   <td colspan="3">
   <p>Subdirección Administrativa y Financiera</p>
   </td>
 </tr>
 <tr>
   <td colspan="3">
   <p>Manga Calle 4 AVENIDA EDIFICIO SEAPORT</p>
   </td>
 </tr>
 <tr>
   <td colspan="3">
   <p>Liquidación No '.$sec.'</p>
   </td>
 </tr>
 <tr>
 <td ><p>FECHA:</p></td>
 <td  colspan="3">
 <p>'.date("Y/m/d").'</p>
 </td>
</tr>
<tr>
<td ><p>ASUNTO:</p></td>
<td  colspan="3">
<p>VIABILIDAD DE EVENTOS</p>
</td>
</tr>
<tr>
<td ><p>PETICIONARIO / EMPRESA:</p></td>
<td  colspan="3">
<p>'.$name_contrib.'</p>
</td>
</tr>
<tr>
<td ><p>DIRECCION:</p></td>
<td  colspan="3">
<p>'.$dir_correspondecia_contrib.'</p>
</td>
</tr>
<tr>
<td ><p>CORREO:</p></td>
<td  colspan="3">
<p>'.$email_cotrib.'</p>
</td>
</tr>

<tr>
<td ><p>Id Solicitante:</p></td>
<td  colspan="3">
<p>'.$id_contribuyente.'</p>
</td>
</tr>
<tr>
<td ><p>TELÉFONO:</p></td>
<td  colspan="3">
<p>'.$tmovil.'</p>
</td>
</tr>
 <tr>
   <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 107 de 17 de febrero de 2021 para este monto de proyecto: </p></td>
   <td colspan="3">
   <p>$ '.$valor_tarifa.'</p>
   </td>
 </tr>
 <tr>
   <td><p>VALOR EVENTO</p></td>
   <td>
   <p> $'.$valor_evento.'</p>
   </td>
   <td >TOTAL LIQUIDACIÓN</td>
   <td >
   <p style="
font-weight: bold;
">$ '.$valor_liquidacion.'</p>
   </td>
 </tr>
 <tr>
   <td colspan="4">
   <p>CONSIDERACIONES</p>

   <p>Categorización de profesionales con base en la Resolución 1280 de 2010 del MAVDT y afectados por un factor multiplicador Factor de administración de acuerdo a la resolución 212 de 2004 del MAVDT</p>

   <p>Esta suma deberá&nbsp;consignarse en la Cuenta de Ahorros No. 43300400033-0 del Banco GNB sudameris, a favor del EPA-Cartagena. Para efectos de acreditar la cancelación de los costos indicados, el usuario deberá presentar original del recibo de consignación, y entregar copia</p>

   <p>Favor no hacer retención por ningún concepto, somos no contribuyentes Según Art. 23 Art 369 y Ley 633 de 2000, Art. 5</p>
   </td>
 </tr>
 <tr>
   <td colspan="4">
   <p>CONCEPTO</p>

   <div class="concepto">
   <p class="concepto">LIQUIDACION DE VIABILIDAD PARA REALIZACIÓN DE EVENTOS,REALIZACIÓN DE EVENTO CON COSTO DE PROYECTO : '.$valor_evento.' pesos Colombianos MLV, PARA '.$numero_dias.' DÍAS, SEGÚN SOLICITUD #'.$sec.'</p>
   </div>
   </td>
 </tr>


</tbody>

</table>

<table>
<tbody>

<tr>
<td>
Detalle del evento: <p>'.$descripcion_evento.'<p>



</td>
</tr>


</tbody>

</table>

';
$valor = $this->session->get('session_liquidacion.valorLiquidacion_pdf','');
	    $code="4157709998461239"."8020".$sec."3900".$valor."96".date('Y')."1231";
  $code_content="(415)7709998461239"."(8020)".$sec."(3900)".$valor."(96)".date('Y')."1231";
$node->set('body',$html);
$node->body->format = 'full_html';
$node->save(); //Guarda el cambio una vez realizado


    $module = 'Liquidadorepa';
    $key = 'red';
    // Specify 'to' and 'from' addresses.
  $to =$email_cotrib ;
    $from =  $this->config('system.site')->get('mail');
   $params = [];
   //$params['message'] = 'Mail Body';
   //$params['subject'] = 'Sample Subject';
   $language_code = $this->languageManager->getDefaultLanguage()->getId();

   $send_now = TRUE;





 $mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']);
 $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-L']);
 $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
 $mpdf->SetHTMLHeader('
 <div style="text-align: right; font-weight: bold;">
    EPA
 </div>','O');
 $mpdf->SetHTMLFooter('
<table width="100%">
    <tr>
        <td width="33%">{DATE j-m-Y}</td>
        <td width="33%" align="center">{PAGENO}/{nbpg}</td>
        <td width="33%" style="text-align: right;">EPA</td>
    </tr>
</table>
<table class="items" width="100%" cellpadding="8" border="1">
<thead>
	<tr>
		<td>Contenido de la clave de pago</td>
    <td>'.$code_content.'</td>

	</tr>
</thead>
<tbody>
<tr>

<td>Clave de Pago</td>
<td class="barcodecell"><barcode code="'.$code.'" type="EAN128B" class="barcode" /></td>
</tr>
</tbody>
</table>

');

 $mpdf->WriteHTML($html);
 $file = $mpdf->Output($sec.'.pdf', 'D');

$params['attachments'][] = [
    'filecontent' => $file,
    'filename' => $sec.'.pdf',
    'filemime' => 'application/pdf',
  ];


    //$params['mail_title'] = 'Cobros';
   // $params['body'] = 'Gracias!';
    //$params['message'] = 'Gracias!';

	  /////////////////////
	  /*
    $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);


    if ($result['result'] == TRUE) {
		  $f11= strtotime($form_state->getValue('fecha_Inicial'));
	  $dt=\Drupal::time()->getCurrentTime();
	 $diff2 =($f11-$dt)/86400;
     if ($diff2 < 10){
    $this->messenger()->addStatus($this->t('Su fecha es menor a 10 días ,al enviar la solicitud de viavilidad acepta el riezgo de que su tramite no se aceptado', ['@title' =>$N]));
}
    $this->messenger()->addStatus($this->t('El documento se ha generado y se ha enviado un correo con lmas instrucciones, de lo contrario favor comunacar con el correo: info@liquidaciones.epacartagena.gov.co'));

		  $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
           $form_state->setRedirectUrl($url);


    }
    else {
      $this->messenger()->addMessage($this->t('Mensaje no enviado validar dirección de correo!.'), 'error');
    }

*/
    $this->invalidateCacheTag();
    $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
    $form_state->setRedirectUrl($url);

    }


     /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextValidate(array &$form, FormStateInterface $form_state) {




    $vocabulary_name = 'smlv';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);


    foreach ($terms as $term) {
      $id2 = $term->getFields();
          $value  = $term->get('field_smlv')->getValue();
          $valor =$value[0]["value"];
    }
    $valor =$value[0]["value"];
    $valor_tarifa_evento_25 = $valor * 25 ;
    $valor_tarifa_evento_35 = $valor * 35 ;
    $valor_tarifa_evento_50 = $valor * 50 ;
    $valor_tarifa_evento_70 = $valor * 70 ;
    $valor_tarifa_evento_100 = $valor * 100 ;
    $valor_tarifa_evento_200 = $valor * 200 ;
    $valor_tarifa_evento_300 = $valor * 300 ;
    $valor_tarifa_evento_400 = $valor * 400 ;
    $valor_tarifa_evento_500 = $valor * 500 ;
    $valor_tarifa_evento_700 = $valor * 700 ;
    $valor_tarifa_evento_900 = $valor * 900 ;
    $valor_tarifa_evento_1500 = $valor * 1500 ;
    $valor_tarifa_evento_2115 = $valor * 2115 ;
    $valor_tarifa_evento_8458 = $valor * 8458 ;


    $valor_liquidacion =$form_state->getValue('valor_evento_ej');
    $numero_dias = $form_state->getValue('numero_dias_ej');

    if ($valor_liquidacion < $valor_tarifa_evento_25) {
      $valor_tarifa = 163732;//ok
      $valor_liquidacion = 163732 *  $numero_dias ;
      $valor_liquidacion_r = 118600 *  $numero_dias ;
    } elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
        $valor_tarifa = 229488;//ok
      $valor_liquidacion = 229488 *  $numero_dias ;
      $valor_liquidacion_r = 166200 *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_35  && $valor_liquidacion < $valor_tarifa_evento_50) {
        $valor_tarifa =328121;
      $valor_liquidacion =328121  *  $numero_dias;
      $valor_liquidacion_r = 237600 *  $numero_dias ;

    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_50  && $valor_liquidacion < $valor_tarifa_evento_70 ) {
        $valor_tarifa =459633;
      $valor_liquidacion = 459633  *  $numero_dias ;
      $valor_liquidacion_r =  332850 *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_70  && $valor_liquidacion < $valor_tarifa_evento_100) {
        $valor_tarifa =  656900;
      $valor_liquidacion = 656900  *  $numero_dias ;
      $valor_liquidacion_r =  475700  *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_100  && $valor_liquidacion < $valor_tarifa_evento_200) {
        $valor_tarifa =  1314458;
      $valor_liquidacion = 1314458 * $numero_dias;
      $valor_liquidacion_r =  951800  *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_200  && $valor_liquidacion < $valor_tarifa_evento_300) {
        $valor_tarifa = 1972015;
     $valor_liquidacion = 1972015 *  $numero_dias;
     $valor_liquidacion_r =  1428000  *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_300  && $valor_liquidacion < $valor_tarifa_evento_400) {
        $valor_tarifa = 2629573;
     $valor_liquidacion =  2629573  *  $numero_dias ;
     $valor_liquidacion_r =  1904150  *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_400  && $valor_liquidacion < $valor_tarifa_evento_500) {
        $valor_tarifa = 3287130;
      $valor_liquidacion =  3287130  *  $numero_dias  ;
      $valor_liquidacion_r = 2380300  *  $numero_dias;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_500  && $valor_liquidacion < $valor_tarifa_evento_700) {
        $valor_tarifa =4602245;
     $valor_liquidacion = 4602245*  $numero_dias ;
     $valor_liquidacion_r = 3332600 *  $numero_dias;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_700  && $valor_liquidacion < $valor_tarifa_evento_900) {
        $valor_tarifa = 5917360;
      $valor_liquidacion = 5917360  *  $numero_dias ;
      $valor_liquidacion_r = 4284900 *  $numero_dias;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_900  && $valor_liquidacion < $valor_tarifa_evento_1500) {
        $valor_tarifa =98627064;
     $valor_liquidacion = 98627064  *  $numero_dias ;
     $valor_liquidacion_r = 98627060 *  $numero_dias;
    }elseif ($valor_liquidacion >= $valor_tarifa_evento_1500  && $valor_liquidacion < $valor_tarifa_evento_2115) {
      $valor_tarifa =13906685;
      $valor_liquidacion = 13906685 *  $numero_dias;
      $valor_liquidacion_r = 10917550 *  $numero_dias;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_2115  && $valor_liquidacion < $valor_tarifa_evento_8458) {
      $valor_tarifa =13906685;
      $valor_liquidacion =37374939 *  $numero_dias;
      $valor_liquidacion_r =37374939 *  $numero_dias;

	}else {
      /*$valor_tarifa =($valor_evento * 0.4)/100;
      $valor_liquidacion = ( ($valor_evento * 0.4)/100) ;*/
	  $valor_tarifa =208879615;
     /* $valor_liquidacion =37374939 *  $numero_dias;
      $valor_liquidacion_r =37374939 *  $numero_dias;*/
    }
     $valor = $valor_liquidacion;
     $this->setSessionValue('session_liquidacion.valorLiquidacion_pdf',$valor_liquidacion);
    $valor_tarifa = number_format($valor_tarifa, 2, ',', '.');
    $valor_liquidacion = number_format($valor_liquidacion, 2, ',', '.');


    $this->setSessionValue('session_liquidacion.valorLiquidacion_ej', $valor_liquidacion );

    $this->setSessionValue('session_liquidacion.valor_tarifa_ej',  $valor_tarifa  );



$now = DrupalDateTime::createFromTimestamp(time());
$now->setTimezone(new \DateTimeZone('UTC'));

	    $f1= strtotime($form_state->getValue('fecha_Inicial_ej'));
        $cantidad_dias=  $form_state->getValue('numero_dias_ej');
	    $f_limit=strtotime($form_state->getValue('fecha_Final_ej'));
	     $dt=strtotime($now->format('Y-m-d'));
	     $diff =($f_limit-$f1)/86400;
	     $diff02 =($f110-$dt)/86400;

	    if (  $f1 == $f_limit    ) {
			  $sameday = true;
			$diff = 1;

		  }else{ $sameday = false;}

	  	     if ($diff02 < 10){
      $alert='<div class="alertaproximidad">Tenga en cuenta la fecha de su envento antes de liquidar. Su Solicitud tiene un tiempo de respuesta de 15 dias hábiles Contados a partir de la fecha en la que sea adjuntado el soporte de pago y la documentación requerida en el formulario, De conformidad con la ley 1437 del 2011</div>';

      $this->setSessionValue('session_liquidacion.alert_ej', $alert);
}
	  	      if ($f1 > $f_limit){
    $form_state->setErrorByName('fecha_inicial_ej', $this->t('La fecha inicial no puede ser menor a la final '));
}
	      if ($cantidad_dias != $diff  ){

			  if($diff == 0 || $sameday == false){
			  $diff = "Error";
			  }
			  $error_dias ="". $diff;
    $form_state->setErrorByName('fecha_Inicial_ej', $this->t('La Cantidad de días no es correcta '. $error_dias));

			   $form_state->setValue('s0', 'test');
}

  }
             /**
             * Provides custom submission handler for page 1.
             *
             * @param array $form
             *   An associative array containing the structure of the form.
             * @param \Drupal\Core\Form\FormStateInterface $form_state
             *   The current state of the form.
             */
           public function fapiExampleMultistepFormNextSubmit(array &$form, FormStateInterface $form_state)
           {
            $form_state
            ->set('page_values2', [
             // Keep only first step values to minimize stored data.
            'name' => $form_state->getValue('name_ej'),

             ])

             ->set('page_num', 2)

             ->setRebuild(TRUE);
                 $this->setSessionValue('session_liquidacion.id_document_ej', $form_state->getValue('id_document_ej'));
                 $this->setSessionValue('session_liquidacion.name_ej', $form_state->getValue('name_ej'));
                 $this->setSessionValue('session_liquidacion.dir_correspondencia_ej', $form_state->getValue('dir_correspondencia_ej'));
                 $this->setSessionValue('session_liquidacion.email_ej', $form_state->getValue('email_ej'));
                /* $this->setSessionValue('session_liquidacion.estrato_ej', $form_state->getValue('first_estrato'));*/
                 $this->setSessionValue('session_liquidacion.tmovil_ej', $form_state->getValue('tmovil_ej'));
                 $this->setSessionValue('session_liquidacion.tfijo_ej', $form_state->getValue('tfijo_ej'));
                 $this->setSessionValue('session_liquidacion.nombrelegal_ej', $form_state->getValue('nombrelegal_ej'));
                 $this->setSessionValue('session_liquidacion.idlegal_ej', $form_state->getValue('idlegal_ej'));

             }

 /**
   * Provides custom submission handler for page 3.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextSubmit2(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page_values3', [
        // Keep only first step values to minimize stored data.
        'first_name' => $form_state->getValue('fecha_Inicial'),


      ])
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.descripcion_evento_ej', $form_state->getValue('descripcion_evento_ej'));
  $this->setSessionValue('session_liquidacion.barrio_ej', $form_state->getValue('barrio_ej'));
  $this->setSessionValue('session_liquidacion.valor_evento_ej', $form_state->getValue('valor_evento_ej'));
  $this->setSessionValue('session_liquidacion.numero_dias_ej', $form_state->getValue('numero_dias_ej'));
  $this->setSessionValue('session_liquidacion.direccion_evento_ej', $form_state->getValue('direccion_evento_ej'));

  }

  /**
   * Provides custom submission handler for page 3.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextSubmit3(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page_values4', [
        // Keep only first step values to minimize stored data.

        'color3' => $form_state->getValue('color3'),

      ])
      ->set('page_num', 4)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.soportes1_ej', $form_state->getValue('soportes1_ej'));
      $this->setSessionValue('session_liquidacion.soportes2_ej', $form_state->getValue('soportes2_ej'));
      $this->setSessionValue('session_liquidacion.soportes3_ej', $form_state->getValue('soportes3_ej'));
      $this->setSessionValue('session_liquidacion.soportes4_ej', $form_state->getValue('soportes4_ej'));
  }
  /**
   * Builds the second step form (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function fapiExamplePageTwo(array &$form, FormStateInterface $form_state) {

    $form['description_ej'] = [
      '#type' => 'item',
      '#title' => $this->t('Detalles del Evento'),
		 '#default_value' => $this->session->get('session_liquidacion.description_ej',''),
    ];


    $form['barrio_ej'] = [
      '#type' => 'textfield',
      '#title' => 'Barrio',
      '#required' => TRUE,
      '#description' => 'Ingresar el barrio de Cartagena de Indias de  donde se realizará el Evento.',
	  '#default_value' => $this->session->get('session_liquidacion.barrio_ej',''),
    ];


      $form['s1'] = [
        '#type' => 'markup',
        '#markup' => '<hr>',
      ];


        $form['valor_evento_ej'] = array(
      '#type' => 'number',
    '#title' => $this->t('Valor Costo del Proyecto'),
      '#description' => 'Ingresar el costo del evento a evaluar sin espacios, si puntos ni comas. ',
      '#width' => '30%',
      '#align' => 'center',
      '#required' => true,
      '#maxlength' =>10,
			'#default_value' => $this->session->get('session_liquidacion.valor_evento_ej',''),
  );

    $form['fecha_Inicial_ej'] = array (
  '#type' => 'date',
  '#title' => 'Fecha Inicial',
  //'#default_value' => date('Y-m-d'),
  '#default_value' => '',
  //'#description' => date('d-m-Y', time()),
  '#description' => 'Seleccionar el día de inicio del evento ',
  '#required' => TRUE,
        '#required' => TRUE,
     '#attributes' => [
              'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),

             // 'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),
          ],
  );
  $form['fecha_Final_ej'] = array (
    '#type' => 'date',
   '#title' => 'Ingresar Fecha de finalización del Evento',
    //'#default_value' => date('Y-m-d'),
    '#default_value' => '',
    //'#description' => date('d-m-Y', time()),
    '#description' => 'Seleccionar el día de finalizacion del evento ',
    '#required' => TRUE,
     '#attributes' => [
              'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
              //'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),


          ],
    );

    $form['numero_dias_ej'] = array(
      '#type' => 'number',
      '#title' => 'Ingresar duración del evento en días',
      '#width' => '30%',
      '#align' => 'center',
      '#required' => true,
      '#maxlength' => 3,
		'#default_value' => $this->session->get('session_liquidacion.numero_dias_ej',''),
  );




      $form['descripcion_evento_ej'] = [
        '#type' => 'textfield',
        '#title' => 'Breve Descripción del Evento',
       '#required' => TRUE,
		  '#default_value' => $this->session->get('session_liquidacion.descripcion_evento_ej',''),
      ];
      $form['direccion_evento_ej'] = [
        '#type' => 'textfield',
        '#title' => 'Dirección donde se realizará el Evento',
      '#required' => TRUE,
		    '#default_value' => $this->session->get('session_liquidacion.direccion_evento_ej',''),
      ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::fapiExamplePageTwoBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next2'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Siguiente'),
      // Custom submission handler for page 1.
      '#submit' => ['::fapiExampleMultistepFormNextSubmit2'],
      // Custom validation handler for page 1.
      '#validate' => ['::fapiExampleMultistepFormNextValidate'],
    ];
   /* $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
    ];*/

    return $form;
  }
  /**
   * Builds the 2nd step form (page 3).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function fapiExamplePageThree(array &$form, FormStateInterface $form_state) {

   // $form_state->set('page_num', 3);
    $form['description3'] = [
      '#type' => 'item',
      '#title' => $this->t('<div>Documentos Requeridos:'),
    ];


    $form['s3'] = [
      '#type' => 'markup',
      '#markup' => '
      Cargar los documentos en formato PDF con un tamaño de cada archivo inferior a : 2MB.
      </p>',
    ];




  $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr>',
    ];

	    $validators = array(
      'file_validate_extensions' => array('pdf'),
    );

    $form['soportes1_ej'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	    '#required' => TRUE,
      '#title' => t('Documento Identidad'),
      '#size' => 20,
      '#description' => 'Documento de Identidad (Cédula Ciudadanía, Cédula de Extranjería, Pasaporte). Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes1_ej',''),
    );

    $form['soportes2_ej'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	   	'#required' => TRUE,
      '#title' => t('RUT'),
      '#size' => 20,
      '#description' => 'Registro Único Tributario - RUT : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes2_ej',''),
    );

    $form['soportes4_ej'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
     '#required' => TRUE,
      '#title' => t('Representación Legal'),
      '#size' => 20,
      '#description' => 'Certificado de existencia y representación legal: Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes4_ej',''),
    );


    $form['soportes3_ej'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	  	'#required' => TRUE,
      '#title' => t('Evidencia Costo del Proyecto'),
      '#size' => 20,
      '#description' => 'Certificación , Cotización o Factura del costo en el evento a evaluar : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes3_ej',''),
    );


    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::fapiExamplePageThreeBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next3'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Siguiente'),
      // Custom submission handler for page 1.
      '#submit' => ['::fapiExampleMultistepFormNextSubmit3'],
      // Custom validation handler for page 1.
      //'#validate' => ['::fapiExampleMultistepFormNextValidate'],
    ];

    return $form;
  }
  /**
   * Builds the 2nd step form (page 3).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function fapiExamplePageFour(array &$form, FormStateInterface $form_state) {

    // $form_state->set('page_num', 4);
     $form['description4'] = [
       '#type' => 'item',
       '#title' => $this->t('<h2>Validar la Información antes de enviar.</h2>'),
     ];
     $id= $this->session->get('session_liquidacion.id_document_ej', '');
     $email=$this->session->get('session_liquidacion.email_ej', '');
     $Cantidad=$this->session->get('session_liquidacion.valor_evento_ej', '');
     $Cantidad = number_format($Cantidad, 2, ',', '.');
     $tarifa =  $this->session->get('session_liquidacion.valor_tarifa_ej', '');
     $total_liquidacion = $this->session->get('session_liquidacion.valorLiquidacion_ej', '');


     $alert=$this->session->get('session_liquidacion.alert_ej', '');
     $form['s0010'] = [
      '#type' => 'markup',
      '#markup' =>   '
    <div class="row">
    <div class="col">Correo: <br>'.$email.'</div>
    <div class="col">No Identificación: <br>'.$id.'</div>
    <div class="col">Valor Inversión:<br> '.$Cantidad.'</div>
    <div class="col">Valor Tarifa: <br>' . $tarifa . '</div>
    <div class="col">Valor Liquidación:<br> ' . $total_liquidacion . '</div>
  </div>

    </br><div>'.$alert .'</div>',
     ];



    $form['accept'] = array(
      '#type' => 'checkbox',
		 '#required' => TRUE,
      '#title' => $this
        ->t('Yo, Acepto terminos y condiciones del uso de mis datos personales.'),
      '#description' => $this->t('<a href="http://www.epacartagena.gov.co/wp-content/uploads/2019/05/aviso%20privacidad%20-%20EPA.pdf" target="_blank">Política de tratamiento de datos personales</a>'),
    );

     $form['my_captcha_element'] = array(
      '#type' => 'captcha',
      '#captcha_type' => 'recaptcha/reCAPTCHA',
     );
     $form['back'] = [
       '#type' => 'submit',
       '#value' => $this->t('Back'),
       // Custom submission handler for 'Back' button.
       '#submit' => ['::fapiExamplePageFourBack'],
       // We won't bother validating the required 'color' field, since they
       // have to come back to this page to submit anyway.
       '#limit_validation_errors' => [],
     ];




        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Liquidar'),
          ];

    /* $form['submit'] = [
       '#type' => 'submit',
       '#button_type' => 'primary',
       '#value' => $this->t('Submit'),
     ];*/

     return $form;
   }

  /**
   * Provides custom submission handler for 'Back' button (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExamplePageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values2'))
      ->set('page_num', 1)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }

  public function fapiExamplePageThreeBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the 2nd step.
      ->setValues($form_state->get('page_values3'))
      ->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }
  public function fapiExamplePageFourBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the 2nd step.
      ->setValues($form_state->get('page_values4'))
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }




 /**
   * Remove all the session information.
   */
  public function submitClearSession(array &$form, FormStateInterface $form_state) {
    $items = [
      'session_liquidacion.name_ej',
    ];
    foreach ($items as $item) {
      $this->session->remove($item);
    }
    $this->messenger()->addMessage($this->t('Session is cleared.'));
    // Since we might have changed the session information, we will invalidate
    // the cache tag for this session.
    $this->invalidateCacheTag();
  }

  /**
   * Invalidate the cache tag for this session.
   *
   * The form will use this method to invalidate the cache tag when the user
   * updates their information in the submit handlers.
   */
  protected function invalidateCacheTag() {
    $this->cacheTagInvalidator->invalidateTags(['session_example:' . $this->session->getId()]);
  }





}