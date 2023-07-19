<?php

namespace Drupal\webform_handler_validate_dates\Plugin\WebformHandler;



use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Datetime\DrupalDateTime;
/**
 * Webform validate handler.
 *
 * @WebformHandler(
 *   id = "validate_dates_validator",
 *   label = @Translation("Validate Entries by Comparing 2 dates"),
 *   category = @Translation("Validation"),
 *   description = @Translation("This validates two webform fields by ensuring that the comparison relationship (e.g. greater than) applies properly."),
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
	
	
		
		
        if (!$form_state->hasAnyErrors()) {
            //Tu validación aquí
		
			$result = TRUE;
			$result2 = FALSE;
		
			$date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'));
            $date2 = new DrupalDateTime($form_state->getValue('fecha_final'));
			
			$diff = $date1->diff($date2);
			
			$id_legal = $form_state->getValue('ndeg_de_documento_de_representante_legal_');
			
            $hoy  = new DrupalDateTime('now');
			$diff_dias = $date1->diff($hoy->modify('+12 day'));
$comparison ="<=";
			
        if ($date1 && $date2) {
            

            switch ($comparison) {
                case "==":
                    $result = ($date1 == $date2) ? TRUE : FALSE;
					 
                    break;
                case "<=":
                    $result = ($date1 <= $date2) ? TRUE : FALSE;
					
                    break;
                case "<":
                    $result = ($date1 < $date2) ? TRUE : FALSE;
					
                    break;
                case ">=":
                    $result = ($date1 >= $date2) ? TRUE : FALSE;
					 
                    break;
                case ">":
                    $result = ($date1 > $date2) ? TRUE : FALSE;
				
                    break;
            }

			//Imprimir Errores
		
            if ($result == FALSE) {
                $form_state->setErrorByName($this->form['fecha_final'], "Error en las fechas  ".$date1. "/ ".$date1 );
			
            }
			  if ($diff_dias < 10) {
				  //no alertar - > solo imprimir
              // $form_state->setErrorByName($this->form['fecha_final'], "Su solicitud es menos a 10 dias / ". date('Y-m-d', $hoy));
			$this->messenger()->addStatus($this->t('Su solicitud es menos a 10 dias'));
            }
				
			 if (!is_numeric($id_legal)) {
				  //no alertar - > solo imprimir
              // $form_state->setErrorByName($this->form['ndeg_de_documento_de_representante_legal_'], "Validar doc Rep Legal");
			
            }
			
			
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
