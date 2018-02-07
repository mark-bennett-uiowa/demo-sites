<?php

namespace Drupal\uiowa_tracker\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\Core\Routing\RouteMatch;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@inheritdoc}
 */
class MymoduleSubscriber implements EventSubscriberInterface {

  protected $routeMatch;
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public function __construct(RouteMatch $routeMatch, Connection $connection) {
    $this->routeMatch = $routeMatch;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['trackNodes'];
    return $events;
  }

  /**
   * Tracks the request if it belongs to a node.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The response event for this request.
   */
  public function trackNodes(GetResponseEvent $event) {

    /* @var NodeInterface $node */
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      return;
    }
    // UID = 0 means anonymous.
    $uid = \Drupal::currentUser()->id();
    $nid = $node->id();

    // @TODO Is this really the name of the content type?
    $nodeIsType = TRUE || $node->bundle() == 'content-type';
    $nodeIsTracked = uiowa_tracker_check_node($node);

    if ($nodeIsType && $uid && $nodeIsTracked) {
      $this->uiowaTrackerInsertNodeView($nid, $uid);
    }

  }

  /**
   * Insert node view into uiowa_tracker_log table.
   *
   * @param int $nid
   *   The viewed node.
   * @param int $uid
   *   The user who viewed the node.
   *
   * @return bool
   *   TRUE if node was added to the uiowa_tracker_log table, otherwise FALSE.
   */
  protected function uiowaTrackerInsertNodeView($nid, $uid) {
    if (is_numeric($nid) && is_numeric($uid) && $uid) {
      /* @var Node $node */
      $node = Node::load($nid);

      /* @var User $user */
      $user = User::load($uid);
    }
    else {
      return FALSE;
    }

    $path = $node->toUrl()->toString();

    $rolelist = "";
    foreach ($user->getRoles() as $role) {
      if ($role != "authenticated") {
        $rolelist .= $role . ", ";
      }
    }
    $rolelist = substr($rolelist, 0, -2);

    $this->connection->insert('uiowa_tracker_log')
      ->fields([
        'nid' => $nid,
        'path' => $path,
        'pagetitle' => $node->getTitle(),
        'uid' => $uid,
        'username' => $user->getAccountName(),
        'rolename' => $rolelist,
        'timestamp' => REQUEST_TIME,
      ])->execute();
  }

}
