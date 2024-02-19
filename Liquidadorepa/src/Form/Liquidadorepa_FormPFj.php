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


/**
 * Implementando un liquidador para el EPA Cartagena.
 */
/**
 * Generando un formulario de 4 pasos.
 *
 * @see \Drupal\Core\Form\FormBase
 */


class Liquidadorepa_FormPFj extends FormBase
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
    return 'liquidadorepa_formpfj';
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

            $form['id_document_pfj'] = [
              '#type' => 'textfield',
              '#required' => TRUE,
              '#title' => 'NIT',
              '#default_value' => $this->session->get('session_liquidacion.id_document_pfj',''),
              ];

              $form['name_pfj'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'Razón Social ',
                '#description' => 'Razón Social de la entidad.',
                '#default_value' => $this->session->get('session_liquidacion.name_pfj', ''),
               ];
               $form['nombrelegal_pfj'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'Nombre de Representante legal ',
                '#description' => 'Razón Social de la entidad.',
                '#default_value' => $this->session->get('session_liquidacion.nombrelegal_pfj', ''),

               ];
               $form['idlegal_pfj'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'N&#176; de Documento de Representante legal ',
                '#description' => 'Documento del Representante legal.',
                '#default_value' => $this->session->get('session_liquidacion.idlegal_pfj', ''),
               ];
               $form['dir_correspondencia_pfj'] = [
                '#type' => 'textfield',
                '#required' => TRUE,
                '#title' => 'Dirección de Correspondencia del Solicitante',
                '#default_value' => $this->session->get('session_liquidacion.dir_correspondencia_pfj', ''),
               ];
               $form['email_pfj'] = [
                '#type' => 'email',
               '#required' => TRUE,
                '#title' => 'Correo Electrónico',
                '#default_value' => $this->session->get('session_liquidacion.email_pfj', ''),
               ];


               $form['tfijo_pfj'] = [
                '#type' => 'textfield',
                '#title' => 'Teléfono fijo',
                '#default_value' => $this->session->get('session_liquidacion.tfijo_pfj', ''),
                ];


                $form['tmovil_pfj'] = [
                  '#type' => 'textfield',
                  '#required' => TRUE,
                  '#title' => 'Teléfono móvil',
                  '#default_value' => $this->session->get('session_liquidacion.tmovil_pfj', ''),
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
    $dir_vallas =  $this->session->get('session_liquidacion.direcciones_valla_pfj', '');

   // $dir_vallas2 = explode(',',$dir_vallas);





    $codigo_liquidacion = $this->session->get('session_liquidacion.id_document_pfj', '');
    $barrio_liquidacion = $this->session->get('session_liquidacion.barrio_pfj', '');

    $direccion_predio_liquidacion = $this->session->get('session_liquidacion.dir_predio_pfj', '');

    $tipo_solicitante = "Persona Jurídica";
    $id_contribuyente = $this->session->get('session_liquidacion.id_document_pfj', '');
    $name_contrib= $this->session->get('session_liquidacion.name_pfj', '');
    $valor_evento = $this->session->get('session_liquidacion.valor_evento_pfj', '');
    $descripcion_evento = $this->session->get('session_liquidacion.descripcion_evento_pfj', '');
    $direccion_evento = $this->session->get('session_liquidacion.direccion_evento_pfj', '');
    $numero_dias =  $this->session->get('session_liquidacion.numero_dias_pfj', '');

	  $numero_vallas =  $this->session->get('session_liquidacion.cantidad_v_pfj', '');

    $valor_evento = number_format($valor_evento, 2, ',', '.');

    //$valor = $valor_liquidacion * $numero_vallas ;
    $valor_liquidacion =  $this->session->get('session_liquidacion.valorLiquidacion_pfj', '');
    $valor_tarifa = $this->session->get('session_liquidacion.tarifa_pfj', '');


    $dir_correspondecia_contrib = $this->session->get('session_liquidacion.dir_correspondencia_pfj', '');

$email_cotrib =  $this->session->get('session_liquidacion.email_pfj', '');
$tfijo =  $this->session->get('session_liquidacion.tfijo_pfj', '');
$tmovil = $this->session->get('session_liquidacion.tmovil_pfj', '');
$tfijo =  $this->session->get('session_liquidacion.tfijo_pfj', '');
$estrato =  $this->session->get('session_liquidacion.estrato_pfj', '');
//$condicion =  $this->session->get('session_liquidacion.condicion_pfj', '');

$file1 = $this->session->get('session_liquidacion.soportes1_pfj');
$file2 = $this->session->get('session_liquidacion.soportes2_pfj');
$file3 = $this->session->get('session_liquidacion.soportes3_pfj');
$file4 = $this->session->get('session_liquidacion.soportes4_pfj');
/*
	$dir_vallas1 = $this->session->get('session_liquidacion.direccion_valla1_pfj');
    $dir_vallas2 = $this->session->get('session_liquidacion.direccion_valla2_pfj');
    $dir_vallas3 = $this->session->get('session_liquidacion.direccion_valla3_pfj');
    $dir_vallas4 = $this->session->get('session_liquidacion.direccion_valla4_pfj');
    $dir_vallas5 = $this->session->get('session_liquidacion.direccion_valla5_pfj');
    $dir_vallas6 = $this->session->get('session_liquidacion.direccion_valla6_pfj');
    $dir_vallas7 = $this->session->get('session_liquidacion.direccion_valla7_pfj');
    $dir_vallas8 = $this->session->get('session_liquidacion.direccion_valla8_pfj');
    $dir_vallas9 = $this->session->get('session_liquidacion.direccion_valla9_pfj');
    $dir_vallas10 = $this->session->get('session_liquidacion.direccion_valla10_pfj');
*/


    $file1 = $this->session->get('session_liquidacion.soportes1_pfj');
    $file2 = $this->session->get('session_liquidacion.soportes2_pfj');
    $file3 = $this->session->get('session_liquidacion.soportes3_pfj');

  $cantidad_v =  $this->session->get('session_liquidacion.cantidad_v_pfj', '');

 /* if (!empty($dir_vallas1)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas1 ;
		   $cantidad_v =  $cantidad_v+1;
        }

        if (!empty($dir_vallas2)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas2 ;
			  $cantidad_v =  $cantidad_v + 1;
        }
         if (!empty($dir_vallas3)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas3 ;
			  $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas4)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas4 ;
			    $cantidad_v = $cantidad_v + 1;
        }
           if (!empty($dir_vallas5)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas5 ;
			     $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas6)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas6 ;
			    $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas7)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas7 ;
			    $cantidad_v =  $cantidad_v + 1;
        }    if (!empty($dir_vallas8)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas8 ;
			   $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas9)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas9 ;
			    $cantidad_v =  $cantidad_v+ 1;
        }
           if (!empty($dir_vallas10)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas10 ;
			   $cantidad_v=  $cantidad_v + 1;
        }

*/
       /*

Creando un nodo tipo factura con los datos recibidos

**/
$my_article = Node::create(['type' => 'liquidacion']);
$my_article->set('title', $codigo_liquidacion);
$my_article->set('field_valor', $valor_liquidacion);
$my_article->set('field_barrio_liquidacion', $barrio_liquidacion);
$my_article->set('field_concepto_ambiental_liq', "Fija");
$my_article->set('field_direccion_correspondencia', $dir_correspondecia_contrib);
$my_article->set('field_direccion_del_predio', $direccion_evento);


$my_article->set('field_valor_evento',$valor_evento);
//$my_article->set('field_valor_letras', $valor_letras);
$my_article->set('field_descripcion_evento', $descripcion_evento );
$my_article->set('field_tipo_de_solicitante', $tipo_solicitante);
$my_article->set('field_id_contribuyente', $id_contribuyente);
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
$my_article->set('uid', $id_contribuyente);
         $my_article->enforceIsNew();
           $my_article->save();
 $nid = $my_article->id();
       $node = \Drupal\node\Entity\Node::load($nid); //Accedemos a la información del nodo
	    /** Obteniendo el field_consecutivo_factura del nodo creado */
           $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
	       $sec ="02"."0".$consecutivo_facturas[0]["value"].date('Y');
	 // $node->set('field_codigo_liquidacion_factura', $sec); //valor liquidacion
$node->set('title', $sec); //Accedemos al campo que queremos editar/modificar

         $html= '
		  <style>
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
             <td rowspan="6">EPA | Zona Liquidaciones</td>
           </tr>
           <tr>
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
           <td ><p>FECHA:</p></td>
           <td  colspan="3">
           <p>'.date("Y/m/d").'</p>
           </td>
         </tr>
         <tr>
         <td colspan="4">
         <p>Liquidación No '.$sec.'</p>
         </td>
       </tr>
       <tr>
       <td ><p>ASUNTO:</p></td>
       <td  colspan="3">
       <p>Viabilidad Publicidad Fija</p>
       </td>
     </tr>
     <tr>
     <td ><p>PETICIONARIO / EMPRESA:</p></td>
     <td  colspan="3">
     <p>'.$name_contrib.'</p>
     </td>
   </tr>
   <tr>
   <td ><p>Id Solicitante:</p></td>
<td  colspan="3">
<p>'.$id_contribuyente.'</p>
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
             <td><p>VALOR INVERSIÓN</p></td>
             <td>
             <p> $ '.$valor_evento.'</p>
             </td>
             <td>TOTAL LIQUIDACIÓN</td>
             <td >
             <p style="
    font-weight: bold;
" >$ '.$valor_liquidacion.'</p>
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
             <p class="concepto">LIQUIDACION POR CONCEPTO DE  VIABILIDAD AMBIENTAL  PARA LA PUBLICIDAD EXTERIOR VISUAL FIJA PARA '.$numero_vallas.' VALLAS, CON UN COSTO DE REALIZACIÓN DE INVERSIÓN DE IMPLEMENTACION DE PROYECTO DE  : '.$valor_evento.' PARA LAS DIRECCIONES :'.$dir_vallas.', SEGÚN SOLICITUD #'.$sec.'</p>
             </div>
             </td>
           </tr>

         </tbody>
       </table>
       ';
	  $node->set('body',$html);
       $node->body->format = 'full_html';
	  $node->save(); //Guarda el cambio una vez realizado

    $valor = $this->session->get('session_liquidacion.valorLiquidacion_pdf','');
    $code="4157709998461239"."8020".$sec."3900".$valor."96".date('Y')."1231";
    $code_content="(415)7709998461239"."(8020)".$sec."(3900)".$valor."(96)".date('Y')."1231";





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
 /*   $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);

    if ($result['result'] == TRUE) {
		  $f11= strtotime($form_state->getValue('fecha_Inicial'));
	  $dt=\Drupal::time()->getCurrentTime();
	 $diff2 =($f11-$dt)/86400;
     if ($diff2 < 10){
    $this->messenger()->addStatus($this->t('Su fecha es menor a 10 días , el tramite se realizará con riesgo de no ser aceptado', ['@title' =>$N]));
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


/*
	   $dir_vallas1 = $this->session->get('session_liquidacion.direccion_valla1_pfj');
    $dir_vallas2 = $this->session->get('session_liquidacion.direccion_valla2_pfj');
    $dir_vallas3 = $this->session->get('session_liquidacion.direccion_valla3_pfj');
    $dir_vallas4 =$this->session->get('session_liquidacion.direccion_valla4_pfj');
    $dir_vallas5 = $this->session->get('session_liquidacion.direccion_valla5_pfj');
    $dir_vallas6 = $this->session->get('session_liquidacion.direccion_valla6_pfj');
    $dir_vallas7 = $this->session->get('session_liquidacion.direccion_valla7_pfj');
    $dir_vallas8 = $this->session->get('session_liquidacion.direccion_valla8_pfj');
    $dir_vallas9 = $this->session->get('session_liquidacion.direccion_valla9_pfj');
    $dir_vallas10 = $this->session->get('session_liquidacion.direccion_valla10_pfj');

*/

    $file1 = $this->session->get('session_liquidacion.soportes1_pfj');
    $file2 = $this->session->get('session_liquidacion.soportes2_pfj');
    $file3 = $this->session->get('session_liquidacion.soportes3_pfj');
     $cantidad_v = intval($this->session->get('session_liquidacion.cantidad_v_pfj'));

  /*
      if (!empty($dir_vallas1)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas1 ;
		   $cantidad_v =  $cantidad_v + 1;
        }

        if (!empty($dir_vallas2)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas2 ;
			  $cantidad_v =  $cantidad_v + 1;
        }
         if (!empty($dir_vallas3)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas3 ;
			  $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas4)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas4 ;
			    $cantidad_v = $cantidad_v + 1;
        }
           if (!empty($dir_vallas5)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas5 ;
			     $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas6)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas6 ;
			    $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas7)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas7 ;
			    $cantidad_v =  $cantidad_v + 1;
        }    if (!empty($dir_vallas8)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas8 ;
			   $cantidad_v =  $cantidad_v + 1;
        }
           if (!empty($dir_vallas9)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas9 ;
			    $cantidad_v =  $cantidad_v+ 1;
        }
           if (!empty($dir_vallas10)) {
            $dir_vallas = $dir_vallas."/".$dir_vallas10 ;
			   $cantidad_v=  $cantidad_v + 1;
        }
*/

	   $vocabulary_name = 'smlv';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);


    foreach ($terms as $term) {
      $id2 = $term->getFields();
          $value  = $term->get('field_smlv')->getValue();

    }
    $valor2 = $value[0]["value"];
    $valor = number_format($valor2, 2, ',', '.');

    $valor_tarifa_evento_25 = $valor2 * 25 ;
    $valor_tarifa_evento_35 = $valor2 * 35 ;
    $valor_tarifa_evento_50 = $valor2 * 50 ;
    $valor_tarifa_evento_70 = $valor2 * 70 ;
    $valor_tarifa_evento_100 = $valor2 * 100 ;
    $valor_tarifa_evento_200 = $valor2 * 200 ;
    $valor_tarifa_evento_300 = $valor2 * 300 ;
    $valor_tarifa_evento_400 = $valor2 * 400 ;
    $valor_tarifa_evento_500 = $valor2 * 500 ;
    $valor_tarifa_evento_700 = $valor2 * 700 ;
    $valor_tarifa_evento_900 = $valor2 * 900 ;
    $valor_tarifa_evento_1500 = $valor2 * 1500 ;
    $valor_tarifa_evento_2115 = $valor2 * 2115 ;
    $valor_tarifa_evento_8458 = $valor2 * 8458 ;

	  //$valor_liquidacion recibe el valor de la tarifa multiplicada por los dias

	//$valor_liquidacion =  $this->session->get('session_liquidacion.valor_evento_pfj', '');
	   $numero_dias = $this->session->get('session_liquidacion.numero_dias_pfj');

	  $valor_liquidacion =   $this->session->get('session_liquidacion.valor_evento_pfj');



      $this->setSessionValue('session_liquidacion.error',  $valor_liquidacion);

