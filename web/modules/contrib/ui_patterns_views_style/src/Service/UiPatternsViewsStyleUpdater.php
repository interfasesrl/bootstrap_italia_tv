<?php

declare(strict_types = 1);

namespace Drupal\ui_patterns_views_style\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Service to have better structured code for updates.
 */
class UiPatternsViewsStyleUpdater implements UiPatternsViewsStyleUpdaterInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory
  ) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function update9101(): void {
    // As there is no way to know which view uses pattern style.
    foreach ($this->configFactory->listAll('views.view.') as $view_config_name) {
      $view = $this->configFactory->getEditable($view_config_name);
      /** @var array $displays */
      $displays = $view->get('display');
      $changed = FALSE;
      foreach ($displays as $key => $display) {
        if (!isset($display['display_options']['style']['type'])
          || $display['display_options']['style']['type'] != 'pattern'
        ) {
          continue;
        }

        if (!isset($display['display_options']['style']['options']['pattern_variant'])) {
          $displays[$key]['display_options']['style']['options'] = $this->update9101PrepareNewSettings($display['display_options']['style']['options']);
          $changed = TRUE;
        }
      }
      if ($changed) {
        $view->set('display', $displays);
        $view->save(TRUE);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function update9101PrepareNewSettings(array $styleSettings): array {
    $pattern = $styleSettings['pattern'];

    // Update variant structure.
    $styleSettings['pattern_variant'] = $styleSettings['variants'][$pattern];
    unset($styleSettings['variants']);

    // Update mapping structure.
    $previous_mapping = $styleSettings['pattern_mapping'][$pattern]['settings'];
    unset($styleSettings['pattern_mapping']);

    $styleSettings['pattern_mapping'] = [];
    foreach ($previous_mapping as $source_field => $source_field_settings) {
      if ($source_field_settings['destination'] == '_hidden') {
        continue;
      }

      $styleSettings['pattern_mapping'][$source_field] = [
        'destination' => $source_field_settings['destination'],
        'weight' => $source_field_settings['weight'],
        'plugin' => 'view_style',
        'source' => 'rows',
      ];
    }

    return $styleSettings;
  }

}
