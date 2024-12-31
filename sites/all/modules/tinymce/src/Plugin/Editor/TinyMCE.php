<?php

namespace Drupal\tinymce\Plugin\Editor;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Plugin\EditorBase;
use Drupal\editor\Entity\Editor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a TinyMCE-based text editor for Drupal.
 *
 * @Editor(
 *   id = "tinymce",
 *   label = @Translation("TinyMCE"),
 *   supports_content_filtering = TRUE,
 *   supports_inline_editing = TRUE,
 *   is_xss_safe = FALSE,
 *   supported_element_types = {
 *     "textarea"
 *   }
 * )
 */
class TinyMCE extends EditorBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;


  /**
   * Constructs a \Drupal\tinymce\Plugin\Editor\TinyMCE object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getJSSettings(Editor $editor) {
    $default_Settings = $this->getDefaults();

    $customSettings = ($settings = $editor->getSettings())
      && !empty($settings['tinymce_editor_settings'])
      ? $settings['tinymce_editor_settings']
      : Json::encode($default_Settings);

    return [
      'json' => Json::decode($customSettings),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'tinymce/tinymce',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $editor = $form_state->get('editor');
    $settings = $editor->getSettings();

    $default_Settings = $this->getDefaults();

    $form['tinymce_editor_settings'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Editor settings'),
      '#default_value' => $settings['tinymce_editor_settings'] ?? Json::encode($default_Settings),
      '#description' => $this->t('Custom settings for the editor. Please see <a href=":example" target="_blank">this page for additional documentation</a>.<br/>Note that you need to register your domain (have an API key) to remove the notice, <a href=":api-key" target="_blank">see more details here</a>.', [':example' => 'https://www.tiny.cloud/docs/demo/full-featured/', ':api-key' => 'https://www.tiny.cloud/docs/quick-start/#step3addyourapikey']),
      '#rows' => 20,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaults() {
    return [
      'plugins' => 'print preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
      'mobile' => [
        'plugins' => 'print preview importcss tinydrive searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons',
      ],
      'menubar' => 'file edit view insert format tools table tc help',
      'toolbar' => 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | image media link anchor codesample | ltr rtl',
      'autosave_ask_before_unload' => TRUE,
      'autosave_interval' => '30s',
      'autosave_prefix' => '{path}{query}-{id}-',
      'autosave_restore_when_empty' => FALSE,
      'autosave_retention' => '2m',
      'importcss_append' => TRUE,
      'image_advtab' => TRUE,
      'image_caption' => TRUE,
      'image_title' => TRUE,
      'automatic_uploads' => TRUE,
      'images_upload_url' => '/tinymce/upload',
      'quickbars_selection_toolbar' => 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
      'noneditable_noneditable_class' => 'mceNonEditable',
      'toolbar_mode' => 'sliding',
      'contextmenu' => 'link image imagetools table',
      'skin' => 'oxide',
      'content_css' => 'default',
    ];
  }

}
