<?php


namespace Drupal\Liquidador1\Form;

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

/**
 * Implements a codimth Simple Form API.
 */
class Liquidador1_Form2 extends FormBase
{
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
   * Constructs a new EmailExampleGetFormPage.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   *   The email validator.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EmailValidator $email_validator) {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $form = new static(
      $container->get('plugin.manager.mail'),
      $container->get('language_manager'),
      $container->get('email.validator')
    );
    $form->setMessenger($container->get('messenger'));
    $form->setStringTranslation($container->get('string_translation'));
    return $form;
  }
  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

	  $form['s0'] = [
      '#type' => 'markup',
      '#markup' => '<hr><p><div>Los campos obligatorios estan marcados con un (*)</div></p>',
    ];
    $form['s0'] = [
      '#type' => 'markup',
      '#markup' => '<hr><p><div>Zona y Tipo de Árboles</div></p>',
    ];

    //$form['barrio'] = array(
     // '#type' => 'entity_autocomplete',
     // '#required' => TRUE,
     // '#title' => t('Barrio'),
     //      '#description' => '<p>Seleccionar el barrio de Cartagena de Indias de  donde se realizará la Tala y/o Poda de Árboles.</p><p> El formulario sólo  tendrá validez en los Barrios //que generan coincidencia al escribir.</p>',
     // '#target_type' => 'taxonomy_term',
     // '#selection_settings' => [
      //    'include_anonymous' => FALSE,
     //     'target_bundles' => array('barrios'),
   //   ],
//  );
  $form['barrio'] = [
    '#type' => 'textfield',
    '#title' => 'Barrio',
   '#required' => TRUE,
    '#description' => 'Ingresar el barrio de Cartagena de Indias de  donde se realizará la Tala y/o Poda de Árboles.',
  ];

  $form['dir_predio'] = [
    '#type' => 'textfield',
    '#title' => 'Dirección del predio',
   '#required' => TRUE,
    '#description' => 'Dirección donde se realizará la Tala y/o Poda de Árboles.',
  ];

  $form['name_predio'] = [
    '#type' => 'textfield',
   '#required' => TRUE,
    '#title' => 'Nombre del propietario del predio',
  ];
    $form['s1'] = [
      '#type' => 'markup',
      '#markup' => '<hr>',
    ];

    $form['cantidad_arboles'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Número de Árboles a talar'),
      '#description' => 'Por disposiciones legales si la solicitud supera 25 árboles, este trámite debe realizarse a través del sistema VITAL del MinAmbiente.',
     '#required' => TRUE,
    ];

    $form['especie'] = [
      '#type' => 'textfield',
      '#title' => 'Especie',
      '#description' => 'Multiples especies han de ser separadas por coma (,).',

    ];
    $form['s2'] = [
      '#type' => 'markup',
      '#markup' => '<hr><p><div>Información de quien Realiza la liquidación</div></p>',
    ];

