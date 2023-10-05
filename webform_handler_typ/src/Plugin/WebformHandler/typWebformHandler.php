<?php

namespace Drupal\webform_handler_typ\Plugin\WebformHandler;



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
 *   id = "handler_typ",
 *   label = @Translation("typ"),
 *   category = @Translation("Creation"),
 *   description = @Translation("Create epa forms typ"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class typWebformHandler extends WebformHandlerBase {



    /**
 * {@inheritdoc}
 */
public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    parent::validateForm($form, $form_state, $webform_submission);

    $page = $webform_submission->getCurrentPage();


    if(  $page == 'datos_del_evento' ){
         $this->validate_count($form_state,$webform_submission);
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

public function validate_count($form_state, $webform_submission) {
  $cantidad_placas = $form_state->getValue('cantidad_de_arboles');
  $array_placas = count($form_state->getValue('cantidad'));

  if (  $array_placas > $cantidad_placas ) {
    // Use addError to display an alert message.
    $form_state->setErrorByName('cantidad_de_arboles', $this->t('La cantidad de árboles no coincide con las especies ingresadas' ));
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
       
      
      
           $cantidad_arboles = $form_state->getValue('cantidad_de_arboles');
           $valor_liquidacion =  $valor *  $cantidad_arboles;
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
   $data['valor_tarifa'] =number_format($valor , 2, ',', '.');
   $webform_submission->setData($data);


}





 }


}
