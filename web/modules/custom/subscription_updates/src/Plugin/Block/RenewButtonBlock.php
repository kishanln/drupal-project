<?php
namespace Drupal\subscription_updates\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a 'Renew Button' block.
 *
 * @Block(
 *   id = "renew_button_block",
 *   admin_label = @Translation("Renew Button"),
 *   category = @Translation("Renew")
 * )
 */

class RenewButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
   
  public function build() {

    $current_user_roles = \Drupal::currentUser()->getRoles();
    $query = \Drupal::database()->select('commerce_subscription','cs'); 
    $query ->leftjoin('commerce_payment','cp','cp.order_id = cs.initial_order');
    $query->fields('cp',['payment_gateway']);
    $query->fields('cp',['remote_state']);
    $query->fields('cs',['state']);
    $query->condition('cs.type','product_variation','=');	
    // $query->condition('cp.payment_gateway','paypal_test','=');
    $query->condition('cs.uid',\Drupal::currentUser()->id(),'=');	
    $results = $query->execute()->fetchAll();
    $states=[];
    foreach($results as $value){
      $states[]=$value->state;
    }
    if(!in_array('active',$states) && !empty($states)){
      return [
        '#type' => 'markup',
        '#markup' => '<div class="form-actions js-form-wrapper form-wrapper" id="renew">
                <a href="/add/susbcription-product" class="button js-form-submit form-submit">Renew My Membership</a>
            </div>'
      ];
    }else{
      return [
        '#type' => 'markup',
        '#markup' => ' '
      ];
    }

        
    }
  }
