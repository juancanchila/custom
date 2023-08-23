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

use Drupal\Core\StreamWrapper\PrivateStream;


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


      $path = "sites/default/files/tmp/mpdf";
     

      $output = 'Test Text';
      $file_save_path_stream_directory =  'private://gmt';
      
      file_prepare_directory($file_save_path_stream_directory, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);
      
      $fileLocation = $file_save_path_stream_directory . '/test.txt';
      
      $file = file_save_data($output, $fileLocation, FILE_EXISTS_REPLACE); 

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
