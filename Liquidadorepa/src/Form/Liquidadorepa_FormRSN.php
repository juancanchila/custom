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


class Liquidadorepa_FormRSN extends FormBase
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
    return 'liquidadorepa_formrsn';
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
 //pagina 1


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
  '#markup' => '<hr><div><p> Ingresar en esta página la información de titular del trámite ambiental, siendo el titular la persona Jurídica que solicita la viabilidad.</p> </div>',
  ];

  $form['id_document_rsn'] = [
    '#type' => 'textfield',
    '#required' => TRUE,
    '#title' => 'NIT',
    '#default_value' => $this->session->get('session_liquidacion.id_document_rsn',''),
    ];

    $form['name_rsn'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
      '#title' => 'Razón Social ',
      '#description' => 'Razón Social de la entidad.',
      '#default_value' => $this->session->get('session_liquidacion.name_rsn', ''),
     ];
     $form['nombrelegal_rsn'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
      '#title' => 'Nombre de Representante legal ',
      '#description' => 'Razón Social de la entidad',
      '#default_value' => $this->session->get('session_liquidacion.nombrelegal_rsn', ''),

     ];
     $form['idlegal_rsn'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
      '#title' => 'N&#176; de Documento de Representante legal ',
      '#description' => 'Documento del Representante legal',
      '#default_value' => $this->session->get('session_liquidacion.idlegal_rsn', ''),
     ];
     $form['dir_correspondencia_rsn'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => 'Dirección de Correspondencia del Solicitante',
      '#default_value' => $this->session->get('session_liquidacion.dir_correspondencia_rsn', ''),
     ];
     $form['email_rsn'] = [
      '#type' => 'email',
     '#required' => TRUE,
      '#title' => 'Correo Electrónico',
      '#default_value' => $this->session->get('session_liquidacion.email_rsn', ''),
     ];


     $form['tfijo_rsn'] = [
      '#type' => 'textfield',
      '#title' => 'Teléfono fijo',
      '#default_value' => $this->session->get('session_liquidacion.tfijo_rsn', ''),
      ];


      $form['tmovil_rsn'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => 'Teléfono móvil',
        '#default_value' => $this->session->get('session_liquidacion.tmovil_rsn', ''),
       ];
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
//validate all form
       }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
	  



$valor_tarifa=$this->session->get('session_liquidacion.valor_tarifa_rsnn', '');

    $valor_liquidacion  = $this->session->get('session_liquidacion.valorLiquidacion_rsnn', '');

    $codigo_liquidacion =$this->session->get('session_liquidacion.id_document_rsn', '');

    $name_contrib = $this->session->get('session_liquidacion.name_rsn', '');
    $barrio_liquidacion = $this->session->get('session_liquidacion.barrio_rsn', '');

    $direccion_evento =  $this->session->get('session_liquidacion.direccion_evento_rsn', '');


    $nombre_establecimiento=  $this->session->get('session_liquidacion.nombre_establecimiento_rsn', '');

    $tipo_solicitante = "Persona Jurídica";
    $id_contribuyente = $this->session->get('session_liquidacion.id_document_rsn', '');
    $dir_correspondecia_contrib = $this->session->get('session_liquidacion.dir_correspondencia_rsn', '');
    $email_cotrib = $this->session->get('session_liquidacion.email_rsn', '');
    $tfijo =$this->session->get('session_liquidacion.tfijo_rsn', '');
    $tmovil = $this->session->get('session_liquidacion.tmovil_rsn', '');
    $estrato = $this->session->get('session_liquidacion.estrato_rsn', '');
    $condicion = $this->session->get('session_liquidacion.condicion_rsn', '');
     $description_rsnvento = $this->session->get('session_liquidacion.description_rsnvento', '');

     $total_mts2 =  $this->session->get('session_liquidacion.numero_m2', '');
	  
	  
    $valor_tarifa1_menor45  = 290000;
    $valor_tarifa2_entre46y79  = 435000;
    $valor_tarifa3_mayor80  = 580000;
	  
	  

if( $total_mts2 < 46){

 $valor_tarifa = $valor_tarifa1_menor45 ;
}

if( $total_mts2 >= 46 && $total_mts2 < 81  ){

 $valor_tarifa =  $valor_tarifa2_entre46y79  ;
}

