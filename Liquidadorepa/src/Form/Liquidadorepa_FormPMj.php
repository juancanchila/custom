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


class Liquidadorepa_FormPMj extends FormBase
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

  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager, EmailValidator $email_validator, SessionInterface $session, CacheTagsInvalidatorInterface $invalidator)
  {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
    $this->emailValidator = $email_validator;
    $this->session = $session;
    $this->cacheTagInvalidator = $invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
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
    return 'liquidadorepa_formpmj';
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

    $form['id_document'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => 'NIT',
      '#default_value' => $this->session->get('session_liquidacion.id_document_pmj', ''),
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
      '#title' => 'Razón Social ',
      '#description' => 'Razón Social de la entidad.',
      '#default_value' => $this->session->get('session_liquidacion.name_pmj', ''),
    ];
    $form['nombrelegal'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
      '#title' => 'Nombre de Representante legal ',
      '#description' => 'Razón Social de la entidad.',
      '#default_value' => $this->session->get('session_liquidacion.nombrelegal_pmj', ''),

    ];
    $form['idlegal'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#required' => TRUE,
      '#title' => 'N&#176; de Documento de Representante legal ',
      '#description' => 'Documento del Representante legal.',
      '#default_value' => $this->session->get('session_liquidacion.idlegal_pmj', ''),
    ];
    $form['dir_correspondencia'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => 'Dirección de Correspondencia del Solicitante',
      '#default_value' => $this->session->get('session_liquidacion.dir_correspondencia_pmj', ''),
    ];
    $form['email'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => 'Correo Electrónico',
      '#default_value' => $this->session->get('session_liquidacion.email_pmj', ''),
    ];


    $form['tfijo'] = [
      '#type' => 'textfield',
      '#title' => 'Teléfono fijo',
      '#default_value' => $this->session->get('session_liquidacion.tfijo_pmj', ''),
    ];


    $form['tmovil'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => 'Teléfono móvil',
      '#default_value' => $this->session->get('session_liquidacion.tmovil_pmj', ''),
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
  protected function setSessionValue($key, $value)
  {
    if (empty($value)) {
      // If the value is an empty string, remove the key from the session.
      $this->session->remove($key);
    } else {
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



    $valor_tarifa =  $this->session->get('session_liquidacion.tarifa_pmj', '');
    $valor_liquidacion  = $this->session->get('session_liquidacion.valor_liquidacion_pmj', '');
    $valor_evento =  $this->session->get('session_liquidacion.valor_evento_pmj', '');
    $valor_evento = number_format($valor_evento, 2, ',', '.');
    $placas =  $this->session->get('session_liquidacion.placas_pmj', '');
    $dir_correspondecia_contrib = $this->session->get('session_liquidacion.dir_correspondencia_pmj', '');
    $email_cotrib = $this->session->get('session_liquidacion.email_pmj', '');
    $tfijo = $this->session->get('session_liquidacion.tfijo_pmj', '');
    $tmovil =  $this->session->get('session_liquidacion.tmovil_pmj', '');
    $estrato = $this->session->get('session_liquidacion.estrato_pmj', '');
    $condicion =  $this->session->get('session_liquidacion.condicion_pmj', '');


    $codigo_liquidacion = $this->session->get('session_liquidacion.id_document_pmj', '');
    $barrio_liquidacion = $this->session->get('session_liquidacion.barrio_pmj', '');

    $tipo_solicitante = "Persona Jurídica";
    $id_contribuyente = $this->session->get('session_liquidacion.id_document_pmj', '');
    $name_contrib = $this->session->get('session_liquidacion.name_pmj', '');

    $numero_dias = $this->session->get('session_liquidacion.numero_meses_pmj', '');
    $numero_vehiculos =$this->session->get('session_liquidacion.field_select_NV_pmj', '') ;

    $my_article = Node::create(['type' => 'liquidacion']);
    $my_article->set('title', $codigo_liquidacion);
    $my_article->set('field_valor',  $valor_liquidacion);
    $my_article->set('field_concepto_ambiental_liq', "Móvil");
    $my_article->set('field_barrio_liquidacion', $barrio_liquidacion);
    $my_article->set('field_cantidad_vehiculos', $numero_vehiculos);
    $my_article->set('field_tipo_de_solicitante', $tipo_solicitante);
    $my_article->set('field_id_contribuyente', $id_contribuyente);
    $my_article->set('field_nombre_contribuyente', $name_contrib);
    $my_article->set('field_email_contribuyente', $email_cotrib);
    $my_article->set('field_telefono_fijo_contribuyent', $tfijo);
    $my_article->set('field_telefono_movil_contribuyen', $tmovil);
    $my_article->set('field_estrato_contribuyente', $estrato);
    $my_article->set('field_condicion_contribuyente', $condicion);
    $my_article->set('field_comparado_factura', false);
    //$my_article->set('field_codigo_liquidacion_factura', false);
    $my_article->set('field_estado', FALSE);

    $my_article->set('field_id_file', $file1);
    $my_article->set('field_rut_file', $file2);
    $my_article->set('field_ei_file', $file3);
    $my_article->set('field_tarjeta_de_propiedad', $file4);
    $my_article->set('field_evidencia_foto', $file5);
    //$my_article->set('field_placas', $placas);
    $my_article->set('field_detalleplacas', $placas);
    $my_article->set('status', '0');
    // $my_article->set('uid', $id_contribuyente);

    $my_article->enforceIsNew();
    $my_article->save();


    $nid = $my_article->id();
    $node = \Drupal\node\Entity\Node::load($nid); //Accedemos a la información del nodo
    /** Obteniendo el field_consecutivo_factura del nodo creado */
    $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
    $sec = "03" . "0" . $consecutivo_facturas[0]["value"] . "2021";
    //$node->set('field_codigo_liquidacion_factura', $sec); //valor liquidacion
    $node->set('title', $sec); //Accedemos al campo que queremos editar/modificar



    $html = '

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
    <p>Liquidación No ' . $sec . '</p>
    </td>
  </tr>
  <tr>
  <td ><p>FECHA:</p></td>
  <td  colspan="3">
  <p>' . date("Y/m/d") . '</p>
  </td>
</tr>
<tr>
<td ><p>ASUNTO:</p></td>
<td  colspan="3">
<p>VIABILIDAD PARA PUBLICIDAD EXTERIOR VISUAL MÓVIL</p>
</td>
</tr>
<tr>
<td ><p>PETICIONARIO / EMPRESA:</p></td>
<td  colspan="3">
<p>' . $name_contrib . '</p>
</td>

</tr>
<tr>
<td ><p>Id Solicitante:</p></td>

<td  colspan="3">
<p>' . $id_contribuyente . '</p>
</td>
</tr>

<tr>
<td ><p>DIRECCION:</p></td>
<td  colspan="3">
<p>' . $dir_correspondecia_contrib . '</p>
</td>
</tr>
<tr>
<td ><p>CORREO:</p></td>
<td  colspan="3">
<p>' . $email_cotrib . '</p>
</td>
</tr>
<tr>
<td ><p>TELÉFONO:</p></td>
<td  colspan="3">
<p>' . $tmovil . '</p>
</td>
</tr>
  <tr>
    <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 107 de 17 de febrero de 2021</p></td>
    <td colspan="3">
    <p>$ ' . $valor_tarifa . '</p>
    </td>
  </tr>
  <tr>
    <td><p>N° VEHÍCULOS</p></td>
    <td>
    <p>' . $numero_vehiculos . '</p>
    </td>
    <td>TOTAL LIQUIDACIÓN</td>
    <td >
    <p style="
font-weight: bold;
">$ ' . $valor_liquidacion . '</p>
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
    <p class="concepto">VIABILIDAD PARA PUBLICIDAD EXTERIOR VISUAL MÓVIL PARA UN NÚMERO DE VEHÍCULOS IGUAL A : ' . $numero_vehiculos . ' , SEGÚN SOLICITUD CON #' . $sec . '</p> Para las placas : ' . $placas . ', Con una Inversión de ' . $valor_evento . '
    </div>
    </td>
  </tr>

</tbody>
</table>
';
    $node->set('body', $html);
    $node->body->format = 'full_html';
    $node->save(); //Guarda el cambio una vez realizado
    // Specify 'to' and 'from' addresses.

    $valor = $this->session->get('session_liquidacion.valorLiquidacion_pdf','');
    $code = "4157709998461239" . "8020" . $sec . "3900" . $valor . "96" . date('Y') . "1231";
    $code_content = "(415)7709998461239" . "(8020)" . $sec . "(3900)" . $valor . "(96)" . date('Y') . "1231";

    $mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']);
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-L']);
    $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
    $mpdf->SetHTMLHeader('
<div style="text-align: right; font-weight: bold;">
   EPA
</div>', 'O');

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
<td align="center">EAN128A [A/B/C]</td>
<td>' . $code_content . '</td>
<td class="barcodecell"><barcode code="' . $code . '" type="EAN128A" class="barcode" /></td>
</tr>
</tbody>
</table>

');
    $mpdf->WriteHTML($html);
    $file = $mpdf->Output($sec . '.pdf', 'D');

    $params['attachments'][] = [
      'filecontent' => $file,
      'filename' => $sec . '.pdf',
      'filemime' => 'application/pdf',
    ];


    //$params['mail_title'] = 'Cobros';
    // $params['body'] = 'Gracias!';
    //$params['message'] = 'Gracias!';

    /////////////////////
    /* $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);

 if ($result['result'] == TRUE) {
 $this->messenger()->addStatus($this->t('El documento se ha generado y se ha enviado un correo con lmas instrucciones, de lo contrario favor comunacar con el correo: info@liquidaciones.epacartagena.gov.co'));
 $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>1]);
$form_state->setRedirectUrl($url);
}else {
  $this->messenger()->addMessage($this->t('Mensaje no enviado validar dirección de correo!.'), 'error');
}
*/

    $this->invalidateCacheTag();
    $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => 1]);
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
  public function fapiExampleMultistepFormNextValidate(array &$form, FormStateInterface $form_state)
  {
    //placas


/*
    $placa1 = $this->session->get('session_liquidacion.placa1_pmj', '');
    $placa2 = $this->session->get('session_liquidacion.placa2_pmj', '');
    $placa3 = $this->session->get('session_liquidacion.placa3_pmj', '');
    $placa4 = $this->session->get('session_liquidacion.placa4_pmj', '');
    $placa5 = $this->session->get('session_liquidacion.placa5_pmj', '');
    $placa6 = $this->session->get('session_liquidacion.placa6_pmj', '');
    $placa7 = $this->session->get('session_liquidacion.placa7_pmj', '');
    $placa8 = $this->session->get('session_liquidacion.placa8_pmj', '');
    $placa9 = $this->session->get('session_liquidacion.placa9_pmj', '');
    $placa10 = $this->session->get('session_liquidacion.placa10_pmj', '');

    $cantidad = 0;

    if ($placa1 != "") {
      $placas = $placas . "/" . $placa1;
      $cantidad = $cantidad + 1;
    }

    if ($placa2 != "") {
      $placas = $placas . "/" . $placa2;
      $cantidad = $cantidad + 1;
    }
    if ($placa3 != "") {
      $placas = $placas . "/" . $placa3;
      $cantidad = $cantidad + 1;
    }
    if ($placa4 != "") {
      $placas = $placas . "/" . $placa4;
      $cantidad = $cantidad + 1;
    }
    if ($placa5 != "") {
      $placas = $placas . "/" . $placa5;
      $cantidad = $cantidad + 1;
    }
    if ($placa6 != "") {
      $placas = $placas . "/" . $placa6;
      $cantidad = $cantidad + 1;
    }
    if ($placa7 != "") {
      $placas = $placas . "/" . $placa7;
      $cantidad = $cantidad + 1;
    }
    if ($placa8 != "") {
      $placas = $placas . "/" . $placa8;
      $cantidad = $cantidad + 1;
    }
    if ($placa9 != "") {
      $placas = $placas . "/" . $placa9;
      $cantidad = $cantidad + 1;
    }
    if ($placa10 != "") {
      $placas = $placas . "/" . $placa10;
      $cantidad = $cantidad + 1;
    }
*/

    //$this->setSessionValue('session_liquidacion.placas_pmj', $placas);
    // calculos
 //   $this->setSessionValue('session_liquidacion.field_select_NV_pmj1', $cantidad);

    $vocabulary_name = 'smlv';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);


    foreach ($terms as $term) {
      $id2 = $term->getFields();
      $value  = $term->get('field_smlv')->getValue();
    }
    $valor1 = $value[0]["value"];
    $valor = number_format($valor1, 2, ',', '.');

    $valor_tarifa_evento_25 = $valor1 * 25;
    $valor_tarifa_evento_35 = $valor1 * 35;
    $valor_tarifa_evento_50 = $valor1 * 50;
    $valor_tarifa_evento_70 = $valor1 * 70;
    $valor_tarifa_evento_100 = $valor1 * 100;
    $valor_tarifa_evento_200 = $valor1 * 200;
    $valor_tarifa_evento_300 = $valor1 * 300;
    $valor_tarifa_evento_400 = $valor1 * 400;
    $valor_tarifa_evento_500 = $valor1 * 500;
    $valor_tarifa_evento_700 = $valor1 * 700;
    $valor_tarifa_evento_900 = $valor1 * 900;
    $valor_tarifa_evento_1500 = $valor1 * 1500;
    $valor_tarifa_evento_2115 = $valor1 * 2115;
    $valor_tarifa_evento_8458 = $valor1 * 8458;




    $cantidad = intval(  $this->session->get('session_liquidacion.field_select_NV_pmj', ''));
    $numero_dias = $form_state->getValue('numero_meses_pmj');

    $valor_liquidacion =  $form_state->getValue('valor_evento_pmj');



    if ($valor_liquidacion < $valor_tarifa_evento_25) {
      $valor_tarifa = 163732;//ok
      $valor_liquidacion = 163732 *  $numero_dias * $cantidad ;
      $valor_liquidacion_r = 118600 *   $numero_dias * $cantidad ;
    } elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
        $valor_tarifa = 229488;//ok
      $valor_liquidacion = 229488 *   $numero_dias * $cantidad ;
      $valor_liquidacion_r = 166200 *  $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_35  && $valor_liquidacion < $valor_tarifa_evento_50) {
        $valor_tarifa =328121;
      $valor_liquidacion =328121  *   $numero_dias * $cantidad ;
      $valor_liquidacion_r = 237600 *  $numero_dias * $cantidad ;

    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_50  && $valor_liquidacion < $valor_tarifa_evento_70 ) {
        $valor_tarifa =459633;
      $valor_liquidacion = 459633  *   $numero_dias * $cantidad ;
      $valor_liquidacion_r =  332850 *  $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_70  && $valor_liquidacion < $valor_tarifa_evento_100) {
        $valor_tarifa =  656900;
      $valor_liquidacion = 656900  *  $numero_dias * $cantidad ;
      $valor_liquidacion_r =  475700  *   $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_100  && $valor_liquidacion < $valor_tarifa_evento_200) {
        $valor_tarifa =  1314458;
      $valor_liquidacion = 1314458 *  $numero_dias * $cantidad ;
      $valor_liquidacion_r =  951800  *   $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_200  && $valor_liquidacion < $valor_tarifa_evento_300) {
        $valor_tarifa = 1972015;
     $valor_liquidacion = 1972015 *  $numero_dias * $cantidad ;
     $valor_liquidacion_r =  1428000  *   $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_300  && $valor_liquidacion < $valor_tarifa_evento_400) {
        $valor_tarifa = 2629573;
     $valor_liquidacion =  2629573  *  $numero_dias * $cantidad ;
     $valor_liquidacion_r =  1904150  *  $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_400  && $valor_liquidacion < $valor_tarifa_evento_500) {
        $valor_tarifa = 3287130;
      $valor_liquidacion =  3287130  *   $numero_dias * $cantidad ;
      $valor_liquidacion_r = 2380300  *   $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_500  && $valor_liquidacion < $valor_tarifa_evento_700) {
        $valor_tarifa =4602245;
     $valor_liquidacion = 4602245*   $numero_dias * $cantidad ;
     $valor_liquidacion_r = 3332600 *   $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_700  && $valor_liquidacion < $valor_tarifa_evento_900) {
        $valor_tarifa = 5917360;
      $valor_liquidacion = 5917360  *  $numero_dias * $cantidad ;
      $valor_liquidacion_r = 4284900 *  $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_900  && $valor_liquidacion < $valor_tarifa_evento_1500) {
        $valor_tarifa =98627064;
     $valor_liquidacion = 98627064  *  $numero_dias * $cantidad ;
     $valor_liquidacion_r = 98627060 *  $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion >= $valor_tarifa_evento_1500  && $valor_liquidacion < $valor_tarifa_evento_2115) {
      $valor_tarifa =13906685;
      $valor_liquidacion = 13906685 *  $numero_dias * $cantidad ;
      $valor_liquidacion_r = 10917550 *   $numero_dias * $cantidad ;
    }elseif ($valor_liquidacion  >= $valor_tarifa_evento_2115  && $valor_liquidacion < $valor_tarifa_evento_8458) {
      $valor_tarifa =13906685;
      $valor_liquidacion =37374939 *  $numero_dias * $cantidad ;
      $valor_liquidacion_r =37374939 *   $numero_dias * $cantidad ;

	}else {
      /*$valor_tarifa =($valor_evento * 0.4)/100;
      $valor_liquidacion = ( ($valor_evento * 0.4)/100) ;*/
	  $valor_tarifa =208879615;
     /* $valor_liquidacion =37374939 *  $numero_dias;
      $valor_liquidacion_r =37374939 *  $numero_dias;*/
    }
    /*
    $valor= $valor_liquidacion *  $numero_vehiculos;
    $valor_liquidacion =$valor_liquidacion *  $numero_vehiculos;
*/
    // definir las variables de sesion
    $this->setSessionValue('session_liquidacion.valorLiquidacion_pdf',$valor_liquidacion);
    $valor_tarifa = number_format( $valor_tarifa, 2, ',', '.');
    $valor_liquidacion = number_format($valor_liquidacion, 2, ',', '.');


    $this->setSessionValue('session_liquidacion.valor_liquidacion_pmj',  $valor_liquidacion);
    $this->setSessionValue('session_liquidacion.tarifa_pmj', $valor_tarifa);


    $f110 = strtotime($form_state->getValue('fecha_Inicial_pmj'));
    $dt = \Drupal::time()->getCurrentTime();
    $diff02 = ($f110 - $dt) / 86400;

    if ($diff02 < 10) {
      $alert = '<div class="alertaproximidad">Su fecha es menor a 10 días ,al enviar la solicitud de viabilidad acepta el riezgo de que su tramite no se aceptado</div>';
      /*$this->messenger()->addStatus($this->t('Su fecha es menor a 10 días , el tramite se realizará con riesgo de no ser aceptado', ['@title' =>$N]));*/
      $this->setSessionValue('session_liquidacion.alert_pmj', $alert);
    } else {
      $alert = '';
      $this->setSessionValue('session_liquidacion.alert_pmj', $alert);
    }

    $cantidad_meses =  $form_state->getValue('numero_meses_pmj') * 86400;
    $f1 = strtotime($form_state->getValue('fecha_Inicial_pmj'));
    $f_limit = strtotime($form_state->getValue('fecha_Final_pmj'));
    $diff = ($f_limit - $f1);

    if ($f1 > $f_limit) {
      $form_state->setErrorByName('fecha_Inicial_pmj', $this->t('La fecha inicial no puede ser menor a la final '));
    }
    if ($diff < 2592000) {
      $form_state->setErrorByName('fecha_Final_pmj', $this->t('La fecha final no puede ser menor a 1 mes de 30 días '));
    }

    $limite = strtotime("2023-12-31");
    if ($f_limit > $limite) {
      /*$form_state->setErrorByName('fecha_Final_ej', $this->t('La fecha inicial no puede ser superior al 31 de diciembre del presente año '));*/
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
        'name' => $form_state->getValue('name'),

      ])

      ->set('page_num', 2)

      ->setRebuild(TRUE);
    $this->setSessionValue('session_liquidacion.id_document_pmj', $form_state->getValue('id_document'));
    $this->setSessionValue('session_liquidacion.name_pmj', $form_state->getValue('name'));
    $this->setSessionValue('session_liquidacion.dir_correspondencia_pmj', $form_state->getValue('dir_correspondencia'));
    $this->setSessionValue('session_liquidacion.email_pmj', $form_state->getValue('email'));
    // $this->setSessionValue('session_liquidacion.estrato_pmj', $form_state->getValue('first_estrato'));
    $this->setSessionValue('session_liquidacion.tmovil_pmj', $form_state->getValue('tmovil'));
    $this->setSessionValue('session_liquidacion.tfijo_pmj', $form_state->getValue('tfijo'));
    $this->setSessionValue('session_liquidacion.nombrelegal_pmj', $form_state->getValue('nombrelegal'));
    $this->setSessionValue('session_liquidacion.idlegal_pmj', $form_state->getValue('idlegal'));
  }

  /**
   * Provides custom submission handler for page 3.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextSubmit2(array &$form, FormStateInterface $form_state)
  {
    $form_state
      ->set('page_values3', [
        // Keep only first step values to minimize stored data.
        'fecha_Inicial' => $form_state->getValue('fecha_Inicial_pmj'),
        'field_select_NV_pmj' => $form_state->getValue('field_select_NV_pmj'),

      ])
      ->set('page_num', 3)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 3.
      ->setRebuild(TRUE);
    $this->setSessionValue('session_liquidacion.fecha_Inicial_pmj', $form_state->getValue('fecha_Inicial_pmj'));
    $this->setSessionValue('session_liquidacion.fecha_Final_pmj', $form_state->getValue('fecha_Final_pmj'));
    $this->setSessionValue('session_liquidacion.numero_meses_pmj', $form_state->getValue('numero_meses_pmj'));
    $this->setSessionValue('session_liquidacion.field_select_NV_pmj', $form_state->getValue('field_select_NV_pmj'));
    $this->setSessionValue('session_liquidacion.valor_evento_pmj', $form_state->getValue('valor_evento_pmj'));
    $this->setSessionValue('session_liquidacion.placas_pmj', $form_state->getValue('placas_pmj'));

    /*
    $this->setSessionValue('session_liquidacion.placa1_pmj', $form_state->getValue('placa1'));
    $this->setSessionValue('session_liquidacion.placa2_pmj', $form_state->getValue('placa2'));
    $this->setSessionValue('session_liquidacion.placa3_pmj', $form_state->getValue('placa3'));
    $this->setSessionValue('session_liquidacion.placa4_pmj', $form_state->getValue('placa4'));
    $this->setSessionValue('session_liquidacion.placa5_pmj', $form_state->getValue('placa5'));
    $this->setSessionValue('session_liquidacion.placa6_pmj', $form_state->getValue('placa6'));
    $this->setSessionValue('session_liquidacion.placa7_pmj', $form_state->getValue('placa7'));
    $this->setSessionValue('session_liquidacion.placa8_pmj', $form_state->getValue('placa8'));
    $this->setSessionValue('session_liquidacion.placa9_pmj', $form_state->getValue('placa9'));
    $this->setSessionValue('session_liquidacion.placa10_pmj', $form_state->getValue('placa10'));

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
  public function fapiExampleMultistepFormNextSubmit3(array &$form, FormStateInterface $form_state)
  {
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
    $this->setSessionValue('session_liquidacion.soportes1_pmj', $form_state->getValue('soportes1'));
    $this->setSessionValue('session_liquidacion.soportes2_pmj', $form_state->getValue('soportes2'));
    $this->setSessionValue('session_liquidacion.soportes3_pmj', $form_state->getValue('soportes3'));
    $this->setSessionValue('session_liquidacion.soportes4_pmj', $form_state->getValue('soportes4'));
    $this->setSessionValue('session_liquidacion.soportes5_pmj', $form_state->getValue('soportes5'));
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
  public function fapiExamplePageTwo(array &$form, FormStateInterface $form_state)
  {

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('Vehículos y Placas'),
    ];


    $form['fecha_Inicial_pmj'] = array(
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
        /* 'max' => \Drupal::service('date.formatter')->format(REQUEST_TIME, 'custom', date('Y').'-12-31'),*/
      ],
    );
    $form['fecha_Final_pmj'] = array(
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
    $form['numero_meses_pmj'] = array(
      '#type' => 'number',
      '#title' => 'Ingresar duración de la publicidad  en meses',
      '#width' => '30%',
      '#align' => 'center',
      '#default_value' => $this->session->get('session_liquidacion.numero_meses_pmj', ''),
      '#required' => true,
      '#maxlength' => 3
    );

    $form['field_select_NV_pmj'] = array(
      '#type' => 'number',
      '#title' => $this->t('Número de Vehículos a Evaluar'),
      '#width' => '30%',
      '#align' => 'center',
      //'#default_value' => $this->session->get('session_liquidacion.numero_meses_pmj', ''),
      '#required' => true,
      '#maxlength' => 3
    );

    $form['placas_pfj'] = [
      '#type' => 'textarea',
      //'#default_value' => $this->session->get('session_liquidacion.direccion_valla1_pfj', ''),
      '#title' => 'Ingresar la placas separadas por coma',

    ];


    /*
    $form['field_select_NV_pmj'] = array(
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Número de Vehículos a Evaluar'),
      //'#default_value' => $this->session->get('session_liquidacion.field_select_NV_pmj', ''),
      '#options' => array(t('1'), t('2'), t('3'), t('4'), t('5'), t('6'), t('7'), t('8'), t('9'), t('10'),  t('57')),
      '#attributes' => [

        'name' => 'field_select_NV_pmj',
      ],
    );

    $form['placa1'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 1',
     // '#default_value' => $this->session->get('session_liquidacion.placa1_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '0'],
          ], 'or',
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '1'],
          ], 'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '2'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ]

        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '0'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '1'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '2'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ]
      ),
    ];
    $form['placa2'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 2',
    //  '#default_value' => $this->session->get('session_liquidacion.placa2_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '1'],
          ], 'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '2'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '1'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '2'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ]
      ),
    ];
    $form['placa3'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 3',
     // '#default_value' => $this->session->get('session_liquidacion.placa3_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '2'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],


        ],

        'visible' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '2'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ]
      ),
    ];
    $form['placa4'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 4',
     // '#default_value' => $this->session->get('session_liquidacion.placa4_pmj', ''),

      //'#required' => TRUE,

      '#states' => array(

        'required' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],


        ],

        'visible' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '3'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],
          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ]
      ),
    ];
    $form['placa5'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 5',
    //  '#default_value' => $this->session->get('session_liquidacion.placa5_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],


          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ],

        'visible' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '4'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '10'],
          ],

        ]
      ),
    ];

    $form['placa6'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 6',
     // '#default_value' => $this->session->get('session_liquidacion.placa6_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],



        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '5'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

        ]
      ),
    ];

    $form['placa7'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 7',
      //'#default_value' => $this->session->get('session_liquidacion.placa7_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],



        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '6'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

        ]
      ),
    ];

    $form['placa8'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 8',
     // '#default_value' => $this->session->get('session_liquidacion.placa8_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],



        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '7'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

        ]
      ),
    ];
    $form['placa9'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 9',
      //'#default_value' => $this->session->get('session_liquidacion.placa9_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],



        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '8'],
          ],

          'or',

          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

        ]
      ),
    ];


    $form['placa10'] = [
      '#type' => 'textfield',
      '#title' => 'Ingresar la Placa # 10',
     // '#default_value' => $this->session->get('session_liquidacion.placa10_pmj', ''),
      //'#required' => TRUE,

      '#states' => array(

        'required' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],



        ],

        'visible' => [
          [
            ':input[name="field_select_NV_pmj"]' => ['value' => '9'],
          ],

        ]
      ),
    ];

*/
    $form['valor_evento_pmj'] = array(
      '#type' => 'number',
      '#title' => 'Valor Total de Inversión de la Publicidad Movil',
      '#width' => '30%',
      '#default_value' => $this->session->get('session_liquidacion.valor_evento_pmj', ''),
      '#align' => 'center',
      '#required' => true,
      '#maxlength' => 10
    );




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
  public function fapiExamplePageThree(array &$form, FormStateInterface $form_state)
  {

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
      '#default_value' => $this->session->get('session_liquidacion.soportes1_pmj', ''),
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
      '#default_value' => $this->session->get('session_liquidacion.soportes2_pmj', ''),
    );
    $form['soportes3'] = array(
      '#type' => 'managed_file',
      '#name' => 'soportes',
      '#required' => TRUE,
      '#title' => t('Evidencia Inversión'),
      '#size' => 20,
      '#description' => 'Certificación , Cotización o Factura del valor invertido en el proyecto : Límite: 2MB./ PDF',
      '#upload_validators' => $validators,
      '#upload_location' => 'public://my_files/privado',
      '#default_value' => $this->session->get('session_liquidacion.soportes3_pmj', ''),
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
      '#default_value' => $this->session->get('session_liquidacion.soportes4_pmj', ''),
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
      '#default_value' => $this->session->get('session_liquidacion.soportes5_pmj', ''),
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
  public function fapiExamplePageFour(array &$form, FormStateInterface $form_state)
  {

    // $form_state->set('page_num', 4);
    $form['description4'] = [
      '#type' => 'item',
      '#title' => $this->t('<h2>Validar la Información antes de enviar.</h2'),
    ];


    $id = $this->session->get('session_liquidacion.id_document_pmj', '');
    $email = $this->session->get('session_liquidacion.email_pmj', '');
   /* $Cantidad1 = $this->session->get('session_liquidacion.field_select_NV_pmj1', '');*/
    $Cantidad1 =intval( $this->session->get('session_liquidacion.field_select_NV_pmj', ''));
    $Placas = $this->session->get('session_liquidacion.placas_pmj', '');
    $Cantidad2 = $this->session->get('session_liquidacion.valor_evento_pmj',
    '');

    $Cantidad2 = number_format( $Cantidad2, 2, ',', '.');
    $alert = $this->session->get('session_liquidacion.alert_pmj', '');
    $tarifa =  $this->session->get('session_liquidacion.tarifa_pmj', '');
    $total_liquidacion = $this->session->get('session_liquidacion.valor_liquidacion_pmj', '');
    $form['s0010'] = [
      '#type' => 'markup',
      '#markup' =>   '
    <div class="row">

    <div class="col">Correo: <br>' . $email. '</div>
    <div class="col">No Identificación:<br> ' . $id . '</div>
    <div class="col">Cantidad de Vehículos:<br> ' . $Cantidad1 . '</div>
    <div class="col">Valor Total Inversión:<br> ' . $Cantidad2 . '</div>
    <div class="col">Valor Tarifa: <br>' . $tarifa . '</div>
    <div class="col">Valor Liquidación:<br> ' . $total_liquidacion . '</div>
  </div> </br><div>
  <div class="col">Placas:<br> ' . $Placas.'</div>
  ' . $alert . '</div>',

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
  public function fapiExamplePageTwoBack(array &$form, FormStateInterface $form_state)
  {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values2'))
      ->set('page_num', 1)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }

  public function fapiExamplePageThreeBack(array &$form, FormStateInterface $form_state)
  {
    $form_state
      // Restore values for the 2nd step.
      ->setValues($form_state->get('page_values3'))
      ->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }
  public function fapiExamplePageFourBack(array &$form, FormStateInterface $form_state)
  {
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
  public function submitClearSession(array &$form, FormStateInterface $form_state)
  {
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
  protected function invalidateCacheTag()
  {
    $this->cacheTagInvalidator->invalidateTags(['session_example:' . $this->session->getId()]);
  }
}