if ($valor_liquidacion < $valor_tarifa_evento_25) {
        $valor_tarifa = 163732;//ok
        $valor_liquidacion = 163732 *  $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r = 118600 * $numero_dias * $cantidad_v  ;


      } elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
          $valor_tarifa = 209559;//ok
        $valor_liquidacion = 229488 *  $numero_dias * $cantidad_v ;
        $valor_liquidacion_r = 166200 *  $numero_dias *$cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_35  && $valor_liquidacion < $valor_tarifa_evento_50) {
          $valor_tarifa =328121;
        $valor_liquidacion =328121  *  $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r = 237600 *   $numero_dias * $cantidad_v  ;

      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_50  && $valor_liquidacion < $valor_tarifa_evento_70 ) {
          $valor_tarifa =459633;
        $valor_liquidacion = 459633  *   $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r =  332850 *  $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_70  && $valor_liquidacion < $valor_tarifa_evento_100) {
          $valor_tarifa =  656900;
        $valor_liquidacion = 656900  *   $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r =  475700  *   $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_100  && $valor_liquidacion < $valor_tarifa_evento_200) {
          $valor_tarifa =  1314458;
        $valor_liquidacion = 1314458 *  $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r =  951800  *   $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_200  && $valor_liquidacion < $valor_tarifa_evento_300) {
          $valor_tarifa = 1972015;
       $valor_liquidacion = 1972015 *   $numero_dias * $cantidad_v  ;
       $valor_liquidacion_r =  1428000  *  $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_300  && $valor_liquidacion < $valor_tarifa_evento_400) {
          $valor_tarifa = 2629573;
       $valor_liquidacion =  2629573  *   $numero_dias * $cantidad ;
       $valor_liquidacion_r =  1904150  *   $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_400  && $valor_liquidacion < $valor_tarifa_evento_500) {
          $valor_tarifa = 3287130;
        $valor_liquidacion =  3287130  *   $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r = 2380300  *  $numero_dias * $cantidad_v ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_500  && $valor_liquidacion < $valor_tarifa_evento_700) {
          $valor_tarifa =4202580;
       $valor_liquidacion = 4202580*  $numero_dias * $cantidad_v  ;
       $valor_liquidacion_r = 3332600 *  $numero_dias *$cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_700  && $valor_liquidacion < $valor_tarifa_evento_900) {
          $valor_tarifa = 5403489;
        $valor_liquidacion = 5403489  *   $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r = 4284900 *   $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_900  && $valor_liquidacion < $valor_tarifa_evento_1500) {
          $valor_tarifa =90062154;
       $valor_liquidacion = 90062154  *  $numero_dias * $cantidad_v  ;
       $valor_liquidacion_r = 90062150 *  $numero_dias * $cantidad_v ;
      }elseif ($valor_liquidacion >= $valor_tarifa_evento_1500  && $valor_liquidacion < $valor_tarifa_evento_2115) {
        $valor_tarifa =12699009;
        $valor_liquidacion = 12699009 *   $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r = 10917550 *   $numero_dias * $cantidad_v  ;
      }elseif ($valor_liquidacion  >= $valor_tarifa_evento_2115  && $valor_liquidacion < $valor_tarifa_evento_8458) {
        $valor_tarifa =12699009;
        $valor_liquidacion =37374939 *   $numero_dias * $cantidad_v  ;
        $valor_liquidacion_r =37374939 *   $numero_dias * $cantidad_v  ;

    }else {
        /*$valor_tarifa =($valor_evento * 0.4)/100;
        $valor_liquidacion = ( ($valor_evento * 0.4)/100) ;*/
      $valor_tarifa =208879615;
       /* $valor_liquidacion =37374939 *  $numero_dias;
        $valor_liquidacion_r =37374939 *  $numero_dias;*/
      }

      $this->setSessionValue('session_liquidacion.valorLiquidacion_pdf',$valor_liquidacion);
	  $valor_tarifa = number_format($valor_tarifa, 2, ',', '.');
	  $valor_liquidacion = number_format( $valor_liquidacion, 2, ',', '.');

	  $this->setSessionValue('session_liquidacion.tarifa_pfj', $valor_tarifa);
	  $this->setSessionValue('session_liquidacion.valorLiquidacion_pfj',$valor_liquidacion);



    $tipo = $form_state->getValue('field_select_renovacion_pfj');
    if ($tipo == 'Renovación'){


      $dt=\Drupal::time()->getCurrentTime();


    }else{

    $f110= strtotime($form_state->getValue('session_liquidacion.fecha_Inicial_pfj'));
	  $dt=\Drupal::time()->getCurrentTime();
	  $diff02 =($f110-$dt)/86400;

     if ($diff02 < 10){
     $alert='<div class="alertaproximidad">Tenga en cuententa la fecha de su envento antes de liquidar. Su Solicitud tiene un tiempo de respuesta de 15 dias hábiles Contados a partir de la fecha en la que sea adjuntado el soporte de pago y la documentacion requerida en el formulario, De conformidad con la ley 1437 del 2011</div>';
      /*$this->messenger()->addStatus($this->t('Su fecha es menor a 10 días , el tramite se realizará con riesgo de no ser aceptado', ['@title' =>$N]));*/
      $this->setSessionValue('session_liquidacion.alert', $alert);
}else{
  $alert='';
  $this->setSessionValue('session_liquidacion.alert', $alert);
}

	 $cantidad_meses=  $form_state->getValue('numero_dias_pfj') * 86400 ;
   $f1= strtotime($form_state->getValue('fecha_Inicial_pfj'));
   $f_limit=strtotime($form_state->getValue('fecha_Final_pfj'));
  $diff =($f_limit-$f1);

    if ($f1 > $f_limit){
   $form_state->setErrorByName('fecha_Inicial_pfj', $this->t('La fecha inicial no puede ser menor a la final '));
}
       if ($diff < 2592000){
   $form_state->setErrorByName('fecha_Final_pfj', $this->t('La fecha final no puede ser menor a 1 mes de 30 días '));
}

$limite=strtotime("2023-12-31");
if ($f_limit> $limite){
  /*$form_state->setErrorByName('fecha_Final_pfj', $this->t('La fecha inicial no puede ser superior al 31 de diciembre del presente año '));*/
}

  } }
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
            'name' => $form_state->getValue('name'),

             ])

             ->set('page_num', 2)

             ->setRebuild(TRUE);
                 $this->setSessionValue('session_liquidacion.id_document_pfj', $form_state->getValue('id_document_pfj'));
                   $this->setSessionValue('session_liquidacion.name_pfj', $form_state->getValue('name_pfj'));
			       $this->setSessionValue('session_liquidacion.nombrelegal_pfj', $form_state->getValue('nombrelegal_pfj'));
                   $this->setSessionValue('session_liquidacion.idlegal_pfj', $form_state->getValue('idlegal_pfj'));
                   $this->setSessionValue('session_liquidacion.dir_correspondencia_pfj', $form_state->getValue('dir_correspondencia_pfj'));
                   $this->setSessionValue('session_liquidacion.email_pfj', $form_state->getValue('email_pfj'));
                   //$this->setSessionValue('session_liquidacion.estrato_afj', $form_state->getValue('first_estrato'));
                   $this->setSessionValue('session_liquidacion.tmovil_pfj', $form_state->getValue('tmovil_pfj'));
                   $this->setSessionValue('session_liquidacion.tfijo_pfj', $form_state->getValue('tfijo_pfj'));
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
        'valor_evento_pfj' => $form_state->getValue('valor_evento_pfj'),
        'field_select_V_pfj' => $form_state->getValue('field_select_V_pfj'),
      ])
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
        $this->setSessionValue('session_liquidacion.barrio_pfj', $form_state->getValue('barrio_pfj'));
        $this->setSessionValue('session_liquidacion.field_select_renovacion_pfj', $form_state->getValue('field_select_renovacion_pfj'));
        $this->setSessionValue('session_liquidacion.valor_evento_pfj', $form_state->getValue('valor_evento_pfj'));
        $this->setSessionValue('session_liquidacion.fecha_Inicial_pfj', $form_state->getValue('fecha_Inicial_pfj'));
        $this->setSessionValue('session_liquidacion.fecha_Final_pfj', $form_state->getValue('fecha_Final_pfj'));

        $this->setSessionValue('session_liquidacion.numero_dias_pfj', $form_state->getValue('numero_dias_pfj'));

        $this->setSessionValue('session_liquidacion.cantidad_v_pfj', $form_state->getValue('cantidad_v_pfj'));



        $this->setSessionValue('session_liquidacion.direcciones_valla_pfj', $form_state->getValue('direcciones_valla_pfj'));
