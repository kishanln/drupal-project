<?php

/**
* @file
* Contains \Drupal\subscription_updates\OrderCompleteSubscriber\.
*/

namespace Drupal\subscription_updates\EventSubscriber;

use Drupal\commerce_order\AvailabilityResult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\commerce_order\Entity\Order;
use \Drupal\user\Entity\User;
use Drupal\Core\Database\Database;
use \Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\commerce_recurring\RecurringOrderManagerInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;



    /**
    * Class OrderCompleteSubscriber.
    *
    * @package Drupal\mymodule
    */

class OrderCompleteSubscriber implements EventSubscriberInterface {


	static function getSubscribedEvents() {
    $events['commerce_order.place.post_transition'] = ['orderCompleteHandler'];
    $events['commerce_order.cancel.pre_transition'] = 'orderCancelHandler';
    // $events['commerce_order.place.pre_transition'] = 'onPlaceOrder';
    $events[CartEvents::CART_ENTITY_ADD] = ['updateCart'];
		return $events;
	}
  public function orderCompleteHandler(WorkflowTransitionEvent $event) {
//
  

    // // Restrict user to rebuy subscription/plan if s/he already have one.
    // $current_userid = \Drupal::currentUser()->id();
    // $subscriptions = \Drupal::database()->select('commerce_subscription', 'cs');
    // $subscriptions->leftJoin('commerce_order', 'co', 'co.order_id = cs.initial_order'); 
    // $subscriptions->fields('cs', ['subscription_id']);
    // $subscriptions->condition('cs.uid', $current_userid);
    // $subscription_product = $subscriptions->execute()->fetchAll(); 
    // \Drupal::logger('subscription_id')->notice('<pre>'.print_r($subscription_product[0]->subscription_id,true).'</pre>');
  
    // $entity_manager = \Drupal::entityManager();
    // $subscription = $entity_manager->getStorage('commerce_subscription')->load((int)$subscription_product[0]->subscription_id);
 
    // $start_date = $subscription->getStartDate();

    // $ends_date = $subscription->getEndDate();
    
    // $billing_schedule = $subscription->getBillingSchedule();
    // $billing_period = $billing_schedule->getPlugin()->generateFirstBillingPeriod($start_date);
    // $end_date = $billing_period->getEndDate()->getTimestamp();

    // $subscription->setEndTime($end_date);
    // \Drupal::logger('ends_date')->notice('<pre>'.print_r($end_date,true).'</pre>');
    
    // // $subscription->setNextRenewalTime($billing_period->getEndDate()->getTimestamp());
    

//
    $current_user = \Drupal::currentUser();
    
    $order = $event->getEntity();
    $order_id = $order->id();
    $items = $order->getItems();
    $payment_method = $order->get('payment_gateway')->first()->entity->label();
    if ($order->bundle() == 'recurring' && $order->getState()->getId() == 'needs_payment' && $payment_method =='stripe') {
     
      $user = User::load($order->getCustomerId());
      $roles = $user->getRoles();
      if(!in_array("paid_member", $roles)) {
        $user->addRole('paid_member');
        $user->save();
        return;
      }
      return;
    }
    if ($order->bundle() == 'recurring' && $order->getState()->getId() == 'needs_payment' && $payment_method =='paypal') {
        $user = User::load($order->getCustomerId());
        $roles = $user->getRoles();
        if(in_array("paid_member", $roles)) {
          $user->removeRole('paid_member');
          $user->save();
          return;
        }
       return;
    }

    if($order->getState()->getId() == 'completed') {
      $count = 0; 
      foreach($items as $item){
        $purchasedEntity = $item->getPurchasedEntity();

        $product = $purchasedEntity->getProduct();
        $product_id = $product->id();
        $product_variation_type = $purchasedEntity->bundle();

        $node_id = $item->get('field_content_id')->getValue();
          if(isset($product_variation_type) && $product_variation_type == 'default' ) {
            //load node
            $node = \Drupal\node\Entity\Node::load($node_id[0]['target_id']);
            $no_of_days = $purchasedEntity->field_number_of_days->value;
            //get product variation days // prmotion days
            if (empty($no_of_days)) {
              $no_of_days = '30';
              $node->set('field_number_of_days', $no_of_days); 
            } 
            if (isset($node)) {
          
            //order->created date //start date
              //End date empty

            $moderation_state = $node->moderation_state->value;

             if ($moderation_state == 'published') {
              $promotional_end_date = $node->get('field_promotion_end_date')->getValue();  
              $promotional_start_date =  $node->get('field_promotion_start_date')->getValue();  
              if (empty($promotional_end_date)) {
                $promotional_end_date = date('Y-m-d', strtotime(" + {$no_of_days} days"));
                $node->set('field_promotion_end_date', $promotional_end_date); 
              }elseif($promotional_start_date > $promotional_end_date){
                $node->set('field_promotional_days', $no_of_days);
                $node->set('field_promotion_start_date', date('Y-m-d', time()));
                $node->set('field_promotion_end_date',' '); 
              }else{   
                $node->set('field_promotional_days', $no_of_days);
                $node->set('field_promotion_start_date', date('Y-m-d', time()));
                $node->set('field_promotion_end_date',' '); 
              }
             }

            if ($moderation_state != 'published') {
               $node->set('field_promotional_days', $no_of_days);
               $node->set('field_promotion_start_date', date('Y-m-d', time()));
               $node->set('moderation_state', "review");
            }
            $node->save();
           } 

          } 
          if (isset($product_variation_type) && $product_variation_type == 'recurring_product' ) {
            $count++;
          } 
     
        $subscriptions = \Drupal::database()->select('commerce_subscription', 'cs');
        $subscriptions->fields('cs', ['subscription_id']);
        $subscriptions->condition('cs.uid',$order->getCustomerId(),'=');
        $subscriptions->condition('cs.initial_order', $order->id(),'=');
        $subscription_product = $subscriptions->execute()->fetchAssoc(); 


        if(!empty($subscription_product)){

            $entity_manager = \Drupal::entityTypeManager();
            $subscription = $entity_manager->getStorage('commerce_subscription')->load((int) $subscription_product['subscription_id']);
            // $billing_schedule_item = $subscription->getBillingSchedule();
            $billing_schedule_item = $purchasedEntity->get('billing_schedule')->referencedEntities()[0];
            // $billing_schedule = $billing_schedule_item->entity;

            if(strtolower($billing_schedule_item->getDisplayLabel()) == 'yearly'){
              $subscription->setEndTime(strtotime('+1 year'));
            }
            if(strtolower($billing_schedule_item->getDisplayLabel()) ==  'monthly'){
              $subscription->setEndTime(strtotime('+1 month'));
            }
            $subscription->save();
          }
        }

       
        $customer = $order->getCustomer();
        $user_id = $customer->id();
        $user = User::load($user_id);

        if (isset($count) && $count >= 1 ) {
          $user->addRole('paid_member');
        }
        $user->save();


      
    } elseif ($order->getState()->getId() == 'canceled') {
        $user = User::load($order->getCustomerId());
        $roles = $user->getRoles();
        if(in_array("paid_member", $roles)) {
          $user->removeRole('paid_member');
        }
        $user->save();
    } elseif ($order->getState()->getId() == 'needs_payment') {
        $user = User::load($order->getCustomerId());
        $roles = $user->getRoles();
        if(in_array("paid_member", $roles)) {
          $user->removeRole('paid_member');
        }
        $user->save();
    } elseif ($order->getState()->getId() == 'failed'){
        $user = User::load($order->getCustomerId());
        $roles = $user->getRoles();
        if(in_array("paid_member", $roles)) {
          $user->removeRole('paid_member');
        }
        $user->save();
    }    
	}


