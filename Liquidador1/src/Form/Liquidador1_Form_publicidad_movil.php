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
class Liquidador1_Form_publicidad_movil extends FormBase
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

    $form['s1'] = [
      '#type' => 'markup',
      '#markup' => '<hr>',
    ];

	$form['fecha_Inicial'] = array (
'#type' => 'date',
'#title' => 'Fecha Inicial para la Publicidad',
//'#default_value' => date('Y-m-d'),
'#default_value' => '',
//'#description' => date('d-m-Y', time()),
'#description' => 'Seleccionar el día de inicio de la publicidad ',
'#required' => TRUE,
	//
	 '#attributes' => [
            'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
        ],
);
$form['fecha_Final'] = array (
  '#type' => 'date',
 '#title' => 'Ingresar Fecha de finalización de la publicidad',
  //'#default_value' => date('Y-m-d'),
  '#default_value' => '',
  //'#description' => date('d-m-Y', time()),
  '#description' => 'Seleccionar el día de finalizacion de la publicidad ',
  '#required' => TRUE,
	 '#attributes' => [
            'min' =>  \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', 'Y-m-d'),
        ],
  );

	   $form['numero_meses'] = array(
    '#type' => 'number',
    '#title' => 'Ingresar duración de la publicidad  en meses',
    '#width' => '30%',
    '#align' => 'center',
    '#required' => true,
    '#maxlength' => 3
);
	$form['field_select_NV'] = array(
  '#type' => 'select',
    '#required' => TRUE,
  '#title' => $this->t('Número de Vehículos a Evaluar'),
 // '#default_value' => '1',
     '#options' => array( t('1'), t('2'), t('3'), t('4') , t('5') , t('6'), t('7')  , t('8')   , t('9')  , t('10')  ),
	 '#attributes' => [

        'name' => 'field_select_NV',
      ],
);

	$form['placa1'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 1',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [
   [
    ':input[name="field_select_NV"]' => ['value' => '0'],
  ],'or',
	  [
    ':input[name="field_select_NV"]' => ['value' => '1'],
  ],'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '2'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '0'],
  ],
	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '1'],
  ],
	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '2'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];
	$form['placa2'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 2',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '1'],
  ],'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '2'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
		  [
    ':input[name="field_select_NV"]' => ['value' => '1'],
  ],
	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '2'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];
		$form['placa3'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 3',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '2'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '2'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];
			$form['placa4'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 4',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '3'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];
	  		$form['placa5'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 5',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '4'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];

	  		$form['placa6'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 6',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '5'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];

	  		$form['placa7'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 7',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '6'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];

	  		$form['placa8'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 8',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [

	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '7'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];
		  		$form['placa9'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 9',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '8'],
  ],

	'or',

	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];


		  		$form['placa10'] = [
  '#type' => 'textfield',
  '#title' => 'Ingresar la Placa # 10',

//'#required' => TRUE,

'#states' =>array(

		 'required' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],



	  ],

  'visible' => [
	  [
    ':input[name="field_select_NV"]' => ['value' => '9'],
  ],

]
),
];


  $form['valor_evento'] = array(
    '#type' => 'number',
    '#title' => 'Valor Total de Inversión de la Publicidad Movil',
    '#width' => '30%',
    '#align' => 'center',
    '#required' => true,
    '#maxlength' =>10
);





    $form['s2'] = [
      '#type' => 'markup',
      '#markup' => '<hr><p><div>Información de quien Realiza la liquidación</div></p>',
    ];


    $form['id_document'] = [
      '#type' => 'textfield',
     '#required' => TRUE,
      '#title' => 'Documento de Identidad ',
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
       '#title' => 'Nombre  ',
      '#description' => 'Nombre de la persona natural.',
    ];



    $form['dir_correspondencia'] = [
      '#type' => 'textfield',
    '#required' => TRUE,
       '#title'=> 'Dirección de Correspondencia.',
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
/*
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
    Cargar todos los documentos en formato PDF con un tamaño de archivo inferior a : 2MB.
      </p>',
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
      '#description' => 'Documento de Identidad (Cédula Ciudadanía, Cédula de Extranjería, Pasaporte). Límite: 2MB./ PDF',
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
      '#title' => t('Evidencia Inversión'),
      '#size' => 20,
      '#description' => 'Certificación , Cotización o Factura del valor invertido en publicidad a evaluar : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
    );


    $form['soportes4'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Tarjeta de Propiedad'),
      '#size' => 20,
      '#description' => 'Tarjeta de Propiedad del vehículo : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
    );

  $form['soportes5'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
		'#required' => TRUE,
      '#title' => t('Fotografías del vehículo con la publicidad'),
      '#size' => 20,
      '#description' => 'Fotografías : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
    );
*/
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
	//'#required' => TRUE,
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
    return 'Liquidador1_Form_publicidad_movil';
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

  $vocabulary_name = 'smlv';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);


    foreach ($terms as $term) {
      $id2 = $term->getFields();
          $value  = $term->get('field_smlv2021')->getValue();

    }





/*

Valores  obtebidos para la firma

**/
/*

Valores  obtebidos para la Liquidación

**/
$valor1 =$value[0]["value"];
$valor = number_format($valor1, 2, ',', '.');
/*

Valores  obtebidos para la Informacion de la factura

**/
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
$file1 = $form_state->getValue('soportes1');
$file2 = $form_state->getValue('soportes2');
$file3 = $form_state->getValue('soportes3');
$file4 = $form_state->getValue('soportes4');
	  $file5 = $form_state->getValue('soportes5');
$codigo_liquidacion = $form_state->getValue('id_document');
$barrio_liquidacion = $form_state->getValue('barrio');

$numero_vehiculos  = $form_state->getValue('field_select_NV');

$tipo_solicitante = $form_state->getValue('tipo_de_solicitante');
$id_contribuyente = $form_state->getValue('id_document');
$name_contrib= $form_state->getValue('name');

$numero_dias =  $form_state->getValue('numero_meses');

$numero_vehiculos =  $form_state->getValue('field_select_NV')+1;
$valor_evento =  $form_state->getValue('valor_evento');
$valor_liquidacion =  $form_state->getValue('valor_evento');

$valor_evento = number_format($valor_evento, 2, ',', '.');



    if ($valor_liquidacion  <= $valor_tarifa_evento_25) {
      $valor_tarifa = 172702;
      $valor_liquidacion = 172702 *  $numero_dias ;
      $valor_liquidacion_r = 118600*  $numero_dias ;
    } elseif ($valor_liquidacion  > $valor_tarifa_evento_25  && $valor_liquidacion <= $valor_tarifa_evento_35) {
        $valor_tarifa = 242129;
      $valor_liquidacion = 242129 *  $numero_dias ;
      $valor_liquidacion_r = 166200 *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_35  && $valor_liquidacion <= $valor_tarifa_evento_50) {
        $valor_tarifa =328121;
      $valor_liquidacion =328121  *  $numero_dias;
      $valor_liquidacion_r = 237600 *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_70  && $valor_liquidacion <= $valor_tarifa_evento_100) {
        $valor_tarifa =459633;
      $valor_liquidacion = 459633  *  $numero_dias ;
      $valor_liquidacion_r =  332850 *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_100  && $valor_liquidacion <= $valor_tarifa_evento_200) {
        $valor_tarifa =  656900;
      $valor_liquidacion = 656900  *  $numero_dias ;
      $valor_liquidacion_r =  475700  *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_200  && $valor_liquidacion <= $valor_tarifa_evento_300) {
        $valor_tarifa =  1314458;
      $valor_liquidacion = 1314458 * $numero_dias;
      $valor_liquidacion_r =  951800  *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_300  && $valor_liquidacion <= $valor_tarifa_evento_400) {
        $valor_tarifa = 1972015;
     $valor_liquidacion = 1972015 *  $numero_dias;
     $valor_liquidacion_r =  1428000  *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_400  && $valor_liquidacion <= $valor_tarifa_evento_500) {
        $valor_tarifa = 2629573;
     $valor_liquidacion =  2629573  *  $numero_dias ;
     $valor_liquidacion_r =  1904150  *  $numero_dias ;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_500  && $valor_liquidacion <= $valor_tarifa_evento_700) {
        $valor_tarifa = 3287130;
      $valor_liquidacion =  3287130  *  $numero_dias  ;
      $valor_liquidacion_r = 2380300  *  $numero_dias;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_700  && $valor_liquidacion <= $valor_tarifa_evento_900) {
        $valor_tarifa =4602245;
     $valor_liquidacion = 4602245*  $numero_dias ;
     $valor_liquidacion_r = 3332600 *  $numero_dias;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_900  && $valor_liquidacion <= $valor_tarifa_evento_1500) {
        $valor_tarifa = 5917360;
      $valor_liquidacion = 5917360  *  $numero_dias ;
      $valor_liquidacion_r = 4284900 *  $numero_dias;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_1500  && $valor_liquidacion <= $valor_tarifa_evento_2115) {
        $valor_tarifa =98627064;
     $valor_liquidacion = 98627064  *  $numero_dias ;
     $valor_liquidacion_r = 98627060 *  $numero_dias;
    }elseif ($valor_liquidacion  > $valor_tarifa_evento_2115  && $valor_liquidacion <= $valor_tarifa_evento_8458) {
      $valor_tarifa =14669885;
      $valor_liquidacion = 14669885 *  $numero_dias;
      $valor_liquidacion_r = 10917550 *  $numero_dias;
    }else {
      $valor_tarifa =($valor_evento * 10)/100;
      $valor_liquidacion = ( ($valor_evento * 10)/100) ;
    }
     $valor = $valor_liquidacion;

    $valor_tarifa = number_format($valor_tarifa, 2, ',', '.');
    $valor_liquidacion = number_format($valor_liquidacion, 2, ',', '.');
    $valor_liquidacion_r = number_format($valor_liquidacion_r, 2, ',', '.');

$dir_correspondecia_contrib = $form_state->getValue('dir_correspondencia');
$email_cotrib = $form_state->getValue('email');
$tfijo = $form_state->getValue('tfijo');
$tmovil = $form_state->getValue('tmovil');
$estrato = $form_state->getValue('estrato');
$condicion = $form_state->getValue('condicion');
	  $placas = "";

$placa1 = $form_state->getValue('placa1');
$placa2 = $form_state->getValue('placa2');
$placa3 = $form_state->getValue('placa3');
$placa4 = $form_state->getValue('placa4');
$placa5 = $form_state->getValue('placa5');
$placa6 = $form_state->getValue('placa6');
$placa7 = $form_state->getValue('placa7');
$placa8 = $form_state->getValue('placa8');
$placa9 = $form_state->getValue('placa9');
$placa10 = $form_state->getValue('placa10');

	if (!empty($placa1)) {
        $placas = $placas."/".$placa1 ;
    }

 	if (!empty($placa2)) {
        $placas = $placas."/".$placa2 ;
    }
	 	if (!empty($placa3)) {
        $placas = $placas."/".$placa3 ;
    }
	  	if (!empty($placa4)) {
        $placas = $placas."/".$placa4 ;
    }
	 	if (!empty($placa5)) {
        $placas = $placas."/".$placa5 ;
    }
	    	if (!empty($placa6)) {
        $placas = $placas."/".$placa6 ;
    }
	   	if (!empty($placa7)) {
        $placas = $placas."/".$placa7 ;
    }   	if (!empty($placa8)) {
        $placas = $placas."/".$placa8 ;
    }
	    	if (!empty($placa9)) {
        $placas = $placas."/".$placa9 ;
    }
	  	if (!empty($placa10)) {
        $placas = $placas."/".$placa10 ;
    }
/*
$my_article = Node::create(['type' => 'factura']);
         $my_article->set('title', $codigo_liquidacion);
         $my_article->set('field_valor', $valor);
	     $my_article->set('field_concepto_ambiental_liq', "Móvil");
         $my_article->set('field_barrio_liquidacion', $barrio_liquidacion);
         $my_article->set('field_cantidad_vehiculos', $numero_vehiculos);
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
	     $my_article->set('field_rut_file', $file2);
       $my_article->set('field_ei_file', $file3);
       $my_article->set('field_tarjeta_de_propiedad', $file4);
       $my_article->set('field_evidencia_foto', $file5);
	     $my_article->set('field_placas', $placas);
         $my_article->set('status', '0');
        // $my_article->set('uid', $id_contribuyente);

         $my_article->enforceIsNew();
           $my_article->save();


 $nid = $my_article->id();
       $node = \Drupal\node\Entity\Node::load($nid); //Accedemos a la información del nodo
	    /** Obteniendo el field_consecutivo_factura del nodo creado */
        //   $consecutivo_facturas = $node->get('field_consecutivo_factura')->getValue();
	      // $sec ="01"."0".$consecutivo_facturas[0]["value"]."2021";
	 // $node->set('field_codigo_liquidacion_factura', $sec); //valor liquidacion
    //  $node->set('title', $sec); //Accedemos al campo que queremos editar/modificar



   $html='

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
             <td rowspan="6">EPA | Zona Liquidaciones
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
       <p>SIMULACIÓN - VIAVILIDAD PARA PUBLICIDAD EXTERIOR VISUAL MÓVIL</p>
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
<td ><p>TELÉFONO:</p></td>
<td  colspan="3">
<p>'.$tmovil.'</p>
</td>
</tr>
           <tr>
             <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 107 de 17 de febrero de 2021</p></td>
             <td colspan="3">
             <p>$ '.$valor_tarifa.'</p>
             </td>
           </tr>
           <tr>
             <td><p>N° VEHÍCULOS</p></td>
             <td>
             <p>'.$numero_vehiculos.'</p>
             </td>
             <td>TOTAL LIQUIDACIÓN</td>
             <td >
             <p style="
    font-weight: bold;
">$ '.$valor_liquidacion_r.'</p>
             </td>
           </tr>
           <tr>
             <td colspan="4">
             <p>CONSIDERACIONES</p>

             <p>Categorización de profesionales con base en la Resolución 1280 de 2010 del MAVDT y afectados por un factor multiplicador Factor de administración de acuerdo a la resolución 212 de 2004 del MAVDT</p>

             </td>
           </tr>
           <tr>
             <td colspan="4">
             <p>CONCEPTO</p>

             <div class="concepto">
             <p class="concepto">VIAVILIDAD PARA PUBLICIDAD EXTERIOR VISUAL MÓVIL PARA UN NÚMERO DE VEHÍCULOS IGUAL A : '.$numero_vehiculos.' , SEGÚN SOLICITUD CON RADICADO #'.$sec.'</p> Para las pacas : '.$placas.', Con una Inversión de '.$valor_evento.'
             </div>
             </td>
           </tr>

         </tbody>
       </table>
       ';
	  
     /*  $node->set('body',$html);
       $node->body->format = 'full_html';
$node->save(); //Guarda el cambio una vez realizado
  // Specify 'to' and 'from' addresses.

  $code="4157709998461239"."8020".$sec."3900".$valor."96".date('Y')."1231";
  $code_content="(415)7709998461239"."(8020)".$sec."(3900)".$valor."(96)".date('Y')."1231";


 $to =$email_cotrib ;
  $from =  $this->config('system.site')->get('mail');
 $language_code = $this->languageManager->getDefaultLanguage()->getId();

  // Whether or not to automatically send the mail when we call mail() on the
  // mail manager. This defaults to TRUE, and is normally what you want unless
  // you need to do additional processing before the mail manager sends the
  // message.
  $send_now = TRUE;
  // Send the mail, and check for success. Note that this does not guarantee
  // message delivery; only that there were no PHP-related issues encountered
  // while sending.

*/


//$html = 'this is my <b>first</b>downloadable pdf';
$mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']);
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-L']);
$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$mpdf->SetHTMLHeader('
<div style="text-align: right; font-weight: bold;">
   EPA
</div>','O');

	  $mpdf->WriteHTML($html);
$file = $mpdf->Output('Simulacion.pdf', 'D');
/*
$params['attachments'][] = [
  'filecontent' => $file,
  'filename' => $sec.'.pdf',
  'filemime' => 'application/pdf',
];

 $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);




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

	  $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
           $form_state->setRedirectUrl($url);







  }

}