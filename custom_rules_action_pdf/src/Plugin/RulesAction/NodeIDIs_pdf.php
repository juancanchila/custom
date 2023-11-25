<?php

namespace Drupal\custom_rules_action_pdf\Plugin\RulesAction;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedCallbackInterface;



/**
 * Provides a 'Node ID is' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action_pdf",
 *   label = @Translation("get pdf"),
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
class NodeIDIs_pdf extends RulesActionBase
{
 /**
   * Executes the action with the given context.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to modify.
   *
   */
  protected function doExecute(NodeInterface $node) {
//validar para notificar
  $type = "Se ha creado la Liquidación # ";

    // Get the URL of the current node.
    $destination_path = "/node/".$node->id();

    \Drupal::messenger()->addMessage(t($destination_path),'error');


    // Get the URL object using the trusted callback resolver.
$url = \Drupal::service('router.trusted_callback_resolver')->getUrl($destination_path);

// Create a RedirectResponse with the destination URL.
$response = new RedirectResponse($url->toString());

// Send the response to perform the redirect.
$response->send();

    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return FALSE;
  }



}