if( $total_mts2 > 80 ){

  $valor_tarifa = $valor_tarifa3_mayor80 ;
}

	   $valor_tarifa = number_format($valor_tarifa, 2, ',', '.');
       // $f1 =$this->session->get('session_liquidacion.fecha_Inicial_rsn', '');
       // $f2 =$this->session->get('session_liquidacion.fecha_Final_rsn', '');
        $file1 = $this->session->get('session_liquidacion.soportes1_rsn');
       // $file2 = $this->session->get('session_liquidacion.soportes2_rsn');
       // $file3 = $this->session->get('session_liquidacion.soportes3_rsn');


 //crear factura
/*

Creando un nodo tipo factura con los datos recibidos

**/
$my_article = Node::create(['type' => 'liquidacion']);
$my_article->set('title', $codigo_liquidacion);
$my_article->set('field_valor' , $valor_liquidacion_r);//aqui
$my_article->set('field_barrio_liquidacion', $barrio_liquidacion);//aqui//
$my_article->set('field_concepto_ambiental_liq', "Rumba Segura");
$my_article->set('field_direccion_correspondencia', $dir_correspondecia_contrib);
$my_article->set('field_direccion_del_predio', $direccion_evento);
  $my_article->set('field_nombre_predio', $nombre_establecimiento );

$my_article->set('field_tipo_de_solicitante', $tipo_solicitante);
$my_article->set('field_id_contribuyente', $id_contribuyente);
$my_article->set('field_nombre_contribuyente', $name_contrib);
$my_article->set('field_email_contribuyente', $email_cotrib );
$my_article->set('field_telefono_fijo_contribuyent', $tfijo);
$my_article->set('field_telefono_movil_contribuyen', $tmovil);
$my_article->set('field_estrato_contribuyente', $estrato);
$my_article->set('field_condicion_contribuyente', $condicion);
$my_article->set('field_comparado_factura',false);
//$my_article->set('field_codigo_liquidacion_factura', false);
$my_article->set('field_estado',FALSE);


$my_article->set('field_id_file', $file1);
//$my_article->set('field_rut_file', $file2);
//$my_article->set('field_ei_file', $file3);

$my_article->set('status', '0');
//$my_article->set('uid', $id_contribuyente);

$my_article->enforceIsNew();
  $my_article->save();

$nid = $my_article->id();
$node = \Drupal\node\Entity\Node::load($nid);; //Accedemos a la información del nodo
/** Obteniendo el field_consecutivo_factura del nodo creado */
  $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
$sec ="05"."0".$consecutivo_facturas[0]["value"].date('Y');
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
<p>Evaluación Rumba Segura</p>
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
   <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 624
   de 31 de octubre de 2022  </p></td>
   <td colspan="4">
   <p>$ '.$valor_tarifa.'</p>
   </td>
 </tr>
 <tr>
   <td >TOTAL LIQUIDACIÓN</td>
   <td colspan="3">
   <p style="
font-weight: bold;
">$ '.$valor_tarifa.'</p>
   </td>
 </tr>
 <tr>
   <td colspan="4">
   <p>CONSIDERACIONES</p>

   <p> Se implementa el sistema de insonorización y cumplimiento normativo de la  Resolución 0627 de 2006 del MAVDT en el marco del decreto 1461 de 2022 del Distrito de Cartagena</p>

   <p>Esta suma deberá&nbsp;consignarse en la Cuenta de Ahorros No. 43300400033-0 del Banco GNB sudameris relacionando el número de la liquidación, a favor del EPA-Cartagena. Para efectos de acreditar la cancelación de los costos indicados, el usuario deberá presentar original del recibo de consignación, y cargar copia en el aplicativo VITAC</p>

   <p>Favor no hacer retención por ningún concepto, somos no contribuyentes Según Art. 23 Art 369 y Ley 633 de 2000, Art. 5</p>
   </td>
 </tr>
 <tr>
   <td colspan="4">
   <p>CONCEPTO</p>

   <div class="concepto">
   <p class="concepto">Liquidación Evaluación Rumba Segura</p>
   </div>
   </td>
 </tr>

</tbody>
</table>

<table>
<tbody>

<tr>
<td>
Detalle del Establecimiento: <p> Nombre Establecimiento: '. $nombre_establecimiento.'<p>
<p> Dirección del Establecimiento'.$direccion_evento .'<p>

<p> Total Metros Cuadrados :'.$total_mts2 .'</p>


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
   //$params['message'] = 'Mail Body';
   //$params['subject'] = 'Sample Subject';
   $language_code = $this->languageManager->getDefaultLanguage()->getId();

   $send_now = TRUE;


