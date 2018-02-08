<?php

namespace Drupal\demo_external_posts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
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
  public function defaultConfiguration() {
    return [
      'demo_external_posts_config' => 1,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['demo_external_posts_post_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Post ID'),
      '#description' => $this->t('This will load an external post.'),
      '#default_value' => $this->configuration['demo_external_posts_config'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['demo_external_posts_config']
      = $form_state->getValue('demo_external_posts_post_id');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    return $this->wsPosts->renderPost($this->configuration['demo_external_posts_config']);

  }

}
