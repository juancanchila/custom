<?php

namespace Drupal\custom_rules_action_pdf\Plugin\RulesAction;






/**
 * Provides a 'Node ID is' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action_pdf",
 *   label = @Translation("get pdf"),
 *   category = @Translation("Node"),
 * context_definitions = {

 *   }
 * )
 *
 */


class NodeIDIs_pdf extends RulesActionBase
{

  protected function doExecute() {

    $message_info = "Se ha actualizado la Liquidación # ";
    \Drupal::messenger()->addMessage(t( $message_info), 'warning');
    }





}