//eniar correo


 //$mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']);
	$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);



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
    //$params['test'] = 'Gracias!';

	  /////////////////////
  /*  $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);


    if ($result['result'] == TRUE) {

    $this->messenger()->addStatus($this->t('El documento se ha generado y se ha enviado un correo con las instrucciones, de lo contrario favor comunacar con el correo: info@liquidaciones.epacartagena.gov.co'));

		  $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
           $form_state->setRedirectUrl($url);


    }
    else {
      $this->messenger()->addMessage($this->t('Mensaje no enviado validar dirección de correo!.'), 'error');
    }
*/

    $this->invalidateCacheTag();
   /* $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['user' =>1]);*/
 
	  
	// url to redirect
$path = '/zona_de_tramites';
// query string
$path_param = [
 'abc' => '123',
 'xyz' => '456'
];
// use below if you have to redirect on your known url
$url = Url::fromUserInput($path, ['query' => $path_param]);
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
            'name' => $form_state->getValue('name'),

             ])

             ->set('page_num', 2)

             ->setRebuild(TRUE);
                 $this->setSessionValue('session_liquidacion.idlegal_rsn', $form_state->getValue('idlegal_rsn'));
                 $this->setSessionValue('session_liquidacion.id_document_rsn', $form_state->getValue('id_document_rsn'));
                 $this->setSessionValue('session_liquidacion.name_rsn', $form_state->getValue('name_rsn'));
                 $this->setSessionValue('session_liquidacion.nombrelegal_rsn', $form_state->getValue('nombrelegal_rsn'));
                 $this->setSessionValue('session_liquidacion.dir_correspondencia_rsn', $form_state->getValue('dir_correspondencia_rsn'));
                 $this->setSessionValue('session_liquidacion.email_rsn', $form_state->getValue('email_rsn'));
                 $this->setSessionValue('session_liquidacion.estrato_rsn', $form_state->getValue('first_rsnstrato_'));
                 $this->setSessionValue('session_liquidacion.tmovil_rsn', $form_state->getValue('tmovil_rsn'));
                 $this->setSessionValue('session_liquidacion.tfijo_rsn', $form_state->getValue('tfijo_rsn'));
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
        'nombre_establecimiento_rsn' => $form_state->getValue('nombre_establecimiento_rsn'),


      ])
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.description_evento_rsn', $form_state->getValue('descripcion_rsnvento_rsn'));
      $this->setSessionValue('session_liquidacion.barrio_rsn', $form_state->getValue('barrio'));

      $this->setSessionValue('session_liquidacion.numero_m2', $form_state->getValue('numero_m2'));

      $this->setSessionValue('session_liquidacion.nombre_establecimiento_rsn', $form_state->getValue('nombre_establecimiento_rsn'));

      $this->setSessionValue('session_liquidacion.numero_dias_rsn', $form_state->getValue('numero_dias_rsn'));
      $this->setSessionValue('session_liquidacion.direccion_evento_rsn', $form_state->getValue('direccion_evento_rsn'));







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

        'soportes1_rsn' => $form_state->getValue('soportes1_rsn'),

      ])
      ->set('page_num', 4)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
      $this->setSessionValue('session_liquidacion.soportes1_rsn', $form_state->getValue('soportes1'));
      $this->setSessionValue('session_liquidacion.soportes2_rsn', $form_state->getValue('soportes2'));
      $this->setSessionValue('session_liquidacion.soportes3_rsn', $form_state->getValue('soportes3'));
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
    //paguina 2



    $form['description_rsnvento'] = [
      '#type' => 'item',
      '#title' => $this->t('Detalles del Evento'),
      '#default_value' => $this->session->get('session_liquidacion.description_rsnvento', ''),
    ];

	      $form['numero_m2'] = array(
      '#type' => 'number',
      '#title' => 'Ingresar Área comercial en mts2',
      '#width' => '30%',
      '#align' => 'center',
      '#default_value' => $this->session->get('session_liquidacion.numero_m2', ''),
      '#required' => true
  );

    $form['barrio'] = [
      '#type' => 'textfield',
      '#title' => 'Barrio',
       '#required' => TRUE,
      '#description' => 'Ingresar el barrio de Cartagena de Indias de  donde se realizará el evento.',
      '#default_value' => $this->session->get('session_liquidacion.barrio_rsn', ''),
        ];
        $form['nombre_establecimiento_rsn'] = [
          '#type' => 'textfield',
          '#title' => 'Nombre Establecimiento Comercial',
           '#required' => TRUE,
          '#description' => 'Ingresar el nombre del establecimiento comercial.',
          '#default_value' => $this->session->get('session_liquidacion.nombre_establecimiento_rsn', ''),
            ];
      $form['s1'] = [
        '#type' => 'markup',
        '#markup' => '<hr>',
      ];
