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


class Liquidadorepa_FormAF extends FormBase
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
    return 'liquidadorepa_formaf';
  }
  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    //pagina1
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
            '#markup' => '<hr><div><p> Ingresar en esta página la información de titular del tramite ambiental, siendo el titular la persona natural que solicita la viabilidad.</p> </div>',
            ];

           $form['id_document'] = [
            '#type' => 'textfield',
            '#required' => TRUE,
            '#title' => 'Documento de Identidad',
            '#default_value' => $this->session->get('session_liquidacion.id_document_af',''),
            ];

            $form['name'] = [
             '#type' => 'textfield',
             '#size' => '60',
             '#required' => TRUE,
             '#title' => 'Nombre  ',
             '#description' => 'Nombre del Solicitante.',
             '#default_value' => $this->session->get('session_liquidacion.name_af', ''),
            ];

            $form['dir_correspondencia'] = [
             '#type' => 'textfield',
             '#required' => TRUE,
             '#title' => 'Dirección de Correspondencia del Solicitante',
             '#default_value' => $this->session->get('session_liquidacion.dir_correspondencia_af', ''),
            ];
            $form['email'] = [
             '#type' => 'email',
            '#required' => TRUE,
             '#title' => 'Correo Electrónico',
             '#default_value' => $this->session->get('session_liquidacion.email_af', ''),
            ];

            $form['tfijo'] = [
            '#type' => 'textfield',
            '#title' => 'Teléfono fijo',
            '#default_value' => $this->session->get('session_liquidacion.tfijo_af', ''),
            ];

            $form['tmovil'] = [
            '#type' => 'textfield',
            '#required' => TRUE,
            '#title' => 'Teléfono móvil',
            '#default_value' => $this->session->get('session_liquidacion.tmovil_af', ''),
           ];

            $form['estrato'] = array(
             '#title' => t('Estrato'),
             '#default_value' => $this->session->get('session_liquidacion.estrato_af', ''),
             '#type' => 'select',
                '#description' => 'Seleccionar el estrato de quien realiza la liquidación.',
             '#options' => array(t('--- Seleccionar ---'), t('1'), t('2'), t('3'), t('4') , t('5') , t('6')    ),
            );

            $form['condicion'] = array(
               '#title' => t('¿Se encuentra en alguna condición especial?'),
               '#default_value' => $this->session->get('session_liquidacion.condicion_af', ''),
               '#type' => 'select',
               '#options' => array(t('Ninguno'), t('Adulto mayor'), t('Habitante de la calle'), t('Mujer gestante'), t('Peligro inminente') , t('Persona en condición de discapacidad') , t('Víctima del conflicto armado') , t('Menor de edad')     ),
              );
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
               //'#validate' => ['::fapiExampleMultistepFormNextValidate1'],
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
    //sesionvalue
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
        //validateform


		  $cantidad_arboles = $this->session->get('session_liquidacion.cantidad_arboles_af', '');
         $cantidad_arboles_limit=26;

         if ($cantidad_arboles >= $cantidad_arboles_limit){
         $form_state->setErrorByName('cantidad_arboles_af', $this->t('Por disposiciones legales si la solicitud supera 25 árboles, este trámite debe realizarse a través del sistema VITAL del MinAmbiente en : http://vital.minambiente.gov.co'));
         }


       }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
//sumbitform
$valor2=$this->session->get('session_liquidacion.tarifa_af', '');


$valor_liquidacion = $this->session->get('session_liquidacion.valorLiquidacion_af','');

//$valor_liquidacion = number_format($valor_liquidacion, 2, ',', '.');

//Construir factura

$file1 = $this->session->get('session_liquidacion.soportes1_af');
$file2 = $this->session->get('session_liquidacion.soportes2_af');
$file3 = $this->session->get('session_liquidacion.soportes3_af');
$file4 = $this->session->get('session_liquidacion.soportes4_af');

