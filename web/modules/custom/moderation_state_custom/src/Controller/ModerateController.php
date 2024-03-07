<?php
namespace Drupal\moderation_state_custom\Controller;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for the Example module.
 */
class ModerateController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function moderateState($nid = null) {
    $node = Node::load($nid);
    $type = $node->bundle();
    $moderation_state = $node->moderation_state->value;
   
    // $draft= $node->set('moderation_state','draft');
    //  echo "<pre>";
    // print_r($draft);
    // exit;
    if($moderation_state == 'draft'){
        $node->set('moderation_state','review');
        $node->save();
    }
    if($type == 'choir_or_group_listing'){
     $redirect_url ='/choir-detail-view/'.$nid;
    }elseif($type == 'teacher'){
      $redirect_url ='/teacher-detail-view/'.$nid;
    }elseif($type == 'tours'){
    $redirect_url ='/tour-detail-view/title/'.$nid;
    }elseif($type == 'workshops'){
    $redirect_url ='/detail-view/title/'.$nid;
    }elseif($type == 'performance_listing'){
    $redirect_url ='/event-detail-view/title/'.$nid;
    }
    elseif($type == 'auditions_and_open_nights'){
      $redirect_url ='/auditions-and-open-nights-detail-view/title/'.$nid;
      }
    $response =  new RedirectResponse($redirect_url);
     return $response->send();
    return [
      '#markup' => 'Hello, world',
    ];
  }

}