/*

      $form['latitud_rsn'] = [
        '#type' => 'textfield',
        '#title' => 'Latitud',
         '#required' => TRUE,
        '#description' => 'Georeferenciación - Latitud',
        '#default_value' => $this->session->get('session_liquidacion.latitud_rsn', ''),
          ];
          $form['longitud_rsn'] = [
            '#type' => 'textfield',
            '#title' => 'Longitud',
             '#required' => TRUE,
            '#description' => 'Georeferenciación - Longitud',
            '#default_value' => $this->session->get('session_liquidacion.longitud_rsn', ''),
              ];




        $form['valor_rsnvento_rsn'] = array(
      '#type' => 'number',
    '#title' => $this->t('Valor Inversión'),
      '#description' => 'Ingresar el valor de la inversión en el montaje del evento a evaluar sin espacios, si puntos ni comas. ',
      '#width' => '30%',
      '#align' => 'center',
      '#default_value' => $this->session->get('session_liquidacion.valor_rsnvento_rsn', ''),
      '#required' => true,
      '#maxlength' =>10
  );





    $form['fecha_Inicial'] = array (
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
              //'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),
          ],
  );
  $form['fecha_Final'] = array (
    '#type' => 'date',
   '#title' => 'Ingresar Fecha de finalización del Evento',
    //'#default_value' => date('Y-m-d'),
    '#default_value' => '',
    //'#description' => date('d-m-Y', time()),
    '#description' => 'Seleccionar el día de finalizacion del evento ',
    '#required' => TRUE,
     '#attributes' => [
              'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
             // 'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),
          ],
    );

    $form['numero_dias_rsn'] = array(
      '#type' => 'number',
      '#title' => 'Ingresar duración del evento en días',
      '#width' => '30%',
      '#align' => 'center',
      '#default_value' => $this->session->get('session_liquidacion.numero_dias_rsn', ''),
      '#required' => true,
      '#maxlength' => 3
  );




      $form['descripcion_rsnvento_rsn'] = [
        '#type' => 'textfield',
        '#title' => 'Breve Descripción del Evento',
        '#default_value' => $this->session->get('session_liquidacion.descripcion_rsnvento_rsn', ''),
       '#required' => TRUE,
      ];*/
      $form['direccion_evento_rsn'] = [
        '#type' => 'textfield',
        '#title' => 'Dirección del Establecimiento',
        '#default_value' => $this->session->get('session_liquidacion.direccion_evento_rsn', ''),
      '#required' => TRUE,
      ];

      $form['sn'] = [
        '#type' => 'markup',
        '#markup' => '<hr class="notificaciones">',
      ];


      $form['email_not_rsn'] = [
        '#type' => 'email',
       '#required' => TRUE,
        '#title' => 'Correo Eléctrónico - Notificaciones',
        '#default_value' => $this->session->get('session_liquidacion.email_rsn', ''),
       ];



        $form['tmovil_not_rsn'] = [
          '#type' => 'textfield',
          '#required' => TRUE,
          '#title' => 'Teléfono móvil Notificaicones',
          '#default_value' => $this->session->get('session_liquidacion.tmovil_rsn', ''),
         ];


         $form['accept_not_rsn'] = array(
          '#type' => 'checkbox',
         '#required' => TRUE,
          '#title' => $this
            ->t('Yo, Acepto terminos y condiciones del uso de mis datos personales y confirmo que deseo recibir notificaciones del tramite solicitado.'),
         // '#description' => $this->t('<a href="http://www.epacartagena.gov.co/wp-content/uploads/2019/05/aviso%20privacidad%20-%20EPA.pdf" target="_blank">Política de tratamiento de datos personales</a>'),
        );



    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::fapiExamplePageTwoBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_rsnrrors' => [],
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
   //pagina 3
    $form['description3'] = [
      '#type' => 'item',
      '#title' => $this->t('<div>Documentos Requeridos:'),
    ];


    $form['s3'] = [
      '#type' => 'markup',
      '#markup' => '
      Cargar los documentos en formato PDF con un tamaño de cada archivo inferior a : 8MB.
      </p>',
    ];




  $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr>',
    ];

	    $validators = array(
      'file_validate_rsnxtensions' => array('pdf'),
    );

    $form['soportes1'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	    '#required' => TRUE,
      '#title' => t('Soportes Liquidación'),
      '#size' => 20,
      '#description' => '<div>
      <ul>
      <li> Solicitud por escrito con los datos básicos del establecimiento de Comercio,
georreferenciación de la ubicación del lugar donde opera, dirección de
notificación, número de contacto y correo electrónico.</li>
<li>Certificado de libertad y tradición, o documento que acredite la condición de
tenedor o poseedor de lugar.
<li> Copia de la cédula del solicitante y del representante legal del Establecimiento.</li>
<li> Certificado de Existencia y Representación Legal expedido por la Cámara de
Comercio y/o documento que acredite su calidad comerciante.
<li> Documento que describa tipo de trabajo de insonorización realizado dentro del
establecimiento. El cual debe ser demostrable a través de planos u otro tipo de
evidencias.</li>
<li> Inventario de los artefactos sonoros del establecimiento.</li>

    </ul>

      </div>. Límite: 8MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes1_rsn',''),
    );


