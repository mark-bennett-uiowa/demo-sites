<?php

namespace Drupal\demo_external_posts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\demo_external_posts\WSPosts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block showing an external post.
 *
 * @Block(
 *   id = "external_post_block",
 *   admin_label = @Translation("An external post.")
 * )
 */
class ExternalPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $wsPosts;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('demo_external_posts.ws_posts'));
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, WSPosts $wsPosts) {
    $this->wsPosts = $wsPosts;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    return $this->wsPosts->renderPost(2);

  }

}
