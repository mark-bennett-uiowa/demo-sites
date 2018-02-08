<?php

namespace Drupal\demo_external_posts\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\demo_external_posts\WSPosts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'demo_external_posts_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "demo_external_posts_formatter",
 *   label = @Translation("External post"),
 *   field_types = {
 *     "demo_external_posts_field",
 *   }
 * )
 */
class DemoExternalPostsFormatter extends FormatterBase implements ContainerFactoryPluginInterface {


  protected $wsPosts;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      // External posts service.
      $container->get('demo_external_posts.ws_posts')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, WSPosts $wsPosts) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->wsPosts = $wsPosts;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $post_id = $item->get('post_id')->getValue();
      $elements[$delta] = $this->wsPosts->renderPost($post_id);
    }

    return $elements;
  }

}