/*
    $form['tipo_de_solicitante'] = [
      '#type' => 'radios',
     '#required' => TRUE,
      '#title' => $this->t('Tipo de Solicitante'),
      '#default_value' => 'natural',
      '#options' => [

        'natural' => $this->t('Persona Natural'),
        'juridica' => $this->t('Persona Jurídica'),
      ],
      '#attributes' => [

        'name' => 'field_select',
      ],

      '#states' => [
        'enabled' => [

          ':input[name="field_custom"]' => ['value' => ''],
        ],
      ],
    ];*/
    $form['id_document'] = [
      '#type' => 'textfield',
   '#required' => TRUE,
      '#title' => 'NIT',
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
     '#title' => 'Nombre',
      '#description' => 'Nombre del Representante legal.',
    ];


    $form['dir_correspondencia'] = [
      '#type' => 'textfield',
  '#required' => TRUE,
      '#title' => 'Dirección de Correspondencia',
    ];
    $form['email'] = [
      '#type' => 'email',
   '#required' => TRUE,
     '#title' => 'Correo Electrónico',
    ];
    $form['tfijo'] = [
      '#type' => 'textfield',

     '#title' => 'Teléfono fijo',
    ];

    $form['tmovil'] = [
      '#type' => 'textfield',
    '#required' => TRUE,
     '#title' => 'Teléfono móvil',
    ];

    $form['estrato'] = array(
      '#title' => t('Estrato'),
      '#type' => 'select',
          '#description' => 'Seleccionar el estrato de quien realiza la liquidación.',
      '#options' => array(t('--- Seleccionar ---'), t('1'), t('2'), t('3'), t('4') , t('5') , t('6')    ),
    );

    $form['condicion'] = array(
      '#title' => t('¿Se encuentra en alguna condición especial?'),
      '#type' => 'select',
         '#options' => array(t('Ninguno'), t('Adulto mayor'), t('Habitante de la calle'), t('Mujer gestante'), t('Peligro inminente') , t('Persona en condición de discapacidad') , t('Víctima del conflicto armado') , t('Menor de edad')     ),
    );

    $form['s3'] = [
      '#type' => 'markup',
      '#markup' => '<hr><p><div>Documentos Requeridos:</div>
    Cargar los documentos en formato PDF con un tamaño de cada archivo inferior a : 2MB.
      </p>',
    ];
/*

    $form['my_item2'] = array(
      '#type' => 'item',
      '#title' => '<h2>Persona Jurídica:</h2>',
      '#markup' => '
      <hr>
      <ul>
	  <li>Documento de Identidad (Cédula Ciudadanía, Cédula de Extranjería, Pasaporte)
</li>
            <li>


      Certificado de existencia y representación legal </li>
       <li>Registro Único Tributario - RUT</li>
        <li>Propietario del inmueble: Certificado de Libertad y Tradición (fecha de expedición no superior a 3 meses) o Escritura Pública del predio</li>
      <li>Tenedor: Copia del documento que lo acredite como tal (contrato de arrendamiento, comodato, etc.) o autorización del propietario o poseedor.</li>
     <li> Poseedor: Manifestación escrita y firmada de tal calidad.</li>
       <li>Copia de la escritura Pública del predio</li>
      <li>Para terceros apoderados, anexar el poder que lo acredite.</li>

      </ul>
    <hr>
      ',

    );
*/  $form['list'] = [
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
    );


    $form['soportes3'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Autorización'),
      '#size' => 20,
      '#description' => ' <ul><li>Propietario del inmueble: Certificado de Libertad y Tradición (fecha de expedición no superior a 3 meses) o Escritura Pública del predio</li>
      <li>Tenedor: Copia del documento que lo acredite como tal (contrato de arrendamiento, comodato, etc.) o autorización del propietario o poseedor.</li>
     <li> Poseedor: Manifestación escrita y firmada de tal calidad.</li>
      <li>Para terceros apoderados, anexar el poder que lo acredite.</li></ul>.',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
    );

    $form['soportes4'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Autorizacion'),
      '#size' => 20,
      '#description' => 'Copia de la escritura Pública del predio. Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
    );


    $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr>',
    ];

    $form['accept'] = array(
      '#type' => 'checkbox',
		 '#required' => TRUE,
      '#title' => $this
        ->t('Yo, Acepto terminos y condiciones del uso de mis datos personales.'),
      '#description' => $this->t('<a href="http://epacartagena.gov.co/web/wp-content/uploads/2021/01/PLAN-DE-TRAMIENTO-DE-RIESGOS-DE-SEGURIDAD-Y-PRIVACIDAD-DE-LA-INFORMACION_2021.pdf" target="_blank">Política de tratamiento de datos personales</a>'),
    );

// Data

