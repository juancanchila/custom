<?php

namespace Drupal\custom_rules_action\Plugin\RulesAction;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\EntityInterface;
use \Drupal\node\Entity\Node;

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
 *     "title" = @ContextDefinition("string",
 *       label = @Translation("Title"),
 *       description = @Translation("The new title.")
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
   * @param string $title
   *   The new title.
   */
  protected function doExecute(NodeInterface $node,$title) {

    $message = $node->body->value;
    $node->setTitle($title);
     $type = "Alert";
        \Drupal::messenger()->addMessage(t($message), $type);
    }

 /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }

}
