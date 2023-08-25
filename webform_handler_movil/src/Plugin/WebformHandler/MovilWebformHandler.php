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
   $this->messenger()->addStatus($this->t("Prueba Handler"));


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

  /**
   * @param $webform_submission
   *
   * Manipulate data.
   *
   */
  private function submitMyFieldData($webform_submission) {


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


 $valor_liquidacion = $this->money_format_fild( $form_state->getValue('valor_del_la_inversion'));



 $numero_dias = intval($form_state->getValue('duracion_del_evento_den_dias'));




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
$valor_tarifa = 118561;//ok
$valor_liquidacion = 118561 *  $numero_dias ;
$valor_liquidacion_r = 118600 *  $numero_dias ;
} elseif ($valor_liquidacion  >= $valor_tarifa_evento_25  && $valor_liquidacion < $valor_tarifa_evento_35) {
 $valor_tarifa = 166176;//ok
$valor_liquidacion = 166176  *  $numero_dias ;
$valor_liquidacion_r = 166200 *  $numero_dias ;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_35  && $valor_liquidacion < $valor_tarifa_evento_50) {
 $valor_tarifa =237598;
$valor_liquidacion =237598  *  $numero_dias;
$valor_liquidacion_r = 237600 *  $numero_dias ;

}elseif ($valor_liquidacion  >= $valor_tarifa_evento_50  && $valor_liquidacion < $valor_tarifa_evento_70 ) {
 $valor_tarifa =332829;
$valor_liquidacion = 332829  *  $numero_dias ;
$valor_liquidacion_r =  332850 *  $numero_dias ;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_70  && $valor_liquidacion < $valor_tarifa_evento_100) {
 $valor_tarifa =  475672;
$valor_liquidacion = 475672  *  $numero_dias ;
$valor_liquidacion_r =  475700  *  $numero_dias ;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_100  && $valor_liquidacion < $valor_tarifa_evento_200) {
 $valor_tarifa =  951804;
$valor_liquidacion = 951804 * $numero_dias;
$valor_liquidacion_r =  951800  *  $numero_dias ;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_200  && $valor_liquidacion < $valor_tarifa_evento_300) {
 $valor_tarifa = 1427971;
$valor_liquidacion = 1427971 *  $numero_dias;
$valor_liquidacion_r =  1428000  *  $numero_dias ;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_300  && $valor_liquidacion < $valor_tarifa_evento_400) {
 $valor_tarifa = 1904121;
$valor_liquidacion =  1904121  *  $numero_dias ;
$valor_liquidacion_r =  1904150  *  $numero_dias ;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_400  && $valor_liquidacion < $valor_tarifa_evento_500) {
 $valor_tarifa = 2380269;
$valor_liquidacion =  2380269  *  $numero_dias  ;
$valor_liquidacion_r = 2380300  *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_500  && $valor_liquidacion < $valor_tarifa_evento_700) {
 $valor_tarifa =3332567 ;
$valor_liquidacion = 3332567 *  $numero_dias ;
$valor_liquidacion_r = 3332600 *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_700  && $valor_liquidacion < $valor_tarifa_evento_900) {
 $valor_tarifa = 4284866;
$valor_liquidacion = 4284866  *  $numero_dias ;
$valor_liquidacion_r = 4284900 *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_900  && $valor_liquidacion < $valor_tarifa_evento_1500) {
 $valor_tarifa =8160204;
$valor_liquidacion = 8160204  *  $numero_dias ;
$valor_liquidacion_r = 8160200 *  $numero_dias;
}elseif ($valor_liquidacion >= $valor_tarifa_evento_1500  && $valor_liquidacion < $valor_tarifa_evento_2115) {
$valor_tarifa =10917526;
$valor_liquidacion = 10917526 *  $numero_dias;
$valor_liquidacion_r = 10917550 *  $numero_dias;
}elseif ($valor_liquidacion  >= $valor_tarifa_evento_2115  && $valor_liquidacion < $valor_tarifa_evento_8458) {
$valor_tarifa =37374939;
$valor_liquidacion =37374939 *  $numero_dias;
$valor_liquidacion_r =37374939 *  $numero_dias;

}else {
/*$valor_tarifa =($valor_evento * 0.4)/100;
$valor_liquidacion = ( ($valor_evento * 0.4)/100) ;*/
$valor_tarifa =208879615;
/* $valor_liquidacion =37374939 *  $numero_dias;
$valor_liquidacion_r =37374939 *  $numero_dias;*/
}
$valor = $valor_liquidacion;


   $data = $webform_submission->getData();
   $current_page = $webform_submission->getCurrentPage();
   // to get a value from a form field
   $form_value = $data['duracion_del_evento_den_dias'];

   // to set the value of a form field
  if( $current_page == 'confirmacion' ){


//Set geo info

   $localidad = $data['localidad'];




   switch ( $localidad) {
    case 233:

        $term = Term::load(intval($data['barrio_localidad_1']));
$name = $term->getName();

        $this->messenger()->addStatus($this->t("Barrios".
        $name  ));

        break;
    case 234:
        $term = Term::load(intval($data['barrio_localidad_2']));
$name = $term->getName();

        $this->messenger()->addStatus($this->t("Barrio".
        $name  ));

        break;
    case 235:
        $term = Term::load(intval($data['barrio_localidad_3']));
        $name = $term->getName();

                $this->messenger()->addStatus($this->t("Barrio".
                $name  ));

        break;
}


//hola kt

   $data['valor_a_pagar'] =number_format($valor_liquidacion, 2, ',', '.');
   $data['valor_tarifa'] =number_format($valor_tarifa , 2, ',', '.');
   $webform_submission->setData($data);


   
   //imprimir en pantalla
   /*
   $this->messenger()->addStatus($this->t("Valor Liquidacion: $". number_format($valor_liquidacion , 2, ',', '.')));
   $this->messenger()->addStatus($this->t("Valor Tarifa: $". number_format($valor_tarifa , 2, ',', '.')));
   $this->messenger()->addStatus($this->t("SMLV: $". number_format( $valor , 2, ',', '.')));
   $this->messenger()->addStatus($this->t("Valor Evento: $" . number_format( $valor_liquidacion , 2, ',', '.')));
   $this->messenger()->addStatus($this->t("Días:".   $numero_dias));*/

}





 }





}
