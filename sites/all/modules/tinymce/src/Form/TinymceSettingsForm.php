<?php

namespace Drupal\tinymce\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements a TinyMCE settings form.
 */
class TinymceSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tinymce_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['tinymce.settings'];
  }

  /**
   * Chosen configuration form.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('tinymce.settings');

    $form['tinymce_self_hosted'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Is tinyMCE library self hosted ?'),
      '#default_value' => $config->get('tinymce_self_hosted'),
      '#description' => $this->t('Check this if the tinyMCE library is installed locally.'),
    ];

    $form['tinymce_javascript_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('tinymce.min.js full path'),
      '#default_value' => $config->get('tinymce_javascript_path'),
      '#description' => $this->t('The full path to tinymce.min.js<br>Example:<ul><li>Self hosted: /libraries/tinymce/tinymce.min.js</li><li>CDN hosted (free): https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js</li><li>CDN hosted (Premium): https://cdn.tiny.cloud/1/{api-key}/tinymce/5/tinymce.min.js</li></ul>'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('tinymce.settings');
    $config->set('tinymce_self_hosted', $form_state->getValue('tinymce_self_hosted'));
    $config->set('tinymce_javascript_path', $form_state->getValue('tinymce_javascript_path'));
    $config->save();

    parent::submitForm($form, $form_state);

    /** @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface $tagInvalidator */
    $tagInvalidator = \Drupal::service('cache_tags.invalidator');
    $tagInvalidator->invalidateTags(['library_info']);
  }

}
