<?php

declare(strict_types=1);

namespace Drupal\Tests\multiselect_dropdown\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests hooks implemented by Multiselect Dropdown.
 *
 * @group multiselect_dropdown
 */
final class MultiselectDropdownHooksTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'multiselect_dropdown',
    'multiselect_dropdown_bef',
  ];

  /**
   * Test the multiselect dropdown theme suggestions alter hooks.
   *
   * @covers ::multiselect_dropdown_theme_suggestions_alter
   * @covers ::multiselect_dropdown_bef_theme_suggestions_alter
   */
  public function testMultiselectDropdownThemeSuggestions(): void {
    $suggestions = [];

    multiselect_dropdown_theme_suggestions_alter($suggestions, [], 'multiselect_dropdown');
    self::assertSame([], $suggestions);

    $variables['element']['#name'] = 'name';
    multiselect_dropdown_theme_suggestions_alter($suggestions, $variables, 'multiselect_dropdown');
    self::assertSame(['multiselect_dropdown__name'], $suggestions);

    multiselect_dropdown_bef_theme_suggestions_alter($suggestions, $variables, 'multiselect_dropdown');
    self::assertSame(['multiselect_dropdown__name'], $suggestions);

    $variables['element']['#context'] = [
      '#view_id' => 'view',
      '#display_id' => 'display',
    ];
    multiselect_dropdown_bef_theme_suggestions_alter($suggestions, $variables, 'multiselect_dropdown');
    self::assertSame(
      [
        'multiselect_dropdown__name',
        'multiselect_dropdown__view',
        'multiselect_dropdown__view__name',
        'multiselect_dropdown__view__display',
        'multiselect_dropdown__view__display__name',
      ],
      $suggestions,
    );
  }

}
