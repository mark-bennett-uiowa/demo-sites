<?php

namespace Drupal\demo_pages;

use GuzzleHttp\Client;

class WSPosts {

  protected $baseURL;
  protected $client;

  public function __construct(Client $client) {

    $this->client = $client;
    $this->baseURL = 'https://jsonplaceholder.typicode.com/';

  }

  public function posts(){
    $posts = json_decode($this->client->get($this->baseURL . '/posts')->getBody());

    return $posts;
  }
  
  public function post($postID) {
    return [
      '#markup' => '<h1>' . $postID . '</h1>',
    ];
  }
  
}