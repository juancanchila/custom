<?php

namespace Drupal\custom_rules_action\Plugin\RulesAction;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a 'UsersRole is' condition.
 *
 * @RulesAction(
 *   id = "custom_rules_action_users",
 *   label = @Translation("UsersRole is"),
 *   category = @Translation("User"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user entity to modify."),
 *       assignment_restriction = "selector"
 *     )
 *   }
 * )
 */
class UsersRole extends RulesActionBase {

  /**
   * {@inheritdoc}
   */
  protected function doExecute(EntityInterface $user) {
    // Check if the user is being created.
    if ($user->isNew()) {
      // Get the value of field_grado.
      $field_grado = $user->get('field_grado')->getValue();

      // If field_grado has a value.
      if (!empty($field_grado)) {
        // Set the password as the value of field_identificacion.
        $password = $user->get('field_identificacion')->value;

        // Set the password for the user.
        $user->setPassword($password);

        // Assign the role "Planta" to the user.
        $user->addRole('planta');

        // Save the user entity.
        $user->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The user entity should be auto-saved after the execution.
    return TRUE;
  }

}