/*
        $this->setSessionValue('session_liquidacion.direccion_valla1_pfj', $form_state->getValue('direccion_valla1_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla2_pfj', $form_state->getValue('direccion_valla2_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla3_pfj', $form_state->getValue('direccion_valla3_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla4_pfj', $form_state->getValue('direccion_valla4_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla5_pfj', $form_state->getValue('direccion_valla5_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla6_pfj', $form_state->getValue('direccion_valla6_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla7_pfj', $form_state->getValue('direccion_valla8_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla9_pfj', $form_state->getValue('direccion_valla9_pfj'));
        $this->setSessionValue('session_liquidacion.direccion_valla10_pfj', $form_state->getValue('direccion_valla10_pfj'));
	    $this->setSessionValue('session_liquidacion.descripcion_valla1_pfj', $form_state->getValue('descripcion_valla1_pfj'));
	    $this->setSessionValue('session_liquidacion.descripcion_valla2_pfj', $form_state->getValue('descripcion_valla2_pfj'));
	      $this->setSessionValue('session_liquidacion.descripcion_valla3_pfj', $form_state->getValue('descripcion_valla3_pfj'));
	      $this->setSessionValue('session_liquidacion.descripcion_valla4_pfj', $form_state->getValue('descripcion_valla4_pfj'));
	    $this->setSessionValue('session_liquidacion.descripcion_valla5_pfj', $form_state->getValue('descripcion_valla5_pfj'));
	      $this->setSessionValue('session_liquidacion.descripcion_valla6_pfj', $form_state->getValue('descripcion_valla6_pfj'));
	        $this->setSessionValue('session_liquidacion.descripcion_valla7_pfj', $form_state->getValue('descripcion_valla7_pfj'));
	      $this->setSessionValue('session_liquidacion.descripcion_valla8_pfj', $form_state->getValue('descripcion_valla8_pfj'));
	    $this->setSessionValue('session_liquidacion.descripcion_valla9_pfj', $form_state->getValue('descripcion_valla9_pfj'));
	      $this->setSessionValue('session_liquidacion.descripcion_valla10_pfj', $form_state->getValue('descripcion_valla10_pfj'));

*/
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

        'soportes1' => $form_state->getValue('soportes1_pfj'),

      ])
      ->set('page_num', 4)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.soportes1_pfj', $form_state->getValue('soportes1_pfj'));

      $this->setSessionValue('session_liquidacion.soportes2_pfj', $form_state->getValue('soportes2_pfj'));
      $this->setSessionValue('session_liquidacion.soportes3_pfj', $form_state->getValue('soportes3_pfj'));
      $this->setSessionValue('session_liquidacion.soportes4_pfj', $form_state->getValue('soportes4_pfj'));

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

    $form['description_pfj'] = [
      '#type' => 'item',
      '#title' => $this->t('Ubicación e Inversión'),
    ];
    $form['field_select_renovacion_pfj'] = array(
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Tipo de Solicitud'),
      '#default_value' => $this->session->get('session_liquidacion.field_select_renovacion_pfj', ''),
         '#options' => array( t('Primera Vez'), t('Renovación'), ),
       '#attributes' => [

            'name' => 'field_select_renovacion_pfj',
          ],
      );

    $form['barrio_pfj'] = [
      '#type' => 'textfield',
      '#title' => 'Barrio',
       '#required' => TRUE,
      '#description' => 'Ingresar el barrio de Cartagena de Indias de  donde se autorizará la publicidad',
      '#default_value' => $this->session->get('session_liquidacion.barrio_pfj', ''),
        ];


  $form['s1'] = [
    '#type' => 'markup',
    '#markup' => '<hr>',
  ];
