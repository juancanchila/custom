<?php

namespace Drupal\custom_rules_action_establecimientos\Plugin\RulesAction;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Url;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

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
 * Provides a 'Establecimiento action' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action_establecimiento",
 *   label = @Translation("Establecimiento ID"),
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
class EstablecimientoID extends RulesActionBase
{


 /**
   * Executes the action with the given context.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to modify.
   *
   */
  //number_format( $valor_evento, 2, ',', '.');
  protected function doExecute(NodeInterface $node) {

// consultar si hay visitas para la vigencia actual
// si no hay visitas crearlas
// notificar a administrativa y al ususario

$type = "Nuevo Establecimiento Creado ";
                \Drupal::messenger()->addMessage(t($type),'error');


                // Define los valores del nodo que deseas crear.
$visita = new \Drupal\node\Entity\Node([
  'type' => 'visita_cs', // Cambia 'article' al tipo de contenido que desees crear.
  'title' => 'Mi nodo programático',
  'body' => [
    'value' => 'Este es el contenido del nodo programático.',
    'format' => 'full_html', // Formato de texto (puedes cambiarlo según tus necesidades).
  ],
]);

// Guarda el nodo en la base de datos.
$visita->save();
   // $node->save();






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
