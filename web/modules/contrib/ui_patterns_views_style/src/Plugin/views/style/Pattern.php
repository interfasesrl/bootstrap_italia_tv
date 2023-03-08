<?php

declare(strict_types = 1);

namespace Drupal\ui_patterns_views_style\Plugin\views\style;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ui_patterns\Element\PatternContext;
use Drupal\ui_patterns\Form\PatternDisplayFormTrait;
use Drupal\ui_patterns\UiPatternsManager;
use Drupal\ui_patterns\UiPatternsSourceManager;
use Drupal\ui_patterns_settings\UiPatternsSettings;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Style plugin to render items in a pattern field.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *     id = "pattern",
 *     title = @Translation("Pattern"),
 *     help = @Translation("Displays content with a pattern."),
 *     theme = "view--pattern",
 *     display_types = {"normal"}
 * )
 */
class Pattern extends StylePluginBase {
  use PatternDisplayFormTrait;

  /**
   * UI Patterns manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsManager
   */
  protected $patternsManager;

  /**
   * UI Patterns source manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsSourceManager
   */
  protected $sourceManager;

  /**
   * A module manager object.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ui_patterns\UiPatternsManager $patterns_manager
   *   UI Patterns manager.
   * @param \Drupal\ui_patterns\UiPatternsSourceManager $source_manager
   *   UI Patterns source manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   A module manager object.
   */
  final public function __construct(array $configuration, $plugin_id, $plugin_definition, UiPatternsManager $patterns_manager, UiPatternsSourceManager $source_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->patternsManager = $patterns_manager;
    $this->sourceManager = $source_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.ui_patterns'),
      $container->get('plugin.manager.ui_patterns_source'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['pattern'] = ['default' => ''];
    $options['pattern_variant'] = ['default' => ''];
    $options['pattern_mapping'] = ['default' => []];
    $options['pattern_settings'] = ['default' => []];
    return $options;
  }

  /**
   * {@inheritdoc}
   *
   * @phpstan-ignore-next-line
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): array {
    parent::buildOptionsForm($form, $form_state);

    $context = [];
    $this->buildPatternDisplayForm($form, 'view_style', $context, $this->options);
    $form['#element_validate'] = [[static::class, 'cleanSettings']];
    return $form;
  }

  /**
   * Clean up pattern settings.
   *
   * @param array $element
   *   The pattern settings element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array $form
   *   The form definition.
   */
  public static function cleanSettings(array &$element, FormStateInterface $form_state, array $form): void {
    $element_value = $form_state->getValue($element['#parents']);
    static::processFormStateValues($element_value);
    $form_state->setValue($element['#parents'], $element_value);
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    $pattern = $this->options['pattern'];

    // We are using groups ($usesRowPlugin)
    foreach ($build as $delta => $group) {
      $build[$delta]['#options']['pattern'] = $pattern;
      $build[$delta]['#options']['variant'] = $this->options['pattern_variant'];
      $build[$delta]['#options']['context'] = new PatternContext('views_style', [
        'view_name' => $this->view->id(),
        'display' => $this->view->current_display,
        'view' => $this->view->storage,
      ]);

      // Set pattern fields.
      $fields = [];
      $mapping = $this->options['pattern_mapping'];
      foreach ($mapping as $source => $field) {
        if ($field['destination'] === '_hidden') {
          continue;
        }
        // Get rid of the source tag.
        $source = \explode(':', $source)[1];
        if ($source === 'title') {
          $fields[$field['destination']] = $group['#title'];
        }
        if ($source === 'rows') {
          $fields[$field['destination']] = $group['#rows'];
        }
      }
      $build[$delta]['#options']['fields'] = $fields;
      if (isset($this->options['pattern_settings'][$pattern]) && $this->moduleHandler->moduleExists('ui_patterns_settings')) {
        $build[$delta]['#options']['settings'] = UiPatternsSettings::preprocess($pattern, $this->options['pattern_settings'][$pattern], $build[$delta]['#options']['variant'], FALSE);
      }
    }
    return $build;
  }

}