$form['valor_evento_pfj'] = array(
  '#type' => 'number',
  '#title' => 'Valor Total de la Inversión de la Publicidad Fija',
  '#width' => '30%',
  '#align' => 'center',
  '#default_value' => $this->session->get('session_liquidacion.valor_evento_pfj', ''),
  '#required' => true,
  '#maxlength' =>10
);

$form['fecha_Inicial_pfj'] = array (
'#type' => 'date',
'#title' => 'Fecha Inicial',
//'#default_value' => date('Y-m-d'),
'#default_value' => '',
//'#description' => date('d-m-Y', time()),
'#description' => 'Seleccionar el día de inicio de la publicidad ',
'#required' => TRUE,
    '#required' => TRUE,
 '#attributes' => [
          'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
         // 'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),
      ],
);
$form['fecha_Final_pfj'] = array (
'#type' => 'date',
'#title' => 'Ingresar Fecha de finalización de la publicidad',
//'#default_value' => date('Y-m-d'),
'#default_value' => '',
//'#description' => date('d-m-Y', time()),
'#description' => 'Seleccionar el día de finalización de la publicidad ',
'#required' => TRUE,
 '#attributes' => [
          'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
         /* 'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),*/
      ],
);

$form['numero_dias_pfj'] = array(
  '#type' => 'number',
  '#title' => 'Ingresar duración de la publicidad en meses',
  '#width' => '30%',
  '#align' => 'center',
  '#default_value' => $this->session->get('session_liquidacion.numero_dias_pfj', ''),
  '#required' => true,
 //corregir maximo 12 minimo 1
  '#maxlength' => 2
);


