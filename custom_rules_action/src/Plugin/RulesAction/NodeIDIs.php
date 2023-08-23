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

    /** Obteniendo el field_consecutivo_factura del nodo creado */
  $consecutivo_facturas = $node->get('field_consecutivo_liquidacion')->getValue();
  $sec ="01"."0".$consecutivo_facturas[0]["value"].date('Y');
    $node->setTitle($sec);
     $type = "Se ha creado la Liquidación # ".$sec;
       \Drupal::messenger()->addMessage(t('Liquidación Creada'), 'status');

       $html= "Test";
       $mpdf = new \Mpdf\Mpdf(['tempDir' => 'sites/default/files/tmp']);
       $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter-L']);
       $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
       $mpdf->SetHTMLHeader('
      <div style="text-align: right; font-weight: bold;">
         EPA
      </div>','O');
  
       $mpdf->SetHTMLFooter('
      Test
  
      ');
  
      $mpdf->WriteHTML($html);

// Your text content
$textContent = "This is the content of the text file.\nSecond line.";

// Specify the file path where you want to create the text file
$filePath = 'private://example.txt'; // Replace with your desired path

// Create the text file
$bytesWritten = file_put_contents($filePath, $textContent);

if ($bytesWritten !== false) {
  // File created successfully
  drupal_set_message("Text file created successfully at $filePath.");
}
else {
  // Error creating file
  drupal_set_message("Error creating text file.", 'error');
}
 

/*



   
     $mpdf->Output($sec.'.pdf', \Mpdf\Output\Destination::FILE);
   // $file = $mpdf->Output($sec.'.pdf', 'D');
   $node->set('field_id_file', $mpdf);
        */

    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
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
