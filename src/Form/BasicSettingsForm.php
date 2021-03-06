<?php
namespace Drupal\auth0\Form;
/**
 * @file
 * Contains \Drupal\auth0\Form\BasicSettingsForm.
 */

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\auth0\Util\AuthHelper;

/**
 * This forms handles the basic module configurations.
 */
class BasicSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'auth0_basic_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = \Drupal::service('config.factory')->get('auth0.settings');

    $form['auth0_client_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Client id'),
      '#default_value' => $config->get('auth0_client_id', ''),
      '#description' => t('Application id, copy from the auth0 dashboard.'),
      '#required' => TRUE,
    );
    $form['auth0_client_secret'] = array(
      '#type' => 'textfield',
      '#title' => t('Client secret'),
      '#default_value' => $config->get('auth0_client_secret', ''),
      '#description' => t('Application secret, copy from the auth0 dashboard.'),
      '#required' => TRUE,
    );
    $form['auth0_secret_base64_encoded'] = array(
      '#type' => 'checkbox',
      '#title' => t('Client Secret is Base64 Encoded'),
      '#default_value' => $config->get('auth0_secret_base64_encoded'),
      '#description' => t('This is stated below the client secret in your Auth0 Dashboard for the client.  If your client was created after September 2016, this should be false.')
    );
    $form['auth0_domain'] = array(
      '#type' => 'textfield',
      '#title' => t('Domain'),
      '#default_value' => $config->get('auth0_domain', ''),
      '#description' => t('Your Auth0 domain, you can see it in the auth0 dashboard.'),
      '#required' => TRUE,
    );
    $form[ AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM ] = array(
      '#type' => 'select',
      '#title' => t('JWT Signature Algorithm'),
      '#options' => [
        'HS256' => $this->t('HS256'),
        'RS256' => $this->t('RS256'),
      ],
      '#default_value' => $config->get( AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM )
        ? $config->get( AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM )
        : AUTH0_DEFAULT_SIGNING_ALGORITHM,
      '#description' => t('Your JWT Signing Algorithm for the ID token. RS256 is recommended and must be set in the advanced settings for this client under the OAuth tab.'),
      '#required' => TRUE,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('auth0_client_id'))) {
      $form_state->setErrorByName('auth0_client_id', $this->t('Please complete the application Client ID'));
    }

    if (empty($form_state->getValue('auth0_client_secret'))) {
      $form_state->setErrorByName('auth0_client_secret', $this->t('Please complete the application Client Secret'));
    }

    if (empty($form_state->getValue('auth0_domain'))) {
      $form_state->setErrorByName('auth0_domain', $this->t('Please complete your Auth0 domain'));
    }

    if (empty($form_state->getValue(AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM))) {
      $form_state->setErrorByName(AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM, $this->t('Please complete your Auth0 Signature Algorithm'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = \Drupal::service('config.factory')->getEditable('auth0.settings');
    $config->set('auth0_client_id', $form_state->getValue('auth0_client_id'))
            ->set('auth0_client_secret', $form_state->getValue('auth0_client_secret'))
            ->set('auth0_domain', $form_state->getValue('auth0_domain'))
            ->set(AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM, $form_state->getValue(AuthHelper::AUTH0_JWT_SIGNING_ALGORITHM))
            ->set('auth0_secret_base64_encoded', $form_state->getValue('auth0_secret_base64_encoded'))
            ->save();
  }

}
