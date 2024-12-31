<?php

namespace Drupal\Tests\tinymce\Functional;

use Drupal\editor\Entity\Editor;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests loading of CKEditor.
 *
 * @group ckeditor
 */
class TinyMCELoadingTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['filter', 'editor', 'tinymce', 'node'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * An untrusted user with access to only the 'plain_text' format.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $untrustedUser;

  /**
   * A normal user with access to the 'plain_text' and 'filtered_html' formats.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $normalUser;

  protected function setUp(): void {
    parent::setUp();

    // Create text format, associate TinyMCE.
    $filtered_html_format = FilterFormat::create([
      'format' => 'filtered_html',
      'name' => 'Filtered HTML',
      'weight' => 0,
      'filters' => [],
    ]);
    $filtered_html_format->save();
    $editor = Editor::create([
      'format' => 'filtered_html',
      'editor' => 'tinymce',
    ]);
    $editor->save();

    // Create a second format without an associated editor so a drop down select
    // list is created when selecting formats.
    $full_html_format = FilterFormat::create([
      'format' => 'full_html',
      'name' => 'Full HTML',
      'weight' => 1,
      'filters' => [],
    ]);
    $full_html_format->save();

    // Create node type.
    $this->drupalCreateContentType([
      'type' => 'article',
      'name' => 'Article',
    ]);

    $this->untrustedUser = $this->drupalCreateUser([
      'create article content',
      'edit any article content',
    ]);
    $this->normalUser = $this->drupalCreateUser([
      'create article content',
      'edit any article content',
      'use text format filtered_html',
      'use text format full_html',
    ]);
  }

  /**
   * Tests loading of TinyMCE CSS, JS and JS settings.
   */
  public function testLoading() {
    // The untrusted user:
    // - has access to 1 text format (plain_text);
    // - doesn't have access to the filtered_html text format, so: no text editor.
    $this->drupalLogin($this->untrustedUser);
    $this->drupalGet('node/add/article');
    list($settings, $editor_settings_present, $editor_js_present, $body, $format_selector) = $this->getThingsToCheck();
    $this->assertFalse($editor_settings_present, 'No Text Editor module settings.');
    $this->assertFalse($editor_js_present, 'No Text Editor JavaScript.');
    $this->assertCount(1, $body, 'A body field exists.');
    $this->assertCount(0, $format_selector, 'No text format selector exists on the page.');
    $hidden_input = $this->xpath('//input[@type="hidden" and contains(@class, "editor")]');
    $this->assertCount(0, $hidden_input, 'A single text format hidden input does not exist on the page.');
    // Verify that TinyMCE glue JS is absent.
    $this->assertSession()->responseNotContains(drupal_get_path('module', 'tinymce') . '/js/tinymce.js');

    // On pages where there would never be a text editor, TinyMCE JS is absent.
    $this->drupalGet('user');
    $this->assertSession()->responseNotContains(drupal_get_path('module', 'tinymce') . '/js/tinymce.js');
    $this->drupalLogout();

    // The normal user:
    // - has access to 2 text formats;
    // - does have access to the filtered_html text format, so: TinyMCE.
    $this->drupalLogin($this->normalUser);
    $this->drupalGet('node/add/article');
    list($settings, $editor_settings_present, $editor_js_present, $body, $format_selector) = $this->getThingsToCheck();
    $tinymce_plugin = $this->container->get('plugin.manager.editor')->createInstance('tinymce');
    $editor = Editor::load('filtered_html');
    $expected = [
      'formats' => [
        'filtered_html' => [
          'format' => 'filtered_html',
          'editor' => 'tinymce',
          'editorSettings' => $tinymce_plugin->getJSSettings($editor),
          'editorSupportsContentFiltering' => TRUE,
          'isXssSafe' => FALSE,
        ],
      ],
    ];
    $this->assertTrue($editor_settings_present, "Text Editor module's JavaScript settings are on the page.");
    $this->assertEquals($expected, $settings['editor'], "Text Editor module's JavaScript settings on the page are correct.");
    $this->assertTrue($editor_js_present, 'Text Editor JavaScript is present.');
    $this->assertCount(1, $body, 'A body field exists.');
    $this->assertCount(1, $format_selector, 'A single text format selector exists on the page.');
    $specific_format_selector = $this->xpath('//select[contains(@class, "filter-list") and @data-editor-for="edit-body-0-value"]');
    $this->assertCount(1, $specific_format_selector, 'A single text format selector exists on the page and has a "data-editor-for" attribute with the correct value.');
    $tinymce_js = $this->xpath('//script[contains(@src, "tinymce.js") or contains(@src, "tinymce.min.js")]');
    $this->assertNotCount(0, $tinymce_js, 'TinyMCE glue library is present.');
  }

  protected function getThingsToCheck() {
    $settings = $this->getDrupalSettings();
    return [
      // JavaScript settings.
      $settings,
      // Editor.module's JS settings present.
      isset($settings['editor']),
      // Editor.module's JS present. Note: tinymce/tinymce depends on
      // editor/drupal.editor, hence presence of the former implies presence of
      // the latter.
      !empty($this->xpath('//script[contains(@src, "tinymce.js") or contains(@src, "tinymce.min.js")]')),
      // Body field.
      $this->xpath('//textarea[@id="edit-body-0-value"]'),
      // Format selector.
      $this->xpath('//select[contains(@class, "filter-list")]'),
    ];
  }

}
