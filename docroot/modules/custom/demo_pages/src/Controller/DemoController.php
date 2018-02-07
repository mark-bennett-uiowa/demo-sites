<?php

namespace Drupal\demo_pages\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\demo_pages\WSPosts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for page example routes.
 */
class DemoController extends ControllerBase {

  protected $wsPosts;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('demo_pages_posts.ws_posts')
    );
  }

  public function __construct(WSPosts $wsPosts) {
    $this->wsPosts = $wsPosts;
  }

  public function post($post_id) {
    $posts = $this->wsPosts->posts();

    $posts_titles = [];
    foreach ($posts as $post) {

      if (!is_object($post)) {
        continue;
      }

      $posts_titles[] = Link::createFromRoute($post->title, 'demo_external_posts_ws_post', ['post_id' => $post->id]);
    }

    $posts_per_page = 10;
    $current_page = pager_default_initialize(count($posts_titles), $posts_per_page);
    $pages = array_chunk($posts_titles, $posts_per_page, TRUE);

    $elements['list'] = [
      '#theme' => 'item_list',
      '#items' => $pages[$current_page],
    ];

    $elements['pager'] = [
      '#type' => 'pager',
    ];

    return $elements;
  }

  public function posts() {
    return $this->wsPosts->posts();
  }

}