  public function orderCancelHandler(WorkflowTransitionEvent $event) {

    $order = $event->getEntity();
    $items = $order->getItems();

    $user = User::load($order->getCustomerId());
    $roles = $user->getRoles();

    if(in_array("paid_member", $roles)){
      $user->removeRole('paid_member');
      $user->save();
    }
    foreach($items as $item){
      $node_id = $item->get('field_content_id')->getValue();
      $node = \Drupal\node\Entity\Node::load($node_id);
      $node->setPublished(FALSE);
      $node->save();
    } 
  }

  public function updateCart(CartEntityAddEvent $event) {

    $current_path = \Drupal::service('path.current')->getPath();
    $pathArgument = explode('/', $current_path);

    $nodeId = '';
    if (strpos($current_path, 'choir-detail-view') !== false) {
      $nodeId = $pathArgument['2'];
    }elseif(strpos($current_path, 'event-detail-view') !== false){
       $nodeId = $pathArgument['3'];
    }elseif(strpos($current_path, 'teacher-detail-view') !== false){
       $nodeId = $pathArgument['2'];
    }elseif(strpos($current_path, 'tour-detail-view') !== false){
       $nodeId = $pathArgument['3'];
    }elseif(strpos($current_path, 'detail-view') !== false){
       $nodeId = $pathArgument['3'];
    }
    if (isset($nodeId)) {
      // You can get nid and anything else you need from the node object.
      $nid = $nodeId;
      // Get purchase entity
      $purchasedEntity = $event->getOrderItem()->getPurchasedEntity();
      // Get product variation type
      $product_variation_type = $purchasedEntity->bundle();
  
      if (isset($product_variation_type) && $product_variation_type == 'default') {
        $product_id = $event->getOrderItem()->getPurchasedEntity()->product_id->getString();
        $cart = $event->getCart();
        $added_order_item = $event->getOrderItem();
        $cart_items = $cart->getItems();
        foreach ($cart_items as $key => $item) {
          $count_element = count($cart_items) - 1;
          $node_id = $item->get('field_content_id')->getValue();
          // if(!empty($node_id) && $_GET['nid'] == $node_id[0]['target_id']) {
          if(!empty($node_id) && $nid == $node_id[0]['target_id']) {
            if ($item->id() != $added_order_item->id()) {
              $cart->removeItem($item);
              $item->delete();
            }
          }else{
            if($count_element == $key) {
              if(isset($nid) && !empty($nid)) {
                $nid = $nid;
                $item->set('field_content_id', $nid);
                $item->save();
              }
            }
          } 
        }
      }
      //restrict product
      else{
        // Restrict user to rebuy subscription/plan if s/he already have one.
          $current_userid = \Drupal::currentUser()->id();
          $subscriptions = \Drupal::database()->select('commerce_subscription', 'cs');
          $subscriptions->leftJoin('commerce_product_variation_field_data', 'cpv', 'cpv.variation_id = cs.purchased_entity'); 
          $subscriptions->fields('cs', ['state']);
          $subscriptions->condition('cs.uid', $current_userid);
          $subscriptions->condition('cs.state', 'active');
          $subscriptions->condition('cpv.type', 'recurring_product');
          $subscription_product = $subscriptions->execute()->fetchcol(); 
     
          if (!empty($subscription_product)) {
            \Drupal::messenger()->deleteAll();
            \Drupal::messenger()->addError(t('You have already active Choir Central Membership'));
            $order_item = $event->getOrderItem();
            $orders = \Drupal::service('commerce_cart.cart_provider')->getCarts();
            foreach ($orders as $order){
            $cart_manager = \Drupal::service('commerce_cart.cart_manager');
            $cart_manager->removeOrderItem($order, $order_item);
          }
          $user = \Drupal::currentUser()->id();
          \Drupal::messenger()->addMessage(t('You can manage your subscriptions.<a href="/user/'.$user.'/subscriptions">here</a>'));
          $response = new RedirectResponse('/sign-up');
          $response->send();
          return;
          }
        }
      //restrict product
    }
    else
    {
      $purchasedEntity = $event->getOrderItem()->getPurchasedEntity();

      // Get product variation type
      $product_variation_type = $purchasedEntity->bundle();
      // Check user role for already subscription.
     
      if (isset($product_variation_type) && $product_variation_type == 'recurring_product') {
   
        // Restrict user to rebuy subscription/plan if s/he already have one.
        $current_userid = \Drupal::currentUser()->id();
        $subscriptions = \Drupal::database()->select('commerce_subscription', 'cs');
        $subscriptions->leftJoin('commerce_product_variation_field_data', 'cpv', 'cpv.variation_id = cs.purchased_entity'); 
        $subscriptions->fields('cs', ['state']);
        $subscriptions->condition('cs.uid', $current_userid);
        $subscriptions->condition('cs.state', 'active');
        $subscriptions->condition('cpv.type', 'recurring_product');
        $subscription_product = $subscriptions->execute()->fetchcol(); 
        if (!empty($subscription_product)) {
          $order_item = $event->getOrderItem();
          $orders = \Drupal::service('commerce_cart.cart_provider')->getCarts();
          foreach ($orders as $order){
          $cart_manager = \Drupal::service('commerce_cart.cart_manager');
          $cart_manager->removeOrderItem($order, $order_item);
          }
          $user = \Drupal::currentUser()->id();
          \Drupal::messenger()->addMessage(t('You can manage your subscriptions.<a href="/user/'.$user.'/subscriptions">here</a>'));
       
        }
      }
    }
  }  

}

?>