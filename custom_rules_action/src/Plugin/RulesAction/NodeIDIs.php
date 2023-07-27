<?php

namespace Drupal\custom_rules_action\Plugin\RulesAction;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\EntityInterface;
/**
 * Provides a 'Node ID is' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action",
 *   label = @Translation("Node ID is"),
 *   category = @Translation("Node"),
 *   context = {
*    "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity.")
 *     )
 *   }
 * )
 *
 */
class NodeIDIs extends RulesActionBase
{
    /**
     * @param $name
     */

     
    protected function doExecute(EntityInterface $entity)
    {

        $entity->get('field_pattern_type')->getValue();
        \Drupal::logger('pattern_rules')->notice("Logging Rules Action");
        \Drupal::logger('pattern_rules')->notice($entity);
        
        $message = "Creado";
     $type = "Alert";
        \Drupal::messenger()->addMessage(t($message), $type);
    }



}