$codigo_liquidacion =$this->session->get('session_liquidacion.id_document_af', '');
$barrio_liquidacion = $this->session->get('session_liquidacion.barrio_af', '');
$direccion_predio_liquidacion = $this->session->get('session_liquidacion.dir_predio_af', '');
$name_predio= $this->session->get('session_liquidacion.name_predio_af', '');
$numero_arboles  = $this->session->get('session_liquidacion.cantidad_arboles_af', '');
$especie_arboles  = $this->session->get('session_liquidacion.especie_af', '');
$tipo_solicitante = "Persona Natural";
$id_contribuyente = $this->session->get('session_liquidacion.id_document_af', '');
$name_contrib= $this->session->get('session_liquidacion.name_af', '');
$dir_correspondecia_contrib = $this->session->get('session_liquidacion.dir_correspondencia_af', '');
$email_cotrib = $this->session->get('session_liquidacion.email_af', '');
$tfijo = $this->session->get('session_liquidacion.tfijo_af', '');
$tmovil =  $this->session->get('session_liquidacion.tmovil_af', '');
$estrato =  $this->session->get('session_liquidacion.estrato_af', '');
$condicion = $this->session->get('session_liquidacion.condicion_af', '');


///Crear tipo de contenido

$Contenido_Liquidacion = Node::create(['type' => 'liquidacion']);
$Contenido_Liquidacion->set('title', $codigo_liquidacion);
$Contenido_Liquidacion->set('field_barrio_liquidacion', $barrio_liquidacion);
$Contenido_Liquidacion->set('field_concepto_ambiental_liq',"Tala");
$Contenido_Liquidacion->set('field_nombre_predio', $name_predio);
$Contenido_Liquidacion->set('field_direccion_correspondencia', $dir_correspondecia_contrib);
$Contenido_Liquidacion->set('field_direccion_del_predio', $direccion_predio_liquidacion);
$Contenido_Liquidacion->set('field_especie', $especie_arboles);
$Contenido_Liquidacion->set('field_numero_de_arboles', 1);
$Contenido_Liquidacion->set('field_tipo_de_solicitante', $tipo_solicitante);
$Contenido_Liquidacion->set('field_id_contribuyente', $id_contribuyente);
$Contenido_Liquidacion->set('field_nombre_contribuyente', $name_contrib);
$Contenido_Liquidacion->set('field_email_contribuyente', $email_cotrib );
$Contenido_Liquidacion->set('field_telefono_fijo_contribuyent', $tfijo);
$Contenido_Liquidacion->set('field_telefono_movil_contribuyen', $tmovil);
$Contenido_Liquidacion->set('field_estrato_contribuyente', $estrato);
$Contenido_Liquidacion->set('field_condicion_contribuyente', $condicion);
$Contenido_Liquidacion->set('field_id_file', $file1);
$Contenido_Liquidacion->set('field_rut_file', $file2);
$Contenido_Liquidacion->set('field_libertad_y_tradicion', $file3);
$Contenido_Liquidacion->set('field_autorizacion', $file4);
$Contenido_Liquidacion->set('field_comparado_factura',false);
$Contenido_Liquidacion->set('field_estado',FALSE);
$Contenido_Liquidacion->set('status', '0');
$Contenido_Liquidacion->enforceIsNew();
$Contenido_Liquidacion->save();


$nid = $Contenido_Liquidacion->id();
$node = \Drupal\node\Entity\Node::load($nid);
$consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
$sec ="04"."0".$consecutivo_facturas[0]["value"].date('Y');
$node->set('title', $sec);


