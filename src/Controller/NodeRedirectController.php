<?php

namespace Drupal\ssi_events\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Controller\NodeViewController;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Custom node redirect controller
 * Based on: https://www.drupal.org/docs/8/api/routing-system/altering-existing-routes-and-adding-new-routes-based-on-dynamic-ones
 * And: https://drupal.stackexchange.com/questions/215141/take-over-display-of-a-content-type-node-route/215149#215149
*/
class NodeRedirectController extends NodeViewController {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $node, $view_mode = 'full', $langcode = NULL) {
    /** @var \Drupal\node\NodeInterface $node */
    // If this is an event and the 'link directly to url' field is checked,
    // then redirect to URL
    if ($this->currentUser->isAnonymous() && $node->getType() === 'event' && $node->field_link_directly_to_event_url->value && !$node->get('field_url')->isEmpty()) {
      return (new TrustedRedirectResponse($node->field_url->uri, 302))
        ->addCacheableDependency($node);
    }

    // Otherwise just go to the full node
    return parent::view($node, $view_mode, $langcode);
  }

}
