<?php

namespace Drupal\custom_rules_action_pdf\Plugin\RulesAction;

use Drupal\user\UserInterface;
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
 *   id = "custom_rules_action_pdf",
 *   label = @Translation("get pdf"),
 *   category = @Translation("Node"),
 * context_definitions = {
 *  "user" = @ContextDefinition("entity:user", label = @Translation("User")),
 *   }
 * )
 *
 */


class NodeIDIs_pdf extends RulesActionBase
{
 /**
   * Executes the action with the given context.
   *
   * @param \Drupal\node\UserInterface $node
   *   The node to modify.
   *
   */
  protected function doExecute(UserInterface $user) {

    $message_info = "Se ha actualizado la Liquidación # ";
    \Drupal::messenger()->addMessage(t( $message_info), 'warning');
    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }



}