$html= '
<style>
     .test {
background-image:url("https://liquidaciones.epacartagena.gov.co/themes/bootstrap5/logo.svg");
}
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
.field.field--name-field-pago-por-pse.field--type-link.field--label-hidden.field__item.quickedit-field > a {
margin-left: 4%;
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
     <td rowspan="6" classs="test">
  EPA | Zona Liquidaciones

  </td>
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
        <td colspan="3">
        <p> Liquidación No '.$sec.'</p>
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
  <p>EVALUACIÓN DE APROVECHAMIENTO FORESTAL</p>
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
<td ><p>DIRECCIÓN:</p></td>
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
        <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 107 de 17 de febrero de 2021</p></td>
        <td colspan="3">
        <p>$ '. $valor2.'</p>
        </td>
      </tr>
      <tr>
        <td><p>N° ÁRBOLES</p></td>
        <td>
        <p>'.$numero_arboles.'</p>
        </td>
        <td>TOTAL LIQUIDACÓN</td>
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
        <p class="concepto">LIQUIDACIÓN DE EVALUACIÓN TECNICA PARA APROVECHAMIENTO FORESTAL,TALA PODA Y/O TRASLADO DE '.$numero_arboles.' ÁRBOLES, SEGÚN SOLICITUD CON  #'.$sec.'</p>
        </div>
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
  $node->save();

//Fin factura




//Enviar Correo
    $module = 'Liquidadorepa';
    $key = 'red';
    // Specify 'to' and 'from' addresses.
    $to =$email_cotrib;
    $from =  $this->config('system.site')->get('mail');
   $params = [];
 /* $params['message'] = 'Liqiodación';
   $params['subject'] = 'Liquidación';
   $params['body'] = 'Liquidación';*/
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

/*$params['attachments'][] = [
    'filecontent' => $file,
    'filename' => '0.pdf',
    'filemime' => 'application/pdf',
  ];*/

	  /////////////////////
   /* $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);

 if ($result['result'] == TRUE) {
 $this->messenger()->addStatus($this->t('El documento se ha generado y se ha enviado un correo con las instrucciones, de lo contrario favor comunicar con el correo: info@liquidaciones.epacartagena.gov.co'));
 $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
$form_state->setRedirectUrl($url);
}else {
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


		      //Obtener Tarifa
    $vocabulary_name = 'tarifa_liquidacion';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);
    foreach ($terms as $term) {
      $id2 = $term->getFields();
          $value  = $term->get('field_valor_tarifa_liquidacion')->getValue();
    }
    $valor =$value[0]["value"]; // caprturar el valor de la tarifa
    $valor2 = number_format($valor, 2, ',', '.'); // mostrar la tarifa actual
	$this->setSessionValue('session_liquidacion.tarifa_af', $valor2);


     $cantidad_arboles = $form_state->getValue('cantidad_arboles_af');
	   $valor_liquidacion =  $valor *  $cantidad_arboles ;
     
	   $this->setSessionValue('session_liquidacion.valorLiquidacion_pdf',$valor_liquidacion);
	   $valor_liquidacion = number_format($valor_liquidacion, 2, ',', '.'); // mostrar la tarifa actual
	 $this->setSessionValue('session_liquidacion.valorLiquidacion_af',$valor_liquidacion);


    $cantidad_arboles_limit=26;

   if ($cantidad_arboles >= $cantidad_arboles_limit){
    $form_state->setErrorByName('cantidad_arboles_af', $this->t('Por disposiciones legales si la solicitud supera 25 árboles, este trámite debe realizarse a través del sistema VITAL del MinAmbiente en : http://vital.minambiente.gov.co'));
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

            //next1
              $form_state
              ->set('page_values2', [
               // Keep only first step values to minimize stored data.
              'name' => $form_state->getValue('name'),

               ])

               ->set('page_num', 2)

               ->setRebuild(TRUE);
                   $this->setSessionValue('session_liquidacion.id_document_af', $form_state->getValue('id_document'));
                   $this->setSessionValue('session_liquidacion.name_af', $form_state->getValue('name'));

                   $this->setSessionValue('session_liquidacion.dir_correspondencia_af', $form_state->getValue('dir_correspondencia'));
                   $this->setSessionValue('session_liquidacion.email_af', $form_state->getValue('email'));
                   $this->setSessionValue('session_liquidacion.estrato_af', $form_state->getValue('first_estrato'));
                   $this->setSessionValue('session_liquidacion.tmovil_af', $form_state->getValue('tmovil'));
                   $this->setSessionValue('session_liquidacion.tfijo_af', $form_state->getValue('tfijo'));


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
    //next2
    $form_state
      ->set('page_values3', [
        // Keep only first step values to minimize stored data.
        'cantidad_arboles_af' => $form_state->getValue('cantidad_arboles_af'),

      ])
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.barrio_af', $form_state->getValue('barrio'));
      $this->setSessionValue('session_liquidacion.dir_predio_af', $form_state->getValue('dir_predio'));
      $this->setSessionValue('session_liquidacion.name_predio_af', $form_state->getValue('name_predio'));
      $this->setSessionValue('session_liquidacion.cantidad_arboles_af', $form_state->getValue('cantidad_arboles_af'));
      $this->setSessionValue('session_liquidacion.especie_af', $form_state->getValue('especie'));

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
//next3


    $form_state
      ->set('page_values4', [
        // Keep only first step values to minimize stored data.

        'soportes1' => $form_state->getValue('soportes1'),

      ])
      ->set('page_num', 4)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.soportes1_af', $form_state->getValue('soportes1'));

      $this->setSessionValue('session_liquidacion.soportes2_af', $form_state->getValue('soportes2'));
      $this->setSessionValue('session_liquidacion.soportes3_af', $form_state->getValue('soportes3'));
      $this->setSessionValue('session_liquidacion.soportes4_af', $form_state->getValue('soportes4'));
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
    //pagina2

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('Zona y Tipo de Árboles'),
    ];


   $form['barrio'] = [
     '#type' => 'textfield',
     '#title' => 'Barrio',
      '#required' => TRUE,
     '#description' => 'Ingresar el barrio de Cartagena de Indias de  donde se realizará la Tala y/o Poda de Árboles.',
     '#default_value' => $this->session->get('session_liquidacion.barrio_af', ''),
       ];

$form['dir_predio'] = [
'#type' => 'textfield',
'#title' => 'Dirección del predio',
'#default_value' => $this->session->get('session_liquidacion.dir_predio_af', ''),
'#required' => TRUE,
'#description' => 'Dirección donde se realizará la Tala y/o Poda de Árboles.',
];



$form['name_predio'] = [
'#type' => 'textfield',
'#required' => TRUE,
'#title' => 'Nombre del propietario del predio',
'#default_value' => $this->session->get('session_liquidacion.name_predio_af', ''),
];
$form['s1'] = [
'#type' => 'markup',
'#markup' => '<hr>',
];

$form['cantidad_arboles_af'] = [
'#type' => 'textfield',
'#title' => $this->t('Número de Árboles a talar'),
'#description' => 'Por disposiciones legales si la solicitud supera 25 árboles, este trámite debe realizarse a través del sistema VITAL del MinAmbiente.',
'#default_value' => $this->session->get('session_liquidacion.cantidad_arboles_af', ''),
'#required' => TRUE,
];

$form['especie'] = [
'#type' => 'textfield',
'#title' => 'Especie',
'#description' => 'Multiples especies han de ser separadas por coma (,).',
'#default_value' => $this->session->get('session_liquidacion.especie_af', ''),
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
//pagina3
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

  $form['soportes1'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	  	'#required' => TRUE,
      '#title' => t('Documento Identidad'),
      '#size' => 20,
      '#description' => 'Documento Identidad. Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes1_af', ''),
    );

    $form['soportes2'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		  '#required' => TRUE,
      '#title' => t('RUT'),
      '#size' => 20,
      '#description' => 'Registro Único Tributario - RUT : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes2_af', ''),
    );
    $form['soportes3'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	    '#required' => TRUE,
      '#title' => t('Certificado de libertad y tradición'),
      '#size' => 20,
      '#description' => 'Certificado de libertad y tradición fecha de expedición no superior a 3 meses',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes3_af', ''),
    );
    $form['soportes4'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		  '#required' => TRUE,
      '#title' => t('Autorizacion'),
      '#size' => 20,
      '#description' => '<ul><il>Tenedor: Copia del documento que lo acredite como tal (contrato de arrendamiento, comodato, etc.) o autorización del propietario o poseedor.</li>

      <li>Poseedor: Manifestación escrita y firmada de tal calidad</li></ul>. Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes4_af', ''),
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
//pagina4
    // $form_state->set('page_num', 4);
     $form['description4'] = [
       '#type' => 'item',
       '#title' => $this->t('<h2>Validar la Información antes de enviar.</h2>'),
     ];
    $id= $this->session->get('session_liquidacion.id_document_af', '');
    $email=$this->session->get('session_liquidacion.email_af', '');
    $Cantidad = $this->session->get('session_liquidacion.cantidad_arboles_af', '');
    $valor2 = $this->session->get('session_liquidacion.tarifa_af', '');

	$valor_liquidacion = $this->session->get('session_liquidacion.valorLiquidacion_af','');



    $form['s0010'] = [
      '#type' => 'markup',
      '#markup' =>   '
    <div class="row">
    <div class="col"><spam style="
    font-weight: bold;
">Correo:<br> '.$email.'</spam></div>
    <div class="col"><spam style="
    font-weight: bold;
">No Identificación:<br> '.$id.'</spam></div>
    <div class="col"><spam style="
    font-weight: bold;
">Cantidad de Árboles:<br> '.$Cantidad.'</spam></div>
	 <div class="col"><spam style="
   font-weight: bold;
">Tarifa:<br> '.$valor2.'</spam></div>
	 <div class="col"><spam style="
   font-weight: bold;
">Valor Liquidación: <br>'. $valor_liquidacion .'</spam></div>
  </div>',
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
    //regresar2
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
      //regresar3
      ->setValues($form_state->get('page_values3'))
      ->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }
  public function fapiExamplePageFourBack(array &$form, FormStateInterface $form_state) {
    $form_state
    //regresar4
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
      'session_liquidacion.name_af',
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