$form['my_captcha_element'] = array(
		'#type' => 'captcha',
		'#captcha_type' => 'recaptcha/reCAPTCHA',
	);
    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Liquidar'),
    ];



    $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr><br/>',
    ];

    return $form;
  }

  /**
   * @return string
   */
  public function getFormId()
  {
    return 'Liquidador1_Form';
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $cantidad_arboles = $form_state->getValue('cantidad_arboles');;
    $cantidad_arboles_limit=26;

   if ($cantidad_arboles >= $cantidad_arboles_limit){
    $form_state->setErrorByName('cantidad_arboles', $this->t('Por disposiciones legales si la solicitud supera 25 árboles, este trámite debe realizarse a través del sistema VITAL del MinAmbiente en : http://vital.minambiente.gov.co'));
}


  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {




    $vocabulary_name = 'tarifa_arboles';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);


    foreach ($terms as $term) {
      $id2 = $term->getFields();
          $value  = $term->get('field_tarifa_arboles')->getValue();

    }



/*

Valores  obtebidos para la Liquidación

**/
$valor =$value[0]["value"];
$valor2 = number_format($valor, 2, ',', '.');
/*

Valores  obtebidos para la Informacion de la factura

**/


$file1 = $form_state->getValue('soportes1');
$file2 = $form_state->getValue('soportes2');
$file3 = $form_state->getValue('soportes3');
$file4 = $form_state->getValue('soportes4');


$codigo_liquidacion = $form_state->getValue('id_document');

$barrio_liquidacion = $form_state->getValue('barrio');
$direccion_predio_liquidacion = $form_state->getValue('dir_predio');
$name_predio= $form_state->getValue('name_predio');
$numero_arboles  = $form_state->getValue('cantidad_arboles');
$especie_arboles  = $form_state->getValue('especie');
$tipo_solicitante = $form_state->getValue('tipo_de_solicitante');
$id_contribuyente = $form_state->getValue('id_document');
$name_contrib= $form_state->getValue('name');

$valor_liquidacion = $numero_arboles * $valor + 1;
 $valor = $valor_liquidacion;
$valor_liquidacion = number_format($valor_liquidacion, 2, ',', '.');
//echo money_format('%(#10n', $valor_liquidacion) . "\n";

$dir_correspondecia_contrib = $form_state->getValue('dir_correspondencia');
$email_cotrib = $form_state->getValue('email');
$tfijo = $form_state->getValue('tfijo');
$tmovil = $form_state->getValue('tmovil');
$estrato = $form_state->getValue('estrato');
$condicion = $form_state->getValue('condicion');
/*

Creando un nodo tipo factura con los datos recibidos

**/


         $my_article = Node::create(['type' => 'factura']);
         $my_article->set('title', $codigo_liquidacion);
        // $my_article->set('field_valor', $valor);
         $my_article->set('field_barrio_liquidacion', $barrio_liquidacion);
	     $my_article->set('field_concepto_ambiental_liq',"Tala");
         $my_article->set('field_nombre_predio', $name_predio);
         $my_article->set('field_direccion_correspondencia_', $dir_correspondecia_contrib);
         $my_article->set('field_direccion_del_predio', $direccion_predio_liquidacion);
         $my_article->set('field_especie', $especie_arboles);

         $my_article->set('field_numero_de_arboles', 1);
         $my_article->set('field_tipo_de_solicitante', $tipo_solicitante);
         $my_article->set('field_id_contribuyente', $id_contribuyente);
         $my_article->set('field_nombre_contribuyente', $name_contrib);
         $my_article->set('field_email_contribuyente', $email_cotrib );
         $my_article->set('field_telefono_fijo_contribuyent', $tfijo);
         $my_article->set('field_telefono_movil_contribuyen', $tmovil);
         $my_article->set('field_estrato_contribuyente', $estrato);
         $my_article->set('field_condicion_contribuyente', $condicion);

         $my_article->set('field_id_file', $file1);
	     $my_article->set('field_rut_file', $file2);
       $my_article->set('field_autorizacion', $file3);
       $my_article->set('field_escritura', $file4);

	    $my_article->set('field_comparado_factura',false);
	     $my_article->set('field_codigo_liquidacion_factura', false);

	     $my_article->set('field_estado',FALSE);
         $my_article->set('status', '0');
       //  $my_article->set('uid', $id_contribuyente);

         $my_article->enforceIsNew();
           $my_article->save();

       $nid = $my_article->id();
       $node = \Drupal\node\Entity\Node::load($nid);; //Accedemos a la información del nodo
	    /** Obteniendo el field_consecutivo_factura del nodo creado */
           $consecutivo_facturas = $node->get('field_consecutivo_factura')->getValue();
	       $sec ="01"."0".$consecutivo_facturas[0]["value"].date('Y');
     $node->set('title', $sec); //Accedemos al campo que queremos editar/modificar
	  $node->set('field_codigo_liquidacion_factura', $sec); //valor liquidacion

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
             <p class="concepto">LIQUIDACIÓN DE EVALUACIÓN TECNICA PARA APROVECHAMIENTO FORESTAL,TALA PODA Y/O TRASLADO DE '.$numero_arboles.' ÁRBOLES, SEGÚN SOLICITUD CON RADICADO #'.$sec.'</p>
             </div>
             </td>
           </tr>

         </tbody>
       </table>

       ';

       $code="4157709998461239"."8020".$sec."3900".$valor."96".date('Y')."1231";
       $code_content="(415)7709998461239"."(8020)".$sec."(3900)".$valor."(96)".date('Y')."1231";

	   $node->set('body',$html);
       $node->body->format = 'full_html';
$node->save(); //Guarda el cambio una vez realizado

/////////////////////////////////////

$module = 'Liquidador1';
    $key = 'contact_message';

    // Specify 'to' and 'from' addresses.
   $to =$email_cotrib;
    $from =  $this->config('system.site')->get('mail');

    // "params" loads in additional context for email content completion in
    // hook_mail(). In this case, we want to pass in the values the user entered
    // into the form, which include the message body in $form_values['message'].
// $params = $form_values;

    // The language of the e-mail. This will one of three values:
    // - $account->getPreferredLangcode(): Used for sending mail to a particular
    //   website user, so that the mail appears in their preferred language.
    // - \Drupal::currentUser()->getPreferredLangcode(): Used when sending a
    //   mail back to the user currently viewing the site. This will send it in
    //   the language they're currently using.
    // - \Drupal::languageManager()->getDefaultLanguage()->getId: Used when
    //   sending mail to a pre-existing, 'neutral' address, such as the system
    //   e-mail address, or when you're unsure of the language preferences of
    //   the intended recipient.
    //
    // Since in our case, we are sending a message to a random e-mail address
    // that is not necessarily tied to a user account, we will use the site's
    // default language.
    $language_code = $this->languageManager->getDefaultLanguage()->getId();

    // Whether or not to automatically send the mail when we call mail() on the
    // mail manager. This defaults to TRUE, and is normally what you want unless
    // you need to do additional processing before the mail manager sends the
    // message.
    $send_now = TRUE;
    // Send the mail, and check for success. Note that this does not guarantee
    // message delivery; only that there were no PHP-related issues encountered
    // while sending.




//$html = 'this is my <b>first</b>downloadable pdf';

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
		<td width="15%">CODE</td>
		<td>Contenido de la clave de pago</td>
		<td>Clave de Pago</td>
	</tr>
</thead>
<tbody>
<tr>
<td align="center">EAN128B [A/B/C]</td>
<td>'.$code_content.'</td>
<td class="barcodecell"><barcode code="'.$code.'" type="EAN128B" class="barcode" /></td>
</tr>
</tbody>
</table>

');


$mpdf->WriteHTML($html);
$file = $mpdf->Output($sec.'.pdf', 'S');

$params['attachments'][] = [
    'filecontent' => $file,
    'filename' => $sec.'.pdf',
    'filemime' => 'application/pdf',
  ];




	  /////////////////////
    $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);


		 if ($result['result'] == TRUE) {

    $this->messenger()->addStatus($this->t('El documento se ha generado y se ha enviado un correo con lmas instrucciones, de lo contrario favor comunacar con el correo: info@liquidaciones.epacartagena.gov.co'));

		  $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
           $form_state->setRedirectUrl($url);


    }
    else {
      $this->messenger()->addMessage($this->t('Mensaje no enviado validar dirección de correo!.'), 'error');
    }



  }

}