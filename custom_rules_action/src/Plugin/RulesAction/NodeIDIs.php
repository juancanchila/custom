<?php

namespace Drupal\custom_rules_action\Plugin\RulesAction;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a 'Node ID is' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action",
 *   label = @Translation("Node ID is"),
 *   category = @Translation("Node"),
 *   context = {
 *        "message" = @ContextDefinition("string",
 *       label = @Translation("Message"),
 *       description = @Translation("write your message"),
 *     ),
 *     "type" = @ContextDefinition("string",
 *       label = @Translation("Message type"),
 *       description = @Translation("Message type: status, warning or error "),
 *     ),
 *   }
 * )
 *
 */
class NodeIDIs extends RulesActionBase
{
    /**
     * @param $name
     */
    protected function doExecute($message, $type)
    {
        \Drupal::messenger()->addMessage(t($message), $type);
    }



}
