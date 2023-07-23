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




        if (!$form_state->hasAnyErrors()) {
            //Tu validación aquí


            $date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'), new \DateTimeZone('UTC'));
            $date2 = new DrupalDateTime($form_state->getValue('fecha_final'), new \DateTimeZone('UTC'));

            $hoy = new DrupalDateTime('now');

            $diff_dias = $date1->diff( $date2);
            $diff_dias_hoy = $date1->diff(    $hoy);
            $this->messenger()->addStatus($this->t("Print: ".    $diff_dias->format('%d days')." /: ".$diff_dias_hoy->format('%d days')));
          /*
			$date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'));
            $date2 = new DrupalDateTime($form_state->getValue('fecha_final'));

            $diff = strval($date1->formatTimeDiffSince($date2));

            $this->messenger()->addStatus($this->t("Print:".$diff ));

			$id_legal = $form_state->getValue('ndeg_de_documento_de_representante_legal_');

            $hoy  = new DrupalDateTime('now');
            $diff_dias_now = $date1->diff($hoy);
			$diff_dias = $date1->diff($hoy->modify('+12 day'));

            $this->messenger()->addStatus($this->t("Print:".$date1));
*/

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
			//$this->messenger()->addStatus($this->t('Su solicitud es menos a 10 dias'));
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
