<?php
namespace Drupal\moderation_state_custom\Controller;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the Example module.
 */
class DraftController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function moderateStateDraft($nid = null) {
    $node = Node::load($nid);
    $type = $node->bundle();
    $moderation_state = $node->moderation_state->value;
   
    if($moderation_state == 'review'){
        $node->set('moderation_state','draft');
        $node->save();
    }
    if($type == 'choir_or_group_listing'){
      $redirect_url ='/user/user-content';
    }elseif($type == 'teacher'){
       $redirect_url ='/user/user-content';
    }elseif($type == 'tours'){
     $redirect_url ='/user/user-content';
    }elseif($type == 'workshops'){
     $redirect_url ='/user/user-content';
    }elseif($type == 'performance_listing'){
     $redirect_url ='/user/user-content';
    }elseif($type == 'auditions_and_open_nights'){
      $redirect_url ='/user/user-content';
     }
    
   
    $response =  new RedirectResponse($redirect_url);
     return $response->send();
    return [
      '#markup' => 'Hello, world',
    ];
  }

}