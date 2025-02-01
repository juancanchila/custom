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


class Liquidadorepa_FormRSJ extends FormBase
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
    return 'liquidadorepa_formrsj';
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

            $form['id_document_rsj'] = [
              '#type' => 'textfield',
              '#required' => TRUE,
              '#title' => 'NIT',
              '#default_value' => $this->session->get('session_liquidacion.id_document_rsj',''),
              ];

              $form['name_rsj'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'Razón Social ',
                '#description' => 'Razón Social de la entidad.',
                '#default_value' => $this->session->get('session_liquidacion.name_rsj', ''),
               ];
               $form['nombrelegal_rsj'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'Nombre de Representante legal ',
                '#description' => 'Razón Social de la entidad.',
                '#default_value' => $this->session->get('session_liquidacion.nombrelegal_rsj', ''),

               ];
               $form['idlegal_rsj'] = [
                '#type' => 'textfield',
                '#size' => '60',
                '#required' => TRUE,
                '#title' => 'N&#176; de Documento de Representante legal ',
                '#description' => 'Documento del Representante legal.',
                '#default_value' => $this->session->get('session_liquidacion.idlegal_rsj', ''),
               ];
               $form['dir_correspondencia_rsj'] = [
                '#type' => 'textfield',
                '#required' => TRUE,
                '#title' => 'Dirección de Correspondencia del Solicitante',
                '#default_value' => $this->session->get('session_liquidacion.dir_correspondencia_rsj', ''),
               ];
               $form['email_rsj'] = [
                '#type' => 'email',
               '#required' => TRUE,
                '#title' => 'Correo Electrónico',
                '#default_value' => $this->session->get('session_liquidacion.email_rsj', ''),
               ];


               $form['tfijo_rsj'] = [
                '#type' => 'textfield',
                '#title' => 'Teléfono fijo',
                '#default_value' => $this->session->get('session_liquidacion.tfijo_rsj', ''),
                ];


                $form['tmovil_rsj'] = [
                  '#type' => 'textfield',
                  '#required' => TRUE,
                  '#title' => 'Teléfono móvil',
                  '#default_value' => $this->session->get('session_liquidacion.tmovil_rsj', ''),
                 ];

                /* $form['estrato'] = array(
                  '#title' => t('Estrato'),
                  '#default_value' => $this->session->get('session_liquidacion.estrato_rsj', ''),
                  '#type' => 'select',
                     '#description' => 'Seleccionar el estrato de quien realiza la liquidación.',
                  '#options' => array(t('--- Seleccionar ---'), t('1'), t('2'), t('3'), t('4') , t('5') , t('6')    ),
                 );

                 $form['condicion'] = array(
                    '#title' => t('¿Se encuentra en alguna condición especial?'),
                    '#default_value' => $this->session->get('session_liquidacion.condicion_rsj', ''),
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


    $valor_liquidacion =$form_state->getValue('valor_evento_rsj');
    $numero_dias = $form_state->getValue('numero_dias_rsj');

    if ($valor_liquidacion < $valor_tarifa_evento_25) {
      $valor_tarifa = 172702;//ok
      $valor_liquidacion = 172702 *  $numero_dias ;
      $valor_liquidacion_r = 118600 *  $numero_dias ;
    } elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
        $valor_tarifa = 242129;//ok
      $valor_liquidacion = 242129 *  $numero_dias ;
      $valor_liquidacion_r = 166200 *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_35  && $valor_liquidacion < $valor_tarifa_evento_50) {
        $valor_tarifa =346098;
      $valor_liquidacion =346098  *  $numero_dias;
      $valor_liquidacion_r = 237600 *  $numero_dias ;

    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_50  && $valor_liquidacion < $valor_tarifa_evento_70 ) {
        $valor_tarifa =484837;
      $valor_liquidacion = 484837  *  $numero_dias ;
      $valor_liquidacion_r =  332850 *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_70  && $valor_liquidacion < $valor_tarifa_evento_100) {
        $valor_tarifa =  693004;
      $valor_liquidacion = 693004  *  $numero_dias ;
      $valor_liquidacion_r =  475700  *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_100  && $valor_liquidacion < $valor_tarifa_evento_200) {
        $valor_tarifa =  1386587;
      $valor_liquidacion = 1386587 * $numero_dias;
      $valor_liquidacion_r =  951800  *  $numero_dias ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_200  && $valor_liquidacion < $valor_tarifa_evento_300) {
        $valor_tarifa = 2080284;
     $valor_liquidacion = 2080284 *  $numero_dias;
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
      $valor_tarifa =14669885;
      $valor_liquidacion = 14669885 *  $numero_dias;
      $valor_liquidacion_r = 10917550 *  $numero_dias;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_2115  && $valor_liquidacion < $valor_tarifa_evento_8458) {
      $valor_tarifa =14669885;
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


    $this->setSessionValue('session_liquidacion.valorLiquidacion_rsj', $valor_liquidacion );

    $this->setSessionValue('session_liquidacion.valor_tarifa_rsj',  $valor_tarifa  );




    $f110= strtotime($form_state->getValue('fecha_Inicial'));
	  $dt=\Drupal::time()->getCurrentTime();
	  $diff02 =($f110-$dt)/86400;

     if ($diff02 < 10){
      $alert='<div class="alertaproximidad">Su fecha es menor a 10 días ,al enviar la solicitud de viabilidad acepta el riezgo de que su tramite no se aceptado</div>';
      /*$this->messenger()->addStatus($this->t('Su fecha es menor a 10 días , el tramite se realizará con riesgo de no ser aceptado', ['@title' =>$N]));*/
      $this->setSessionValue('session_liquidacion.alert_rsj', $alert);
}else{
  $alert='';
  $this->setSessionValue('session_liquidacion.alert_rsj', $alert);
}


    $format = 'Y-m-d';
	 $cantidad_dias=  $form_state->getValue('numero_dias_rsj');
    $f1= strtotime($form_state->getValue('fecha_Inicial_rsj'));
    $f_limit=strtotime($form_state->getValue('fecha_Final_rsj'));
	 $diff =($f_limit-$f1)/86400;
	 $diff2 =($f1-strtotime(\Drupal::service('date.formatter')))/86400;
     if ($f1 > $f_limit){
    $form_state->setErrorByName('fecha_inicial_rsj', $this->t('La fecha inicial no puede ser menor a la final '));
}

	      if ($cantidad_dias != $diff){
    $form_state->setErrorByName('fecha_inicial_rsj', $this->t('La Cantidad de días no es correcta '. $diff));

			   $form_state->setValue('s0', 'test');
}

