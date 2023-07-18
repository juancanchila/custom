namespace Drupal\validar_fechas\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;


/**
 * Webform Date Validation handler.
 *
 * @WebformHandler(
 *   id = "validarfechas",
 *   label = @Translation("My awesome custom handler"),
 *   category = @Translation("Custom"),
 *   description = @Translation("Example of custom handler."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 * )
 */
class validar_fechas extends WebformHandlerBase {

/**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    return true;
  }




}