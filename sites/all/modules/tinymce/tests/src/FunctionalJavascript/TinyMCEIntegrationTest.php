<?php

namespace Drupal\Tests\tinymce\FunctionalJavascript;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\editor\Entity\Editor;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\node\Entity\NodeType;

/**
 * Tests the integration of TinyMCE.
 *
 * @group tinymce
 */
class TinyMCEIntegrationTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * The FilterFormat config entity used for testing.
   *
   * @var \Drupal\filter\FilterFormatInterface
   */
  protected $filterFormat;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'tinymce', 'filter'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a text format and associate TinyMCE.
    $this->filterFormat = FilterFormat::create([
      'format' => 'filtered_html',
      'name' => 'Filtered HTML',
      'weight' => 0,
    ]);
    $this->filterFormat->save();

    Editor::create([
      'format' => 'filtered_html',
      'editor' => 'tinymce',
    ])->save();

    // Create a node type for testing.
    NodeType::create(['type' => 'page', 'name' => 'page'])->save();

    $field_storage = FieldStorageConfig::loadByName('node', 'body');

    // Create a body field instance for the 'page' node type.
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'page',
      'label' => 'Body',
      'settings' => ['display_summary' => TRUE],
      'required' => TRUE,
    ])->save();

    // Assign widget settings for the 'default' form mode.
    EntityFormDisplay::create([
      'targetEntityType' => 'node',
      'bundle' => 'page',
      'mode' => 'default',
      'status' => TRUE,
    ])->setComponent('body', ['type' => 'text_textarea_with_summary'])
      ->save();

    $this->account = $this->drupalCreateUser([
      'administer nodes',
      'create page content',
      'use text format filtered_html',
    ]);
    $this->drupalLogin($this->account);
  }

  /**
   * Tests if the fragment link to a textarea works with TinyMCE enabled.
   */
  public function testFragmentLink() {
    $session = $this->getSession();
    $web_assert = $this->assertSession();
    $tinymce_id = '.tox-tinymce';

    $before_url = $session->getCurrentUrl();
    $this->drupalGet('node/add/page');

    $session->getPage();

    // Add a bottom margin to the title field to be sure the body field is not
    // visible.
    $session->executeScript("document.getElementById('edit-title-0-value').style.marginBottom = window.innerHeight*2 +'px';");

    $this->assertSession()->waitForElementVisible('css', $tinymce_id);
    // Check that the TinyMCE-enabled body field is currently not visible in
    // the viewport.
    $web_assert->assertNotVisibleInViewport('css', $tinymce_id, 'topLeft', 'TinyMCE-enabled body field is visible.');

    // Reset the title bottom margin to make the body field visible again.
    $session->executeScript("document.getElementById('edit-title-0-value').style.marginBottom = '0px';");

    // Check that the TinyMCE-enabled body field is visible in the viewport.
    $web_assert->assertVisibleInViewport('css', $tinymce_id, 'topLeft', 'TinyMCE-enabled body field is not visible.');

    // Use JavaScript to go back in the history instead of
    // \Behat\Mink\Session::back() because that function doesn't work after a
    // hash change.
    $session->executeScript("history.back();");

    $after_url = $session->getCurrentUrl();

    // Check that going back in the history worked.
    self::assertEquals($before_url, $after_url, 'History back works.');
  }

  /**
   * Tests if the Image button appears and works as expected.
   */
  public function testImageDialog() {
    $session = $this->getSession();
    $web_assert = $this->assertSession();

    $this->drupalGet('node/add/page');
    $session->getPage();

    // Asserts the Image button is present in the toolbar.
    $web_assert->elementExists('css', '.tox-tinymce .tox-tbtn[title="Insert/edit image"]');

    // Asserts the Tiny cloud notice is displayed and needs to be closed
    // (otherwise the image button will be covered).
    $this->click('.tox-notification__dismiss');

    // Asserts the image dialog opens when clicking on the "More ..." button and
    // the Image button.
    $this->click('.tox-tbtn[title="More..."]');
    $this->assertNotEmpty($web_assert->waitForElement('css', '.tox-tbtn[title="Insert/edit image"]'));
    $this->click('.tox-tbtn[title="Insert/edit image"]');
    $this->assertNotEmpty($web_assert->waitForElement('css', '.tox-dialog'));

    $web_assert->elementContains('css', '.tox-dialog .tox-dialog__title', 'Insert/Edit Image');
  }

}
