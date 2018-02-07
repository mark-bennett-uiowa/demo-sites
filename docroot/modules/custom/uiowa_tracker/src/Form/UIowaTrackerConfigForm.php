<?php

namespace Drupal\uiowa_tracker\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements the admin form for the tracker.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class UIowaTrackerConfigForm extends ConfigFormBase {

  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, Connection $connection) {
    parent::__construct($config_factory);
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['uiowa_tracker.settings'];
  }

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('uiowa_tracker.settings');

    $form['uiowa_tracker_pathlist'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enter paths of all nodes to track authenticated user views (one per line)'),
      '#description' => $this->t('The paths (e.g. /about/contact) of all nodes that require tracking of authenticated user views, one path per line. ADD leading or trailing slashes.'),
      '#required' => TRUE,
      '#rows' => 15,
      '#default_value' => $config->get('uiowa_tracker_pathlist'),
    ];

    $form['uiowa_tracker_clearlog_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Actions'),
      '#description' => $this->t("In order to clear all entries in the tracking log table, click the \'Clear tracking log\' button. This will remove ALL entries in the table, so use with caution."),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];

    // Add a submit button that handles the submission of the form.
    $form['uiowa_tracker_clearlog_fieldset']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['uiowa_tracker_clearlog_fieldset']['uiowa_tracker_clearlog'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear tracking log'),
      '#attributes' => ['onclick' => 'if(!confirm("Are you sure you want to delete all entries in the tracking log?")){return false;}'],
      '#submit' => ['::uiowaTrackerClearlogFormSubmit'],
    ];

    return $form;
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller.  it must
   * be unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'uiowa_tracker_config_form';
  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('uiowa_tracker.settings');
    $config->set('uiowa_tracker_pathlist', $form_state->getValue('uiowa_tracker_pathlist'));
    $config->save();

    drupal_set_message($this->t('The path list of tracked nodes has been saved.'));

    return parent::submitForm($form, $form_state);
  }

  /**
   * Clear log submit event.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function uiowaTrackerClearlogFormSubmit(array &$form, FormStateInterface $form_state) {
    $this->connection->delete('uiowa_tracker_log')->execute();
    drupal_set_message($this->t('All entries in the University of Iowa tracker log have been cleared.'));
  }

}
