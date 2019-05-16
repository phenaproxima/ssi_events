<?php

namespace Drupal\ssi_events\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Controller\NodeViewController;
use Drupal\Core\Routing\TrustedRedirectResponse;


/**
 * Custom node redirect controller
 * Based on: https://www.drupal.org/docs/8/api/routing-system/altering-existing-routes-and-adding-new-routes-based-on-dynamic-ones
 * And: https://drupal.stackexchange.com/questions/215141/take-over-display-of-a-content-type-node-route/215149#215149
*/
class NodeRedirectController extends NodeViewController {

  public function view(EntityInterface $node, $view_mode = 'full', $langcode = NULL) {

    $redirect = FALSE;
    
    if (\Drupal::currentUser()->isAnonymous()) {
      // If this is an event and the 'link directly to url' field is checked,
      // then redirect to URL
      
      if ($node->getType() == 'event') {
        $checkbox = $node->get('field_link_directly_to_event_url')->getValue();
        $url = $node->get('field_url')->getValue();
  
        if ($checkbox['0']['value'] == 1 && !empty($url)) {
            $url = $url['0']['uri'];
            $redirect = TRUE;
        }
      }
    }
    
    if ($redirect == TRUE) {
      $response = new TrustedRedirectResponse($url, 302);
      $response->addCacheableDependency($node);
    }
    // Otherwise just go to the full node
    else {
      $response = parent::view($node, $view_mode, $langcode);
    }
    return $response;
  }
}
