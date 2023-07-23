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

        $alert_near ='<div class="alertaproximidad">Tenga en cuenta la fecha de su envento antes de liquidar. Su Solicitud tiene un tiempo de respuesta de 15 dias habiles Contados a partir de la fecha en la que sea adjuntado el soporte de pago y la documentción requerida en el formumlario, De conformidad con la ley 1437 del 2011</div>';


        if (!$form_state->hasAnyErrors()) {
            //Tu validación aquí


            $date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'));
            $date2 = new DrupalDateTime($form_state->getValue('fecha_final'));
            $id_legal = $form_state->getValue('ndeg_de_documento_de_representante_legal_');
            $id_natural = 	$form_state->getValue('documento_de_identidad_');

            $hoy = new DrupalDateTime('now');

            $diff_dias = $date1->diff( $date2);
            $diff_dias_hoy = $hoy->diff($date1);
            $this->messenger()->addError($this->t("Print: ".    $diff_dias->format('%R%a days')." /: ".$diff_dias_hoy->format('%R%a days (red)')));



			//Imprimir Errores

            if ($date2 <  $date1 ) {
                $form_state->setErrorByName($this->form['fecha_final'], "Error en las fechas " );

            }
			  if ($diff_dias->format("%r%a")< 10) {

                $this->messenger()->addError(           $this->t($alert_near)

                );
            }

			 if (!is_numeric($id_legal)) {

               $form_state->setErrorByName($this->form['ndeg_de_documento_de_representante_legal_'], "Validar doc Rep Legal");

            }


        }

    }



    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

        $this->submitMyFieldData($webform_submission);
        if (!$form_state->hasAnyErrors()) {
            //Tu logica despues del submit
        }
    }

  /**
   * @param $webform_submission
   *
   * Manipulate data.
   *
   */
  private function submitMyFieldData($webform_submission) {
    $data = $webform_submission->getData();
    $current_page = $webform_submission->getCurrentPage();
    // to get a value from a form field
    $form_value = $data['duracion_del_evento_den_dias'];

    // to set the value of a form field
   if( $current_page == 'datos_del_evento' ){
    $data['valor_tarifa'] = 1000;
}
    $webform_submission->setData($data);

  }

}
