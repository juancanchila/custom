<?php

namespace Drupal\custom_rules_action\Plugin\RulesAction;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Datetime\DrupalDateTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Url;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Mail\MailManagerInterface;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;

use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Drupal\Core\File\FileSystemInterface;


/**
 * Provides a 'Node ID is' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action",
 *   label = @Translation("Node ID is"),
 *   category = @Translation("Node"),
 * context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the content item to change."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 *
 */
class NodeIDIs extends RulesActionBase
{


 /**
   * Executes the action with the given context.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to modify.
   *
   */
  protected function doExecute(NodeInterface $node) {

    
    $hoy =new DrupalDateTime( 'now');



  $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
  $sec ="01"."0".$consecutivo_facturas[0]["value"].date('Y');
    $node->setTitle($sec);
     $type = "Se ha creado la Liquidación # ".$sec;
     $concepto_ambiental_liquidacion = $node->get('field_concepto_ambiental_liq')->getValue();
    

  

      switch ($concepto_ambiental_liquidacion) {
        case 'Publicidad Móvil':
          $type = "Publicidad Móvil ";
          \Drupal::messenger()->addMessage(t($type),'error');
            break;
        case "Eventos":
          $type = "Eventos ";
    \Drupal::messenger()->addMessage(t($type),'error');
          break;
        
          case "Publicidad Fija":
            $type = "Publicidad Fija ";
            \Drupal::messenger()->addMessage(t($type),'error');
            break;
          
            case "Rumba Segura":
              $type = "Rumba Segura ";
              \Drupal::messenger()->addMessage(t($type),'error');
              break;
              case "Aprovechamiento Forestal":
    
                $type = "Aprovechamiento Forestal ";
                \Drupal::messenger()->addMessage(t($type),'error');
                break;

              
    }




/*
      //number_format( $valor_evento, 2, ',', '.');
     

      if( $tipo_solicitante[0]["value"] == "Persona Jurídica"){
        $id_contribuyente = $node->get('field_idlegal')->getValue();
        $name_contrib = $node->get('field_razon_social')->getValue();

      }else{
        $id_contribuyente = $node->get('field_id_contribuyente')->getValue();
        $name_contrib =  $node->get('field_nombre_solicitante')->getValue();
      }






      $email_cotrib = $node->get('field_email_contribuyente')->getValue();
      $tmovil = $node->get('field_telefono_movil_contribuyen')->getValue();
      $valor_tarifa = $node->get('field_valor_tarifa')->getValue();
      $valor_evento = $node->get('field_valor_evento')->getValue();
      $valor = $node->get('field_valor')->getValue();  
      $dir_correspondecia_contrib = $node->get('field_direccion_correspondencia')->getValue();
      $duracion = $node->get('field_duracion')->getValue();
      $code="4157709998461239"."8020".$sec."3900".$this->money_format_fild($valor[0]["value"])."96".date('Y')."1231";
      $code_content="(415)7709998461239"."(8020)".$sec."(3900)".$this->money_format_fild($valor[0]["value"])."(96)".date('Y')."1231";
      $concepto_ambiental_liquidacion =  $node->get('field_concepto_ambiental_liq')->getValue();
      $concepto_ambiental_liquidacion =$concepto_ambiental_liquidacion[0];
      $field_detalle ="test";
      */
      
      //$node->get('field_detalle')->getValue(); //direcciones y placas y especies
      /*
      $cantidad = $node->get('field_cantidad')->getValue(); //AF


      $field_nombre_predio  = $node->get('field_nombre_predio')->getValue(); // AF, RS,
      $field_direccion_del_predio  = $node->get('field_direccion_del_predio')->getValue(); //RS, AF, E
      $field_nombre_establecimiento = $node->get('field_nombre_establecimiento')->getValue();// RS
      $field_barrio_liquidacion = $node->get('field_barrio_liquidacion')->getValue();
      
*/
     

/*

      switch ($concepto_ambiental_liquidacion) {
        case 'Publicidad Móvil':
          $tipo_de_solicitud = "Publicidad Móvil";
          $concepto = '<p class="concepto">VIABILIDAD PARA PUBLICIDAD EXTERIOR VISUAL MÓVIL PARA UN NÚMERO DE VEHÍCULOS IGUAL A : ' .$cantidad[0]["value"] . ' , SEGÚN SOLICITUD CON #' . $sec . '</p> Para las placas : ' . $field_detalle[0]["value"]. ', Con una Inversión de ' . $valor_evento[0]["value"] . '</p>';

            break;
        case "Eventos":
          $tipo_de_solicitud = "Eventos";
          $concepto='<p class="concepto">LIQUIDACION DE VIABILIDAD PARA REALIZACIÓN DE EVENTOS,REALIZACIÓN DE EVENTO CON COSTO DE PROYECTO : '.$valor_evento[0]["value"].' pesos Colombianos MLV, PARA '.$duracion[0]["value"].' DÍAS, SEGÚN SOLICITUD #'.$sec.'</p> <p> Detalle del evento:</p> <p>'.$descripcion_evento[0]["value"].'<p>';
          $descripcion_evento = $node->get('field_descripcion_evento')->getValue();
          break;
     
    }
*/
     
      /*
      $concepto_rsn = '<p class="concepto">Liquidación Evaluación Rumba Segura</p>
      <p>Detalle del Establecimiento: <p> Nombre Establecimiento: '. $field_nombre_establecimiento[0]["value"].'<p>
      <p> Dirección del Establecimiento'.$field_direccion_del_predio[0]["value"] .'<p>
      <p> Total Metros Cuadrados :'.$cantidad[0]["value"].'</p>';
      $concepto_AF = '<p class="concepto">LIQUIDACIÓN DE EVALUACIÓN TECNICA PARA APROVECHAMIENTO FORESTAL,TALA PODA Y/O TRASLADO DE '.$cantidad[0]["value"].' ÁRBOLES, SEGÚN SOLICITUD CON  #'.$sec.'</p>';

      
      $concepto_pf ='<p class="concepto">LIQUIDACION POR CONCEPTO DE  VIABILIDAD AMBIENTAL  PARA LA PUBLICIDAD EXTERIOR VISUAL FIJA PARA '.$cantidad[0]["value"].' VALLAS, CON UN COSTO DE REALIZACIÓN DE INVERSIÓN DE IMPLEMENTACION DE PROYECTO DE  : '.$valor_evento[0]["value"].' PARA LAS DIRECCIONES :'.$field_detalle[0]["value"].', SEGÚN SOLICITUD #'.$sec.'</p>' ;

      */

      /*
      $html= ' <style>

      .page-title
      display: none;
      }
      .layout.layout--threecol-section.layout--threecol-section--33-34-33 {
      border: 1px solid #000;
      }
      .field.field--name-field-enlace-externo.field--type-link.field--label-hidden.field__item {
      text-align: center;
      }
      .layout__region.layout__region--first {
      text-align: center;
      }

      .barcode.barcode-codabar {
      padding: 1.1em 0.6em;
      border: 1px solid #ccc;
  
      width: 97%;
   
      }
      tr td, tr th {
      padding: 0;
      text-align: left;
      border: 1px solid #000;
      }
      th, td {
      border: 1px solid black;
      }
      p {
      margin: 0;
      }
      </style>



      <table>
      <tbody>

       <tr>
      <td rowspan="5">
      EPA | Zona Liquidaciones
      </td>
        </tr>
         <td colspan="3">
         <p>Establecimiento Público Ambiental EPA-Cartagena</p>
         </td>
       </tr>
       <tr>
         <td colspan="3">
         <p>Nit 806013999-2</p>
         </td>
       </tr>
       <tr>
         <td colspan="3">
         <p>Subdirección Administrativa y Financiera</p>
         </td>
       </tr>
       <tr>
         <td colspan="3">
         <p>Manga Calle 4 AVENIDA EDIFICIO SEAPORT</p>
         </td>
       </tr>
       <tr>
         <td colspan="3">
         <p>Liquidación No '.$sec.'</p>
         </td>
       </tr>
       <tr>
       <td ><p>FECHA:</p></td>
       <td  colspan="3">
       <p>'.date("Y/m/d").'</p>
       </td>
      </tr>
      <tr>
      <td ><p>ASUNTO:</p></td>
      <td  colspan="3">
      <p>VIABILIDAD DE '.$tipo_de_solicitud.'</p>
      </td>
      </tr>
      <tr>
      <td ><p>PETICIONARIO / EMPRESA:</p></td>
      <td  colspan="3">
      <p>'.$name_contrib[0]["value"].'</p>
      </td>
      </tr>
      <tr>
      <td ><p>Id Solicitante:</p></td>
      <td  colspan="3">
      <p>'.$id_contribuyente[0]["value"].'</p>
      </td>
      </tr>

      <tr>
      <td ><p>DIRECCION:</p></td>
      <td  colspan="3">
      <p>'.$dir_correspondecia_contrib[0]["value"].'</p>
      </td>
      </tr>

      <tr>
      <td ><p>CORREO:</p></td>
      <td  colspan="3">
      <p>'.$email_cotrib[0]["value"].'</p>
      </td>
      </tr>
      <tr>
      <td ><p>TELÉFONO:</p></td>
      <td  colspan="3">
      <p>'.$tmovil[0]["value"].'</p>
      </td>
      </tr>
       <tr>
         <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 107 de 17 de febrero de 2021 para este monto de proyecto: </p></td>
         <td colspan="3">
         <p>$ '.$valor_tarifa[0]["value"].'</p>
         </td>
       </tr>
       <tr>
         <td><p>VALOR EVENTO</p></td>
         <td>
         <p> $'.$valor_evento[0]["value"].'</p>
         </td>
         <td >TOTAL LIQUIDACIÓN</td>
         <td >
         <p style="
      font-weight: bold;
      ">$ '.$valor[0]["value"].'</p>
         </td>
       </tr>
       <tr>
         <td colspan="4">
         <p>CONSIDERACIONES</p>

         <p>Categorización de profesionales con base en la Resolución 1280 de 2010 del MAVDT y afectados por un factor multiplicador Factor de administración de acuerdo a la resolución 212 de 2004 del MAVDT</p>

         <p>Esta suma deberá&nbsp;consignarse en la Cuenta de Ahorros No. 43300400033-0 del Banco GNB sudameris, a favor del EPA-Cartagena. Para efectos de acreditar la cancelación de los costos indicados, el usuario deberá presentar original del recibo de consignación, y entregar copia</p>

         <p>Favor no hacer retención por ningún concepto, somos no contribuyentes Según Art. 23 Art 369 y Ley 633 de 2000, Art. 5</p>
         </td>
       </tr>
       <tr>
         <td colspan="4">
         <p>CONCEPTO</p>

         <div class="concepto">
         <p class="concepto">'.$concepto.'</p>
         </div>
         </td>
       </tr>

      </tbody>
      </table>

      ';

      $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/sites/tmp']);



      $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-L']);
      $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);

      $mpdf->SetHTMLHeader('
      <div style="text-align: right; font-weight: bold;">
         EPA
      </div>','O');
      $mpdf->SetHTMLFooter('
     <table width="100%">
         <tr>
             <td width="33%">{DATE j-m-Y}</td>
             <td width="33%" align="center">{PAGENO}/{nbpg}</td>
             <td width="33%" style="text-align: right;">EPA</td>
         </tr>
     </table>
     <table class="items" width="100%" cellpadding="8" border="1">
     <thead>
       <tr>
         <td>Contenido de la clave de pago</td>
         <td>'.$code_content.'</td>

       </tr>
     </thead>
     <tbody>
     <tr>

     <td>Clave de Pago</td>
     <td class="barcodecell"><barcode code="'.$code.'" type="EAN128B" class="barcode" /></td>
     </tr>
     </tbody>
     </table>

     ');

     $mpdf->WriteHTML($html);
     $filename = $sec . '.pdf';
$destinationDirectory = 'private://'; // Change this to your desired private destination
$filePath = $destinationDirectory . $filename;

     $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);



    // Load the generated PDF file
   $fileContent = file_get_contents($filePath);
    $pdfFile = file_save_data($fileContent, 'private://' . $filename, FileSystemInterface::EXISTS_REPLACE);

    $node->get('field_liquidacion')->setValue(['target_id' => $pdfFile->id()]);



    $node->save();


    */


    $node->save();

    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return FALSE;
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

}
