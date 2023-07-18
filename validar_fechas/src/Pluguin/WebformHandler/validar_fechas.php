namespace Drupal\validar_fechas\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;


/**
 * Webform Date Validation handler.
 *
 * @WebformHandler(
 *   id = "handler_custom",
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

        parent::validateForm($form, $form_state, $webform_submission);

        if (!$form_state->hasAnyErrors()) {
            //Tu validación aquí
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

        if (!$form_state->hasAnyErrors()) {
            //Tu logica despues del submit
        }
    }

}