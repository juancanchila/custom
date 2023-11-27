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



  // Get the URL of the current node.
  $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()]);

  // Create a RedirectResponse object.
  $redirect = new RedirectResponse($url->toString());

  // Return the RedirectResponse object.
  return $redirect;

    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {

    return FALSE;
  }



}
