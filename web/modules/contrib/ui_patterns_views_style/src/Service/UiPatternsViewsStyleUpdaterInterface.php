<?php

declare(strict_types = 1);

namespace Drupal\ui_patterns_views_style\Service;

/**
 * UI Patterns Views Style updater interface methods.
 */
interface UiPatternsViewsStyleUpdaterInterface {

  /**
   * Implementation of ui_patterns_views_style_update_9101().
   */
  public function update9101(): void;

  /**
   * Prepare new settings structure.
   *
   * @param array $styleSettings
   *   The style settings with the old structure.
   *
   * @return array
   *   The style settings with the new structure.
   */
  public static function update9101PrepareNewSettings(array $styleSettings): array;

}