/*
$form['field_select_NV_pfj'] = array(
'#type' => 'select',
'#required' => TRUE,
'#title' => $this->t('Número de Vallas a Evaluar'),
'#default_value' => $this->session->get('session_liquidacion.field_select_NV_pfj', ''),
   '#options' => array( t('1'), t('2'), t('3'), t('4') , t('5') , t('6'), t('7'), t('8'), t('9'), t('10')),
 '#attributes' => [

      'name' => 'field_select_NV_pfj',
    ],
);*/




$form['cantidad_v_pfj'] = array(
  '#type' => 'number',
  '#title' => 'Cantidad de Vallas a Evaluar',
  '#width' => '30%',
  '#align' => 'center',
  //'#default_value' => $this->session->get('session_liquidacion.numero_meses_pmj', ''),
  '#required' => true,
  '#maxlength' => 3
);
/*
$form['field_select_NV_pfj'] = array(
'#type' => 'select',
'#required' => TRUE,
'#title' => $this->t('Número de Vallas a Evaluar'),
'#default_value' => $this->session->get('session_liquidacion.field_select_NV_pfj', ''),
   '#options' => array( t('1'), t('2'), t('3'), t('4') , t('5') , t('6'), t('7')  , t('8')   , t('9')  , t('10')  ),
 '#attributes' => [

      'name' => 'field_select_NV_pfj',
    ],
);
*/

