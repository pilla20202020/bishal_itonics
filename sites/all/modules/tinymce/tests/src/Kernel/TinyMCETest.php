<?php

namespace Drupal\Tests\tinymce\Kernel;

use Drupal\Component\Serialization\Json;
use Drupal\KernelTests\KernelTestBase;
use Drupal\editor\Entity\Editor;
use Drupal\filter\Entity\FilterFormat;

/**
 * Tests for the 'TinyMCE' text editor plugin.
 *
 * @group tinymce
 */
class TinyMCETest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'user',
    'filter',
    'editor',
    'tinymce',
    'filter_test',
  ];

  /**
   * An instance of the "TinyMCE" text editor plugin.
   *
   * @var \Drupal\tinymce\Plugin\Editor\TinyMCE
   */
  protected $tinymce;

  /**
   * The Editor Plugin Manager.
   *
   * @var \Drupal\editor\Plugin\EditorManager;
   */
  protected $manager;

  protected function setUp(): void {
    parent::setUp();

    // Install the Filter module.

    // Create text format, associate TinyMCE.
    $filtered_html_format = FilterFormat::create([
      'format' => 'filtered_html',
      'name' => 'Filtered HTML',
      'weight' => 0,
      'filters' => [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<h2 id> <h3> <h4> <h5> <h6> <p> <br> <strong> <a href hreflang>',
          ],
        ],
      ],
    ]);
    $filtered_html_format->save();
    $editor = Editor::create([
      'format' => 'filtered_html',
      'editor' => 'tinymce'
    ]);
    $editor->save();

    // Create "TinyMCE" text editor plugin instance.
    $this->tinymce = $this->container->get('plugin.manager.editor')->createInstance('tinymce');
  }

  /**
   * Tests TinyMCE::getJSSettings().
   */
  public function testGetJSSettings() {
    $editor = Editor::load('filtered_html');
    $query_string = '?0=';

    // No configuration.
    $expected_config = $this->getDefaultInternalConfig();
    $this->assertEquals($expected_config, $this->tinymce->getJSSettings($editor), 'Generated JS settings are correct for default configuration.');

    // Basic config.
    $settings = $editor->getSettings();
    $settings['tinymce_editor_settings'] = Json::encode([
      'plugins' => 'image link media template',
      'toolbar' => 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | image media link ',
    ]);
    $expected_config = [
      'json' => [
        'plugins' => 'image link media template',
        'toolbar' => 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | image media link ',
      ],
    ];
    $editor->setSettings($settings);
    $editor->save();
    $this->assertEquals($expected_config, $this->tinymce->getJSSettings($editor), 'Generated JS settings are correct for customized configuration.');
  }

  protected function getDefaultInternalConfig() {
    return [
      'json' => [
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
      ]
    ];
  }

}
