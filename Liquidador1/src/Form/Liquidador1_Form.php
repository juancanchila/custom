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
class Liquidador1_Form extends FormBase
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
      '#title' => 'Documento de Identidad',
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
     '#title' => 'Nombre  ',
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
    $form['my_item'] = array(
      '#type' => 'item',
      '#title' => '<h2>Persona Natural:</h2>',
      '#markup' => '
      <hr>
      <ul>

      <li>
    Documento de Identidad (Cédula Ciudadanía, Cédula de Extranjería, Pasaporte) </li>
     <li>Registro Único Tributario - RUT </li>
      <li>Certificado de libertad y tradición (fecha de expedición no superior a 3 meses). </li>

    <li>Tenedor: Copia del documento que lo acredite como tal (contrato de arrendamiento, comodato, etc.) o autorización del propietario o poseedor.</li>

    <li>Poseedor: Manifestación escrita y firmada de tal calidad.</li>

    </ul>
<hr>

      ',


    );

*/
  $form['list'] = [
      '#type' => 'markup',
      '#markup' => '<hr>',
    ];

	    $validators = array(
      'file_validate_extensions' => array('pdf'),
    );

  
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
	 

/////////////////////////////////////
    $vocabulary_name = 'tarifa_liquidacion';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);


    foreach ($terms as $term) {
      $id2 = $term->getFields();
          $value  = $term->get('field_valor_tarifa_liquidacion')->getValue();

    }



/*

Valores  obtebidos para la Liquidación

**/
$valor =$value[0]["value"];
$valor2 = number_format($valor, 2, ',', '.');
/*

Valores  obtebidos para la Informacion de la factura

**/

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
  <p>SIMULACIÓN - EVALUACIÓN DE APROVECHAMIENTO FORESTAL</p>
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

        </td>
      </tr>


    </tbody>
  </table>

  ';

$mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']);

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-L']);
$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$mpdf->SetHTMLHeader('
<div style="text-align: right; font-weight: bold;">
   EPA
</div>','O');


$mpdf->WriteHTML($html);
$file = $mpdf->Output('simulador.pdf', 'D');

/*$params['attachments'][] = [
    'filecontent' => $file,
    'filename' => 'ggg.pdf',
    'filemime' => 'application/pdf',
  ];*/
	  
	  
  
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