/*
    $form['soportes2'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	   	'#required' => TRUE,
      '#title' => t('RUT'),
      '#size' => 20,
      '#description' => 'Registro Único Tributario - RUT : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes2_rsn',''),
    );


    $form['soportes3'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	  	'#required' => TRUE,
      '#title' => t('Formato de evaluación'),
      '#size' => 20,
      '#description' => 'Formato de Evaluación | <a href="https://epacartagena.gov.co/web/wp-content/uploads/2021/02/Anexo_1_de_la_resolucion_1.pdf" target="_blank" >Descargar </a>: Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes3_rsn',''),
    );

*/
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::fapiExamplePageThreeBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_rsnrrors' => [],
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
       '#title' => $this->t('<h2>Validar la Información antes de enviar.</h2'),
     ];
     $id= $this->session->get('session_liquidacion.id_document_rsn', '');
     $email=$this->session->get('session_liquidacion.email_rsn', '');
     $Cantidad=$this->session->get('session_liquidacion.valor_rsnvento_rsn', '');
	  
     $Cantidad = number_format(floatval($Cantidad), 2, ',', '.');

     $tarifa =  $this->session->get('session_liquidacion.valor_tarifa_rsnn', '');

     $total_liquidacion = $this->session->get('session_liquidacion.valorLiquidacion_rsnn', '');

     $nombre_establecimiento=  $this->session->get('session_liquidacion.nombre_establecimiento_rsn', '');
     $Direccion=$this->session->get('session_liquidacion.direccion_evento_rsn', '');

     $total_mts2 =  $this->session->get('session_liquidacion.numero_m2', '');


    $valor_tarifa1_menor45  = 290000;
    $valor_tarifa2_entre46y79  = 435000;
    $valor_tarifa3_mayor80  = 580000;
	  
	  

if( $total_mts2 < 46){

 $valor_tarifa = $valor_tarifa1_menor45 ;
}

if( $total_mts2 >= 46 && $total_mts2 < 81  ){

 $valor_tarifa =  $valor_tarifa2_entre46y79  ;
}

if( $total_mts2 > 80 ){

  $valor_tarifa = $valor_tarifa3_mayor80 ;
}
   $valor_tarifa = number_format($valor_tarifa, 2, ',', '.');
      $form['s0010'] = [
       '#type' => 'markup',
       '#markup' =>   '
     <div class="row">
     <div class="col">Correo:<br>  '.$email.'</div>
     <div class="col">No Identificación:<br>  '.$id.'</div>
     <div class="col">Dirección Establecimiento:<br>  '.  $Direccion.'</div>
     <div class="col">Nombre del Establecimiento:<br>  '.   $nombre_establecimiento.'</div>
     <div class="col">Total Liquidación:<br>  '.     $valor_tarifa .'</div>
          <div class="col">Total mts2:<br>  '.     $total_mts2 .'</div>
   </div>

',
      ];



    $form['accept'] = array(
      '#type' => 'checkbox',
		 '#required' => TRUE,
      '#title' => $this
        ->t('Yo, Acepto terminos y condiciones del uso de mis datos personales.'),
      '#description' => $this->t('<a href="http://www.epacartagena.gov.co/wp-content/uploads/2019/05/aviso%20privacidad%20-%20EPA.pdf" target="_blank">Política de tratamiento de datos personales</a>'),
    );

     $form['my_captcha_rsnlement'] = array(
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
       '#limit_validation_rsnrrors' => [],
     ];




        // Add a submit button that handles the submission of the form.
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Generar Volante de Pago'),
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
      'session_liquidacion.name',
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
    $this->cacheTagInvalidator->invalidateTags(['session_rsnxample:' . $this->session->getId()]);
  }





}