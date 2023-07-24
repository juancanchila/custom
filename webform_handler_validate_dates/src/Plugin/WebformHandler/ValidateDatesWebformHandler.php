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

  $alert_near ='<div class="alertaproximidad">Tenga en cuenta la fecha de su envento antes de liquidar. Su Solicitud tiene un tiempo de respuesta de 15 dias habiles Contados a partir de la fecha en la que sea adjuntado el soporte de pago y la documentción requerida en el formumlario, De conformidad con la ley 1437 del 2011</div>';
 

        if (!$form_state->hasAnyErrors()) {
            //Tu validación aquí

            $page = $webform_submission->getCurrentPage();
            $date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'));
            $date2 = new DrupalDateTime($form_state->getValue('fecha_final'));
           $cantidad_dias = $form_state->getValue('duracion_del_evento_den_dias');
         
          
            $hoy = new DrupalDateTime('now');
            $diff_dias = $date1->diff( $date2);
            if($diff_dias->format("%r%a") == 0){
                $diff_dias = $diff_dias->format("%r%a")+ 1;
            }else{

                $diff_dias = $diff_dias->format("%r%a");
            }
            $diff_dias_hoy = $hoy->diff($date1);
            $duracion_del_evento_den_dias =$form_state->getValue('duracion_del_evento_den_dias');
            $valor_del_la_inversion = $form_state->getValue('valor_del_la_inversion');
            $this->messenger()->addStatus($this->t("Print:".$form_state->getValue('tipo_de_solicitante') ));
            $this->messenger()->addStatus($this->t("Print n días:". $cantidad_dias ));
            $this->messenger()->addStatus($this->t("Print n días:". $diff_dias->format("%r%a") ));
          /*
           $this->messenger()->addStatus($this->t("Print:".$diff ));
			$date1 =new DrupalDateTime( $form_state->getValue('fecha_inicio'));
            $date2 = new DrupalDateTime($form_state->getValue('fecha_final'));

            $diff = strval($date1->formatTimeDiffSince($date2));

            $this->messenger()->addStatus($this->t("Print:".$diff ));

			$id_legal = $form_state->getValue('ndeg_de_documento_de_representante_legal_');

            $hoy  = new DrupalDateTime('now');
            $diff_dias_now = $date1->diff($hoy);
			$diff_dias = $date1->diff($hoy->modify('+12 day'));

            $this->messenger()->addStatus($this->t("Print:".$date1));
                $this->messenger()->addError($this->t("Print: ".    $diff_dias->format('%R%a days')." /: ".$diff_dias_hoy->format('%R%a days (red)')));
*/




           if( $page == 'datos_de_contacto' && $id_legal ){
            //Imprimir Errores Contacto
       if (!is_numeric($id_legal)) {
            //no alertar - > solo imprimir
        // $form_state->setErrorByName($this->form['ndeg_de_documento_de_representante_legal_'], "Validar doc Rep Legal");

      }
  }//errores de Contacto



            if( $page == 'datos_del_evento' ){
         //Imprimir Errores del evento
         if ($date1 > $date2  ) {
            $form_state->setErrorByName($this->form['fecha_final'], "Error en las fechas " );

        }
        if ( $diff_dias_hoy->format("%r%a") < 10) {
            $this->messenger()->addError($this->t($alert_near));
      }

     
      if (  $diff_dias != $cantidad_dias ) {
        $form_state->setErrorByName($this->$form['duracion_del_evento_den_dias'], "Error en la Cantidad de Días " );
  }

            } //errores del evento
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
