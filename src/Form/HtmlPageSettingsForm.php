<?php

namespace Drupal\dp_html\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * Defines a form for documentation page settings.
 *
 * @internal
 */
class HtmlPageSettingsForm extends ConfigFormBase {

  /**
  * {@inheritdoc}
  */
  protected function getEditableConfigNames() {
    return [
      'doc.adminsettings',
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'doc_page_settings';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('doc.adminsettings');

    $form['html'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Html pages'),
      '#description' => $this->t('Enter urls to html files.<br> Images in the file must be linked relative to the file.'),
    ];

    $form['html']['homepage_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Home url'),
      '#default_value' => $config->get('homepage_url'),
      '#required' => TRUE,
    ];

    $form['html']['get_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Getting started url'),
      '#default_value' => $config->get('get_url'),
      '#required' => TRUE,
    ];

    $form['html']['enrollment_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enrollment url'),
      '#default_value' => $config->get('enrollment_url'),
      '#required' => TRUE,
    ];

    $form['html']['contingency_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Contingency url'),
      '#default_value' => $config->get('contingency_url'),
      '#required' => TRUE,
    ];

    $form['html']['privacy_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Privacy url'),
      '#default_value' => $config->get('privacy_url'),
      '#description' => $this->t('Override default Privacy content.')
    ];

    $form['html']['tsandcs_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Terms of use url'),
      '#default_value' => $config->get('tsandcs_url'),
      '#description' => $this->t('Override default Terms of use content.')
    ];



    return parent::buildForm($form, $form_state);
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the url.
    $this->config('doc.adminsettings')
      ->set('homepage_url', $form_state->getValue('homepage_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('get_url', $form_state->getValue('get_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('enrollment_url', $form_state->getValue('enrollment_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('contingency_url', $form_state->getValue('contingency_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('privacy_url', $form_state->getValue('privacy_url'))
      ->save();

    $this->config('doc.adminsettings')
      ->set('tsandcs_url', $form_state->getValue('tsandcs_url'))
      ->save();

    drupal_flush_all_caches();

  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // And check the url if set.
    if ($form_state->getValue('homepage_url') != '' && UrlHelper::isValid($form_state->getValue('homepage_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('homepage_url', $this->t("Homepage URL doesn't look right"));
    }

    if ($form_state->getValue('get_url') != '' && UrlHelper::isValid($form_state->getValue('get_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('get_url', $this->t("Getting started URL doesn't look right"));
    }

    if ($form_state->getValue('enrollment_url') != '' && UrlHelper::isValid($form_state->getValue('enrollment_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('enrollment_url', $this->t("Enrollment URL doesn't look right"));
    }

    if ($form_state->getValue('contingency_url') != '' && UrlHelper::isValid($form_state->getValue('contingency_url'), TRUE) == FALSE) {
      $form_state->setErrorByName('contingency_url', $this->t("Contingency URL doesn't look right"));
    }
  }
}
