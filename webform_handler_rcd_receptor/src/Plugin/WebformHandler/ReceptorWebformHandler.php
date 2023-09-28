<?php

namespace Drupal\webform_handler_rcd_receptor\Plugin\WebformHandler;



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
 *   id = "handler_rcd_receptor",
 *   label = @Translation("Create Liq Receptor"),
 *   category = @Translation("Creation"),
 *   description = @Translation("Create epa forms receptor"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class ReceptorWebformHandler extends WebformHandlerBase {






    /**
 * {@inheritdoc}
 */
public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    parent::validateForm($form, $form_state, $webform_submission);

    $page = $webform_submission->getCurrentPage();


    if(  $page == 'informacion_de_la_solicitud' ){
         $this->validate_dates($form_state,$webform_submission);
        }
 

    
}

/**
 * {@inheritdoc}
 */
public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $page = $webform_submission->getCurrentPage();

    if(  $page == 'informacion_de_la_solicitud' ){
     //   $this->submitMyFieldData($webform_submission);
           // $this->valor_a_pagar($form_state,$webform_submission);
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
 
  
    $f1 = strtotime($form_state->getValue('fecha_inicio'));
    $f_limit = strtotime($form_state->getValue('fecha_final'));
  
    $f1 = DrupalDateTime::createFromTimestamp($f1);
    $f_limit = DrupalDateTime::createFromTimestamp($f_limit);
  
  
 
  if ($f1 > $f_limit) {
    // Use addError to display an alert message.
    $form_state->setErrorByName('fecha_inicio', $this->t('La fecha inicial no puede ser menor a la final'));
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

 $barrio1 = intval($this->money_format_fild( $form_state->getValue('barrio_localidad_1')));

 $barrio2 = intval($this->money_format_fild( $form_state->getValue('barrio_localidad_2')));
 
 $barrio3 = intval($this->money_format_fild( $form_state->getValue('barrio_localidad_1')));

   $data = $webform_submission->getData();
   $current_page = $webform_submission->getCurrentPage();
   // to get a value from a form field


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
    
   // to set the value of a form field
  if( $current_page == 'confirmacion' ){

//test
   $data['valor_a_pagar'] =number_format($valor_liquidacion, 2, ',', '.');
   $data['valor_tarifa'] =number_format($valor_tarifa , 2, ',', '.');
   $webform_submission->setData($data);


}





 }


}
}