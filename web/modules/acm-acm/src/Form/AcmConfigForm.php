<?php

namespace Drupal\acm\Form;

use Drupal\acm\AcmCredentialsManager;
use Drupal\acm\AcmInfoManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AcmConfigForm.
 *
 * Configuration form for endpoints.
 *
 * @package Drupal\acm\Form
 */
class AcmConfigForm extends ConfigFormBase {

  /**
   * The info manager.
   *
   * @var \Drupal\acm\AcmInfoManager
   */
  protected $infoManager;

  /**
   * The credentials manager.
   *
   * @var \Drupal\acm\AcmCredentialsManager
   */
  protected $credentialsManager;

  /**
   * AcmConfigForm constructor.
   *
   * @param \Drupal\acm\AcmInfoManager $info_manager
   *   The info manager.
   * @param \Drupal\acm\AcmCredentialsManager $credentials_manager
   *   The credentials manager.
   */
  public function __construct(AcmInfoManager $info_manager, AcmCredentialsManager $credentials_manager) {
    $this->infoManager = $info_manager;
    $this->credentialsManager = $credentials_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('acm.info_manager'),
      $container->get('acm.credentials_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'acm_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $envs = $this->infoManager->getEnvironments();

    $env_options = [];
    foreach ($envs as $env) {
      $env_options[$env->getName()] = $env->getLabel();
    }
    if (empty($env_options)) {
      $form['current_environment_message'] = [
        '#markup' => $this->t('Environments information is not defined in <code>hook_acm_environments_info()</code> (see <code>acm.api.php</code> file for reference).'),
      ];
    }
    else {
      $form['current_environment'] = [
        '#type' => 'radios',
        '#title' => $this->t('Current API environment'),
        '#description' => $this->t('Select currently active API environment.<br/>Only credentials and endpoints from this environment will be used.<br/>Changing this value will not reset saved credentials for non-active environments.'),
        '#options' => $env_options,
        '#required' => TRUE,
        '#default_value' => $this->credentialsManager->getCurrentEnvironment(),
      ];
    }

    $creds = $this->infoManager->getCredentials();

    $form['tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-' . $this->credentialsManager->getCurrentEnvironment(),
    ];

    foreach ($envs as $env) {
      $key = $env->getName();
      $form[$key] = [
        '#type' => 'details',
        '#title' => $env->getLabel(),
        '#group' => 'tabs',
        '#tree' => TRUE,
      ];

      foreach ($creds as $cred) {
        $form[$key][$cred->getName()] = [
          '#type' => 'details',
          '#open' => TRUE,
          '#title' => $cred->getLabel(),
          '#tree' => TRUE,
        ];

        $this->renderCredParameter($form[$key][$cred->getName()], $cred, $env);
      }
    }

    $encrypt_profile_options = [
      '' => $this->t('None'),
    ];
    $encrypt_profiles = $this->credentialsManager->getAllEncryptProfiles();
    foreach ($encrypt_profiles as $encrypt_profile) {
      $encrypt_profile_options[$encrypt_profile->id()] = $encrypt_profile->label();
    }

    $encrypt_profile_description = $this->t('Select Encrypt profile to encrypt credentials.<br/><strong>Changing Encrypt profile will reset all values!</strong>');
    if (empty(array_filter(array_keys($encrypt_profile_options)))) {
      $encrypt_profile_description .= '<br/>';
      $encrypt_profile_description .= $this->t('There are no encryption profiles configured. @create_link to securely store your credentials.', [
        '@create_link' => Link::createFromRoute($this->t('Create a new Encrypt profile'), 'entity.encryption_profile.add_form')->toString(),
      ]);
    }
    $form['encrypt_profile'] = [
      '#title' => $this->t('Encryption profile'),
      '#type' => 'select',
      '#description' => $encrypt_profile_description,
      '#options' => $encrypt_profile_options,
      '#default_value' => $this->credentialsManager->getEncryptProfileName(),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Render a single credential parameter.
   */
  protected function renderCredParameter(&$form, $cred, $env) {
    $default_values = $this->credentialsManager->getCredential($cred->getName(), $env->getName());

    foreach ($cred->getParameters() as $parameter) {
      $form[$parameter->getName()] = [
        '#type' => $parameter->getType(),
        '#title' => $parameter->getLabel(),
        '#default_value' => $default_values[$parameter->getName()] ?? NULL,
      ];
    }

    $endpoints = $this->infoManager->getEndpoints();

    $options = [];
    foreach ($endpoints as $endpoint) {
      $options[$endpoint->getName()] = $endpoint->getLabel();
    }

    $form['endpoint'] = [
      '#type' => 'select',
      '#title' => $this->t('Endpoint'),
      '#options' => $options,
      '#default_value' => !empty($default_values['endpoint']) ? $default_values['endpoint']->getName() : key($options),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->credentialsManager->setCurrentEnvironment($form_state->getValue('current_environment'));
    $this->credentialsManager->setEncryptProfileName($form_state->getValue('encrypt_profile'));

    $envs = $this->infoManager->getEnvironments();

    foreach ($envs as $env) {
      $values = $form_state->getValue($env->getName());
      if (empty($values)) {
        continue;
      }

      foreach ($values as $name => $data) {
        $this->credentialsManager->setCredential($name, $data, $env->getName());
      }
    }
    $this->credentialsManager->saveAllCredentials();

    parent::submitForm($form, $form_state);
  }

}