$form['direcciones_valla_pfj'] = [
  '#type' => 'textarea',
  //'#default_value' => $this->session->get('session_liquidacion.direccion_valla1_pfj', ''),
  '#title' => 'Ingresar la Direcciónes separadas por coma',

];

/*
$form['direccion_valla1_pfj'] = [
'#type' => 'textarea',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla1_pfj', ''),
'#title' => 'Ingresar la Direcciónes separadas por coma',

//'#required' => TRUE,

'#states' =>array(

   'required' => [
 [
  ':input[name="field_select_NV_pfj"]' => ['value' => '0'],
],'or',
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '0'],
],
'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],
'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla1_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla1_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso #1',

//'#required' => TRUE,
'#states' => array(

 'required' => [
 [
  ':input[name="field_select_NV_pfj"]' => ['value' => '0'],
],'or',
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '0'],
],
'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],
'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

$form['direccion_valla2_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla2_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 2',

//'#required' => TRUE,

'#states' => array(
  'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],
'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla2_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla2_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso #2',

//'#required' => TRUE,
'#states' => array(

    'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],



'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '1'],
],
'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

$form['direccion_valla3_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla3_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 3',

//'#required' => TRUE,

'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla3_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla3_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso #3',

//'#required' => TRUE,
'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '2'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];


$form['direccion_valla4_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla4_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 4',

//'#required' => TRUE,

'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [


  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla4_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla4_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso #4',

//'#required' => TRUE,
'#states' =>array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [


  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '3'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];
 $form['direccion_valla5_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla5_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 5',

//'#required' => TRUE,

'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla5_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla5_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso # 5',

//'#required' => TRUE,
'#states' =>array(

   'required' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [


  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '4'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

 $form['direccion_valla6_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla6_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 6',

//'#required' => TRUE,

'#states' => array(

   'required' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla6_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla6_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso # 6',

//'#required' => TRUE,
'#states' => array(

   'required' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '5'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['direccion_valla7_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla7_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 7',

//'#required' => TRUE,

'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla7_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla7_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso # 7',

//'#required' => TRUE,
'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '6'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

$form['direccion_valla8_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla8_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 8',

//'#required' => TRUE,

'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla8_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla8_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso # 8',

//'#required' => TRUE,
'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '7'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];


  $form['direccion_valla9_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla9_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 9',

//'#required' => TRUE,

'#states' =>array(

   'required' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla9_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla9_pfj', ''),
'#title' => 'Breve Descripción de la valla y/o aviso # 9',

//'#required' => TRUE,
'#states' =>array(

   'required' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '8'],
],

'or',

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

$form['direccion_valla10_pfj'] = [
'#type' => 'textfield',
//'#default_value' => $this->session->get('session_liquidacion.direccion_valla10_pfj', ''),
'#title' => 'Ingresar la Dirección para la Valla # 10',

//'#required' => TRUE,

'#states' => array(

   'required' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [

  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],

]
),
];

  $form['descripcion_valla10_pfj'] = [
'#type' => 'textfield',
'#title' => 'Breve Descripción de la valla y/o aviso # 10',
//'#default_value' => $this->session->get('session_liquidacion.descripcion_valla10_pfj', ''),
//'#required' => TRUE,
'#states' => array(

   'required' => [
  [
  ':input[name="field_select_NV_pfj"]' => ['value' => '9'],
],



  ],

'visible' => [  [ ':input[name="field_select_NV_pfj"]' => ['value' => '9'],  ],  ]	),
  ];

*/
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

    $form['soportes1_pfj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Documento Identidad'),
      '#size' => 20,
      '#description' => 'Documento de Identidad (Cédula Ciudadanía, Cédula de Extranjería, Pasaporte). Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes1_pfj', ''),
    );


    $form['soportes2_pfj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('RUT'),
      '#size' => 20,
      '#description' => 'Registro Único Tributario - RUT : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes2_pfj', ''),
    );

    $form['soportes4_pfj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Representación Legal'),
      '#size' => 20,
      '#description' => 'Certificado de existencia y representación legal: Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes4_pfj', ''),
    );
    $form['soportes3_pfj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Evidencia Inversión'),
      '#size' => 20,
      '#description' => 'Certificación , Cotización o Factura del valor invertido en publicidad a evaluar : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes3_pfj', ''),
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
     $Cantidad_vallas =  $this->session->get('session_liquidacion.cantidad_v_pfj', '');
     $id= $this->session->get('session_liquidacion.id_document_pfj', '');
     $email=$this->session->get('session_liquidacion.email_pfj', '');
     $Cantidad=$this->session->get('session_liquidacion.valor_evento_pfj', '');
     $Cantidad= number_format($Cantidad, 2, ',', '.');
     $alert=$this->session->get('session_liquidacion.alert_pfj', '');
     $tarifa =  $this->session->get('session_liquidacion.tarifa_pfj', '');
	 $Valor_liquidacion =  $this->session->get('session_liquidacion.valorLiquidacion_pfj', '');
	 $Valor_liquidacion_test =  $this->session->get('session_liquidacion.direccion_valla9_pfj');

     $form['s0010'] = [
      '#type' => 'markup',
      '#markup' =>   '
    <div class="row">
    <div class="col">Correo:<br> '.$email.'</div>
    <div class="col">No Identificación:<br> '.$id.'</div>
    <div class="col">Valor Inversión : <br>'.$Cantidad.'</div>
    <div class="col">Cantidad de Vallas : <br>'.$Cantidad_vallas.'</div>
	 <div class="col">Valor Tarifa : <br>'.$tarifa.'</div>
	  <div class="col">Valor Liquidación :<br> '.$Valor_liquidacion.'</div>


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
      'session_liquidacion.descripcion_valla1_pfj',
      'session_liquidacion.descripcion_valla2_pfj',
      'session_liquidacion.descripcion_valla3_pfj',
      'session_liquidacion.descripcion_valla4_pfj',
      'session_liquidacion.descripcion_valla5_pfj',
      'session_liquidacion.descripcion_valla6_pfj',
      'session_liquidacion.descripcion_valla7_pfj',
      'session_liquidacion.descripcion_valla8_pfj',
      'session_liquidacion.descripcion_valla9_pfj',
      'session_liquidacion.descripcion_valla10_pfj',
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