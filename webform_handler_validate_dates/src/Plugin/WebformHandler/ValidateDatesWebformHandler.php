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
            $hoy = new DrupalDateTime('now');
            $diff_dias = $date1->diff( $date2);
            $diff_dias_hoy = $hoy->diff($date1);
            $duracion_del_evento_den_dias =$form_state->getValue('duracion_del_evento_den_dias');
            $valor_del_la_inversion = $form_state->getValue('valor_del_la_inversion');
            $this->messenger()->addStatus($this->t("Print:".$form_state->getValue('tipo_de_solicitante') ));
            
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

/**
 * Campos del webform
 * $form_state->getValue('field_name');
 * tipo_de_solicitante
 * nombre_de_representante_legal_
 * nombres_y_apellidos
 * razon_social
 * ndeg_de_documento_de_representante_legal_
 * documento_de_identidad
 * correo_electronico
 * telefono_movil
 * telefono_fijo
 * direccion_de_correspondencia_del_solicitante
 * localidad
 * barrio_localidad_1
 * barrio_localidad_2
 * barrio_localidad_3
 * direccion_del_evento
 * duracion_del_evento_den_dias
 * breve_descripcion_del_evento
 * valor_del_la_inversion

 * Documentos
 * 	identificacion
 * 	rut_form1
 *  representacion_legal
 *	evidencia_inversion
 * yo_acepto_terminos_y_condiciones_del_uso_de_mis_datos_personales
 * // oculto
 * 	valor_tarifa
 * 	valor_a_pagar
 * 
 */


$my_article = Node::create(['type' => 'liquidacion']);
$my_article->set('title', $codigo_liquidacion);
/*
$my_article->set('field_valor' , $valor_liquidacion);//aqui
$my_article->set('field_barrio_liquidacion', $barrio_liquidacion);//aqui//
$my_article->set('field_concepto_ambiental_liq', "Eventos");
$my_article->set('field_direccion_correspondencia', $dir_correspondecia_contrib);
$my_article->set('field_direccion_del_predio', $direccion_evento);


$my_article->set('field_valor_evento', $valore);
     $my_article->set('field_descripcion_evento', $descripcion_evento );

$my_article->set('field_tipo_de_solicitante', $tipo_solicitante);
$my_article->set('field_id_contribuyente', $id_contribuyente);
$my_article->set('field_nombre_contribuyente', $name_contrib);
$my_article->set('field_email_contribuyente', $email_cotrib );
$my_article->set('field_telefono_fijo_contribuyent', $tfijo);
$my_article->set('field_telefono_movil_contribuyen', $tmovil);
$my_article->set('field_estrato_contribuyente', $estrato);
$my_article->set('field_condicion_contribuyente', $condicion);
$my_article->set('field_comparado_factura',false);
//$my_article->set('field_codigo_liquidacion_factura', false);
$my_article->set('field_estado',FALSE);


$my_article->set('field_id_file', $file1);
$my_article->set('field_rut_file', $file2);
$my_article->set('field_ei_file', $file3);*/

$my_article->set('status', '0');
//$my_article->set('uid', $id_contribuyente);

$my_article->enforceIsNew();
  $my_article->save();

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
