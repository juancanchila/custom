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


    if (!$form_state->hasAnyErrors()) {
        //Tu validación aquí

        $page = $webform_submission->getCurrentPage();
        if(  $page == 'datos_del_evento' ){
            //   $this->submitMyFieldData($webform_submission);


           // placas
           //cantidad_de_vehiculos

           $cantidad_placas = $form_state->getValue('placas');
           $listado_placas  = $form_state->getValue('cantidad_de_vehiculos');

          

            $this->messenger()->addStatus($this->t("Test".$cantidad_placas[0]."/".$listado_placas));
            //prueba 3
               }
      
    }
}

/**
 * {@inheritdoc}
 */
public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    if (!$form_state->hasAnyErrors()) {
        //Tu logica despues del submit
    }
}


}
