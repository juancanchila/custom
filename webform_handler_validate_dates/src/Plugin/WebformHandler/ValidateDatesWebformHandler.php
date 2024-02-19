<?php

namespace Drupal\webform_handler_validate_dates\Plugin\WebformHandler;



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
 *   id = "validate_dates_validator",
 *   label = @Translation("Validate Dates"),
 *   category = @Translation("Validation"),
 *   description = @Translation("validate epa forms"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class ValidateDatesWebformHandler extends WebformHandlerBase {



    /**
 * {@inheritdoc}
 */
public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    parent::validateForm($form, $form_state, $webform_submission);
    $page = $webform_submission->getCurrentPage();


    if(  $page == 'datos_del_evento' ){
         $this->validate_dates($form_state,$webform_submission);
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

  public function validate_dates($form_state, $webform_submission) {
    $now = DrupalDateTime::createFromTimestamp(time());
    $now->setTimezone(new \DateTimeZone('UTC'));
    $cantidad_dias = $form_state->getValue('duracion_del_evento_den_dias');

    $f1 = strtotime($form_state->getValue('fecha_inicio'));
    $f_limit = strtotime($form_state->getValue('fecha_final'));
    $dt = strtotime($now->format('Y-m-d'));


    $f1 = DrupalDateTime::createFromTimestamp($f1);
    $f_limit = DrupalDateTime::createFromTimestamp($f_limit);
    $dt = DrupalDateTime::createFromTimestamp( $dt);

    $interval = $f1->diff($f_limit);
    $daysDifference = $interval->days;

    if (   $f1 == $f_limit  ) {
      $daysDifference = 1;

    }

    if ( $cantidad_dias != $daysDifference) {
      // Use addError to display an alert message.
      $form_state->setErrorByName('duracion_del_evento_den_dias', $this->t('La cantidad de días no cuincide se calculan: '.$daysDifference ));
  }

  if ($f1 > $f_limit) {
    // Use addError to display an alert message.
    $form_state->setErrorByName('fecha_inicial', $this->t('La fecha inicial no puede ser menor a la final'));
}



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

 $valor_tarifa = 0;
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
  $valor_tarifa = 163732;//ok
  $valor_liquidacion = 163732 *  $numero_dias ;
  $valor_liquidacion_r = 118600 *  $numero_dias ;
} elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
    $valor_tarifa = 209559;//ok
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
    $valor_tarifa = 3001671;
  $valor_liquidacion =  3001671  *  $numero_dias  ;
  $valor_liquidacion_r = 2380300  *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_500  && $valor_liquidacion < $valor_tarifa_evento_700) {
    $valor_tarifa =4202580;
 $valor_liquidacion = 4202580*  $numero_dias ;
 $valor_liquidacion_r = 3332600 *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_700  && $valor_liquidacion < $valor_tarifa_evento_900) {
    $valor_tarifa = 5403489;
  $valor_liquidacion = 5403489  *  $numero_dias ;
  $valor_liquidacion_r = 4284900 *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_900  && $valor_liquidacion < $valor_tarifa_evento_1500) {
    $valor_tarifa =90062154;
 $valor_liquidacion = 90062154  *  $numero_dias ;
 $valor_liquidacion_r = 90062150 *  $numero_dias;
}elseif ($valor_liquidacion >= $valor_tarifa_evento_1500  && $valor_liquidacion < $valor_tarifa_evento_2115) {
  $valor_tarifa =12699009;
  $valor_liquidacion = 12699009 *  $numero_dias;
  $valor_liquidacion_r = 10917550 *  $numero_dias;
}elseif ($valor_liquidacion  > $valor_tarifa_evento_2115  && $valor_liquidacion <= $valor_tarifa_evento_8458) {
  $valor_tarifa =$valor_liquidacion * 0.05;
  $valor_liquidacion =$valor_liquidacion * 0.05;
}elseif ($valor_liquidacion  == $valor_tarifa_evento_2115 ) {
  $valor_tarifa =$valor_liquidacion * 0.06;
  $valor_liquidacion = $valor_liquidacion * 0.06;
}elseif ($valor_liquidacion  > $valor_tarifa_evento_8458 ) {
  $valor_tarifa =$valor_liquidacion * 0.04;
  $valor_liquidacion =$valor_liquidacion * 0.04;
}




$barrio1 = intval($this->money_format_fild( $form_state->getValue('barrio_localidad_1')));

$barrio2 = intval($this->money_format_fild( $form_state->getValue('barrio_localidad_2')));

$barrio3 = intval($this->money_format_fild( $form_state->getValue('barrio_localidad_1')));

   $data = $webform_submission->getData();
   $current_page = $webform_submission->getCurrentPage();
   // to get a value from a form field

   // to set the value of a form field
  if( $current_page == 'confirmacion' ){

    if($barrio1){
      $term_name = \Drupal\taxonomy\Entity\Term::load($barrio1)->get('name')->value;

      $data['barrio'] = $term_name;
    }
    if($barrio2){
      $term_name = \Drupal\taxonomy\Entity\Term::load($barrio2)->get('name')->value;

      $data['barrio'] = $term_name;
    }
    if($barrio3){

      $term_name = \Drupal\taxonomy\Entity\Term::load($barrio3)->get('name')->value;

      $data['barrio'] = $term_name;
    }




   $data['valor_a_pagar'] =number_format($valor_liquidacion, 2, ',', '.');
   $data['valor_tarifa'] =number_format($valor_tarifa, 2, ',', '.');
   $webform_submission->setData($data);


}





 }



}
