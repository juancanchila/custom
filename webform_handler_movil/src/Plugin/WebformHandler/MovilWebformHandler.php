<?php

namespace Drupal\webform_handler_movil\Plugin\WebformHandler;



use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "movil_validator",
 *   label = @Translation("Validate Movil"),
 *   category = @Translation("Validation"),
 *   description = @Translation("validate epa movil"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class MovilWebformHandler extends WebformHandlerBase {

    /**
 * {@inheritdoc}
 */
public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    parent::validateForm($form, $form_state, $webform_submission);

    $alert_near ='<div class="alertaproximidad">Tenga en cuenta la fecha de su publicidad antes de liquidar. Su Solicitud tiene un tiempo de respuesta de 15 dias hábiles Contados a partir de la fecha en la que sea adjuntado el soporte de pago y la documentación requerida en el formulario, De conformidad con la ley 1437 del 2011</div>';


    if (!$form_state->hasAnyErrors()) {
        //Tu validación aquí

        $page = $webform_submission->getCurrentPage();
        if(  $page == 'datos_del_evento' ){
            //   $this->submitMyFieldData($webform_submission);

           // $this->messenger()->addStatus($this->t("Prueba"));
           // placas
           //cantidad_de_vehiculos
           $page = $webform_submission->getCurrentPage();
           $date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'));
           $date2 = new DrupalDateTime($form_state->getValue('fecha_final'));
          $cantidad_meses = $form_state->getValue('duracion_del_evento_den_dias');
          $listado_placas = $form_state->getValue('placas');
          $cantidad_placas = $form_state->getValue('cantidad_de_vehiculos');

           $hoy = new DrupalDateTime('now');
           $diff_dias_hoy = $hoy->diff($date1);
           $diff_meses = $date1->diff( $date2);

           if($diff_meses->format("%r%a") == 0){
               $diff_meses = $diff_meses->format("%r%a")+ 1;
           }else{

               $diff_meses = $diff_meses->format("%m");
           }


           if (count($listado_placas) != $cantidad_placas ) {
            $form_state->setErrorByName($this->form['cantidad_de_vehiculos'], "La cantidad de placas no coincide con la cantidad de vehículos" );

        }

        if ($date1 > $date2  ) {
            $form_state->setErrorByName($this->form['fecha_final'], "Error en las fechas " );

        }
        if ( $diff_dias_hoy->format("%r%a") < 10) {
            $this->messenger()->addError($this->t($alert_near));
      }


      if (  $diff_meses != $cantidad_meses  ) {
        $form_state->setErrorByName($this->$form['duracion_del_evento_den_dias'], "Error en la cantidad de meses, se calcula : ".$diff_meses );
  }




               }




    }
}

/**
 * {@inheritdoc}
 */
public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $page = $webform_submission->getCurrentPage();

    if(  $page == 'confirmacion' ){
     //   $this->submitMyFieldData($webform_submission);
            $this->valor_a_pagar($form_state,$webform_submission);
        }



    if (!$form_state->hasAnyErrors()) {
        //Tu logica despues del submit
    }
}
public function money_format_fild($money) {

    $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
    $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

    $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

    $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
    $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

    $money_clean = (float) str_replace(',', '.', $removedThousandSeparator);

    return $money_clean;
   // $this->messenger()->addStatus($this->t("Print:". $money_clean));
  }



 public function valor_a_pagar( $form_state,$webform_submission) {



 //calculos
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


 $valor_liquidacion = $this->money_format_fild( $form_state->getValue('valor_del_la_inversion'));
 $numero_dias = intval($form_state->getValue('duracion_del_evento_den_dias'));
 $cantidad = intval($form_state->getValue('cantidad_de_vehiculos'));
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




 if ($valor_liquidacion < $valor_tarifa_evento_25) {
    $valor_tarifa = 118561;//ok
    $valor_liquidacion = 118561 *  $numero_dias * $cantidad ;
    $valor_liquidacion_r = 118600 *   $numero_dias * $cantidad ;
  } elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
      $valor_tarifa = 166176;//ok
    $valor_liquidacion = 166176  *   $numero_dias * $cantidad ;
    $valor_liquidacion_r = 166200 *  $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_35  && $valor_liquidacion < $valor_tarifa_evento_50) {
      $valor_tarifa =237598;
    $valor_liquidacion =237598  *   $numero_dias * $cantidad ;
    $valor_liquidacion_r = 237600 *  $numero_dias * $cantidad ;

  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_50  && $valor_liquidacion < $valor_tarifa_evento_70 ) {
      $valor_tarifa =332829;
    $valor_liquidacion = 332829  *   $numero_dias * $cantidad ;
    $valor_liquidacion_r =  332850 *  $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_70  && $valor_liquidacion < $valor_tarifa_evento_100) {
      $valor_tarifa =  475672;
    $valor_liquidacion = 475672  *  $numero_dias * $cantidad ;
    $valor_liquidacion_r =  475700  *   $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_100  && $valor_liquidacion < $valor_tarifa_evento_200) {
      $valor_tarifa =  951804;
    $valor_liquidacion = 951804 *  $numero_dias * $cantidad ;
    $valor_liquidacion_r =  951800  *   $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_200  && $valor_liquidacion < $valor_tarifa_evento_300) {
      $valor_tarifa = 1427971;
   $valor_liquidacion = 1427971 *  $numero_dias * $cantidad ;
   $valor_liquidacion_r =  1428000  *   $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_300  && $valor_liquidacion < $valor_tarifa_evento_400) {
      $valor_tarifa = 1904121;
   $valor_liquidacion =  1904121  *  $numero_dias * $cantidad ;
   $valor_liquidacion_r =  1904150  *  $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_400  && $valor_liquidacion < $valor_tarifa_evento_500) {
      $valor_tarifa = 2380269;
    $valor_liquidacion =  2380269  *   $numero_dias * $cantidad ;
    $valor_liquidacion_r = 2380300  *   $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_500  && $valor_liquidacion < $valor_tarifa_evento_700) {
      $valor_tarifa =3332567 ;
   $valor_liquidacion = 3332567 *   $numero_dias * $cantidad ;
   $valor_liquidacion_r = 3332600 *   $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_700  && $valor_liquidacion < $valor_tarifa_evento_900) {
      $valor_tarifa = 4284866;
    $valor_liquidacion = 4284866  *  $numero_dias * $cantidad ;
    $valor_liquidacion_r = 4284900 *  $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_900  && $valor_liquidacion < $valor_tarifa_evento_1500) {
      $valor_tarifa =8160204;
   $valor_liquidacion = 8160204  *  $numero_dias * $cantidad ;
   $valor_liquidacion_r = 8160200 *  $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion >= $valor_tarifa_evento_1500  && $valor_liquidacion < $valor_tarifa_evento_2115) {
    $valor_tarifa =10917526;
    $valor_liquidacion = 10917526 *  $numero_dias * $cantidad ;
    $valor_liquidacion_r = 10917550 *   $numero_dias * $cantidad ;
  }elseif ($valor_liquidacion  >= $valor_tarifa_evento_2115  && $valor_liquidacion < $valor_tarifa_evento_8458) {
    $valor_tarifa =37374939;
    $valor_liquidacion =37374939 *  $numero_dias * $cantidad ;
    $valor_liquidacion_r =37374939 *   $numero_dias * $cantidad ;

  }else {
    /*$valor_tarifa =($valor_evento * 0.4)/100;
    $valor_tarifa =208879615;
   /* $valor_liquidacion =37374939 *  $numero_dias;
    $valor_liquidacion_r =37374939 *  $numero_dias;*/
  }


$valor = $valor_liquidacion;


   $data = $webform_submission->getData();
   $current_page = $webform_submission->getCurrentPage();
   // to get a value from a form field

   // to set the value of a form field
  if( $current_page == 'confirmacion' ){


   $data['valor_a_pagar'] =number_format($valor_liquidacion, 2, ',', '.');
   $data['valor_tarifa'] =number_format($valor_tarifa , 2, ',', '.');
   $webform_submission->setData($data);


}





 }


}