/**
$limite=strtotime("2022-12-31");
if ($f_limit> $limite){
  $form_state->setErrorByName('fecha_Final_rsj', $this->t('La fecha inicial no puede ser superior al 31 de diciembre del presente año '));
}
*/

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
            'name' => $form_state->getValue('name_rsj'),

             ])

             ->set('page_num', 2)

             ->setRebuild(TRUE);
                 $this->setSessionValue('session_liquidacion.id_document_rsj', $form_state->getValue('id_document_rsj'));
                 $this->setSessionValue('session_liquidacion.name_rsj', $form_state->getValue('name_rsj'));
                 $this->setSessionValue('session_liquidacion.dir_correspondencia_rsj', $form_state->getValue('dir_correspondencia_rsj'));
                 $this->setSessionValue('session_liquidacion.email_rsj', $form_state->getValue('email_rsj'));
                /* $this->setSessionValue('session_liquidacion.estrato_rsj', $form_state->getValue('first_estrato'));*/
                 $this->setSessionValue('session_liquidacion.tmovil_rsj', $form_state->getValue('tmovil_rsj'));
                 $this->setSessionValue('session_liquidacion.tfijo_rsj', $form_state->getValue('tfijo_rsj'));
                 $this->setSessionValue('session_liquidacion.nombrelegal_rsj', $form_state->getValue('nombrelegal_rsj'));
                 $this->setSessionValue('session_liquidacion.idlegal_rsj', $form_state->getValue('idlegal_rsj'));

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
      $this->setSessionValue('session_liquidacion.descripcion_evento_rsj', $form_state->getValue('descripcion_evento_rsj'));
  $this->setSessionValue('session_liquidacion.barrio_rsj', $form_state->getValue('barrio_rsj'));
  $this->setSessionValue('session_liquidacion.valor_evento_rsj', $form_state->getValue('valor_evento_rsj'));
  $this->setSessionValue('session_liquidacion.numero_dias_rsj', $form_state->getValue('numero_dias_rsj'));
  $this->setSessionValue('session_liquidacion.direccion_evento_rsj', $form_state->getValue('direccion_evento_rsj'));

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
      $this->setSessionValue('session_liquidacion.soportes1_rsj', $form_state->getValue('soportes1_rsj'));
      $this->setSessionValue('session_liquidacion.soportes2_rsj', $form_state->getValue('soportes2_rsj'));
      $this->setSessionValue('session_liquidacion.soportes3_rsj', $form_state->getValue('soportes3_rsj'));
      $this->setSessionValue('session_liquidacion.soportes4_rsj', $form_state->getValue('soportes4_rsj'));
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

    $form['description_rsj'] = [
      '#type' => 'item',
      '#title' => $this->t('Detalles del Evento'),
		 '#default_value' => $this->session->get('session_liquidacion.description_rsj',''),
    ];


    $form['barrio_rsj'] = [
      '#type' => 'textfield',
      '#title' => 'Barrio',
      '#required' => TRUE,
      '#description' => 'Ingresar el barrio de Cartagena de Indias de  donde se realizará el Evento.',
	  '#default_value' => $this->session->get('session_liquidacion.barrio_rsj',''),
    ];


      $form['s1'] = [
        '#type' => 'markup',
        '#markup' => '<hr>',
      ];


        $form['valor_evento_rsj'] = array(
      '#type' => 'number',
    '#title' => $this->t('Valor Inversión'),
      '#description' => 'Ingresar el valor de la inversión en el montaje del evento a evaluar sin espacios, si puntos ni comas. ',
      '#width' => '30%',
      '#align' => 'center',
      '#required' => true,
      '#maxlength' =>10,
			'#default_value' => $this->session->get('session_liquidacion.valor_evento_rsj',''),
  );

    $form['fecha_Inicial_rsj'] = array (
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
  $form['fecha_Final_rsj'] = array (
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

    $form['numero_dias_rsj'] = array(
      '#type' => 'number',
      '#title' => 'Ingresar duración del evento en días',
      '#width' => '30%',
      '#align' => 'center',
      '#required' => true,
      '#maxlength' => 3,
		'#default_value' => $this->session->get('session_liquidacion.numero_dias_rsj',''),
  );




      $form['descripcion_evento_rsj'] = [
        '#type' => 'textfield',
        '#title' => 'Breve Descripción del Evento',
       '#required' => TRUE,
		  '#default_value' => $this->session->get('session_liquidacion.descripcion_evento_rsj',''),
      ];
      $form['direccion_evento_rsj'] = [
        '#type' => 'textfield',
        '#title' => 'Dirección donde se realizará el Evento',
      '#required' => TRUE,
		    '#default_value' => $this->session->get('session_liquidacion.direccion_evento_rsj',''),
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

    $form['soportes1_rsj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	  //'#required' => TRUE,
      '#title' => t('Documento Identidad'),
      '#size' => 20,
      '#description' => 'Documento de Identidad (Cédula Ciudadanía, Cédula de Extranjería, Pasaporte). Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes1_rsj',''),
    );

    $form['soportes2_rsj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	   	//'#required' => TRUE,
      '#title' => t('RUT'),
      '#size' => 20,
      '#description' => 'Registro Único Tributario - RUT : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes2_rsj',''),
    );

    $form['soportes4_rsj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
     //'#required' => TRUE,
      '#title' => t('Representación Legal'),
      '#size' => 20,
      '#description' => 'Certificado de existencia y representación legal: Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes4_rsj',''),
    );


    $form['soportes3_rsj'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
	  //	'#required' => TRUE,
      '#title' => t('Evidencia Inversión'),
      '#size' => 20,
      '#description' => 'Certificación , Cotización o Factura del valor invertido en publicidad a evaluar : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes3_rsj',''),
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
     $id= $this->session->get('session_liquidacion.id_document_rsj', '');
     $email=$this->session->get('session_liquidacion.email_rsj', '');
     $Cantidad=$this->session->get('session_liquidacion.valor_evento_rsj', '');
     $Cantidad = number_format($Cantidad, 2, ',', '.');
     $tarifa =  $this->session->get('session_liquidacion.valor_tarifa_rsj', '');
     $total_liquidacion = $this->session->get('session_liquidacion.valorLiquidacion_rsj', '');


     $alert=$this->session->get('session_liquidacion.alert_rsj', '');
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
      'session_liquidacion.name_rsj',
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