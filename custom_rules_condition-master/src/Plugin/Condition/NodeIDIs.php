<?php

namespace Drupal\custom_rules_condition\Plugin\Condition;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides a 'Node ID is' condition.
 *
 * @Condition(
 *   id = "custom_rules_condition_node_id_is",
 *   label = @Translation("Node ID is"),
 *   category = @Translation("Node"),
 *   context = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node")
 *     ),
 *     "ids" = @ContextDefinition("string",
 *       label = @Translation("IDs of Nodes"),
 *       description = @Translation("IDs of Nodes"),
 *       multiple = TRUE
 *     )
 *   }
 * )
 *
 */
class NodeIDIs extends RulesConditionBase
{

    /**
     * @param \Drupal\node\NodeInterface $node
     *   The node to check.
     *
     * @return bool
     *   TRUE if the node id existe in $ids array.
     */
    protected function doEvaluate(NodeInterface $node, $ids)
    {
        return in_array($node->id(),$ids);
    }


}
