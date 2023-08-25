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

    $alert_near ='<div class="alertaproximidad">Tenga en cuenta la fecha de su publicidad antes de liquidar. Su Solicitud tiene un tiempo de respuesta de 15 dias habiles Contados a partir de la fecha en la que sea adjuntado el soporte de pago y la documentción requerida en el formumlario, De conformidad con la ley 1437 del 2011</div>';


    if (!$form_state->hasAnyErrors()) {
        //Tu validación aquí

        $page = $webform_submission->getCurrentPage();
        if(  $page == 'datos_del_evento' ){
            //   $this->submitMyFieldData($webform_submission);


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
        $form_state->setErrorByName($this->$form['duracion_del_evento_den_dias'], "Error en la Cantidad de Días, Se calculan : ".$diff_dias );
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
