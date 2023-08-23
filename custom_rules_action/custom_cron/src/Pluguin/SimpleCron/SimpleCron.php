<?php

namespace Drupal\custom_cron\Plugin\SimpleCron;

use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\simple_cron\Plugin\SimpleCronPluginBase;

/**
 * Single cron example implementation.
 *
 * @SimpleCron(
 *   id = "custom_cron",
 *   label = @Translation("Example: Single", context = "Simple cron")
 * )
 */
class SimpleCron extends SimpleCronPluginBase {

  use LoggerChannelTrait;

  /**
   * {@inheritdoc}
   */
  public function process(): void {
    $this->getLogger('simple_cron_examples')->info('Simple cron run successfully');
  }

}
