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

    $message = "test";
    $hoy =new DrupalDateTime( 'now');
    $title = "Creando titulo con rules en la fecha : " ;
    /** Obteniendo el field_consecutivo_factura del nodo creado */
  $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
  $sec ="01"."0".$consecutivo_facturas[0]["value"].date('Y');
    $node->setTitle($sec);
     $type = "Alert";
     //   \Drupal::messenger()->addMessage(t($message), $type);

//Almacenar y notificar


$name_contrib =  "Teste";
$id_contribuyente = "Teste";
$dir_correspondecia_contrib = $node->get('field_direccion_correspondencia')->value;

$email_cotrib = $node->get('field_email_contribuyente')->value;

$valor_tarifa = $node->get('field_valor_tarifa')->value;
$valor_evento = $node->get('field_valor_evento')->value;
$valor_liquidacion = $node->get('field_valor')->value;
$descripcion_evento = $node->get('field_descripcion_evento')->value;



$html= ' <style>

.page-title {
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
/* background: #efefef; */
width: 97%;
/* background: rgba(0,0,0,0.063); */
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
   <p>Liquidación No '.$node->get('title')->getValue().'</p>
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
<p>VIABILIDAD DE EVENTOS</p>
</td>
</tr>
<tr>
<td ><p>PETICIONARIO / EMPRESA:</p></td>
<td  colspan="3">
<p>'.$name_contrib.'</p>
</td>
</tr>
<tr>
<td ><p>Id Solicitante:</p></td>
<td  colspan="3">
<p>'.$id_contribuyente.'</p>
</td>
</tr>

<tr>
<td ><p>DIRECCION:</p></td>
<td  colspan="3">
<p>'.$dir_correspondecia_contrib.'</p>
</td>
</tr>

<tr>
<td ><p>CORREO:</p></td>
<td  colspan="3">
<p>'.$email_cotrib.'</p>
</td>
</tr>
<tr>
<td ><p>TELÉFONO:</p></td>
<td  colspan="3">
<p>'.$tmovil.'</p>
</td>
</tr>
 <tr>
   <td><p>VALOR TARIFA SEGÚN RESOLUCIÓN N° 107 de 17 de febrero de 2021 para este monto de proyecto: </p></td>
   <td colspan="3">
   <p>$ '.$valor_tarifa.'</p>
   </td>
 </tr>
 <tr>
   <td><p>VALOR EVENTO</p></td>
   <td>
   <p> $'.$valor_evento.'</p>
   </td>
   <td >TOTAL LIQUIDACIÓN</td>
   <td >
   <p style="
font-weight: bold;
">$ '.$valor_liquidacion.'</p>
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
   <p class="concepto">LIQUIDACION DE VIABILIDAD PARA REALIZACIÓN DE EVENTOS,REALIZACIÓN DE EVENTO CON COSTO DE PROYECTO : '.$valor_evento.' pesos Colombianos MLV, PARA '.$numero_dias.' DÍAS, SEGÚN SOLICITUD #'.$sec.'</p>
   </div>
   </td>
 </tr>

</tbody>
</table>

<table>
<tbody>

<tr>
<td>
Detalle del evento: <p>'.$descripcion_evento.'<p>



</td>
</tr>


</tbody>

</table>

';


$node->set("body", $html);
$node->body->format = 'full_html';



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


$file = $mpdf->Output($sec.'.pdf', 'D');

    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }

}
