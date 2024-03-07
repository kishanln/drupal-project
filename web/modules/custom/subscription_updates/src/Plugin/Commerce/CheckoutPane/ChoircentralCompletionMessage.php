<?php

namespace Drupal\subscription_updates\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;

/**
 * Provides the coupons pane.
 *
 * @CommerceCheckoutPane(
 *   id = "choircentral_completion_message",
 *   label = @Translation("Choircentral Completion Message"),
 *   admin_label = @Translation("Choircentral Completion Message"),
 * )
 */
class ChoircentralCompletionMessage extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow = NULL) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $checkout_flow,
      $container->get('entity_type.manager')
    );
    $instance->setToken($container->get('token'));
    return $instance;
  }

  /**
   * Sets the token service.
   *
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   */
  public function setToken(Token $token) {
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  // public function defaultConfiguration() {
  //   return [
  //     'message' => [
  //       'value' => "Your order number is [commerce_order:order_number].\r\nYou can view your order on your account page when logged in.",
  //       'format' => 'plain_text',
  //     ],
  //   ] + parent::defaultConfiguration();
  // }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Message'),
      '#description' => $this->t('Shown the end of checkout, after the customer has placed their order.'),
      '#default_value' => $this->configuration['message']['value'],
      '#format' => $this->configuration['message']['format'],
      '#element_validate' => ['token_element_validate'],
      '#token_types' => ['commerce_order'],
      '#required' => TRUE,
    ];
    $form['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['commerce_order'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['message'] = $values['message'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $user = \Drupal::currentUser()->id();
    $JoinThankDisplay = False;
    $DefaultThankDisplay = False;
    $no_of_days = 0;
		$orderPagePath = \Drupal::request()->getpathInfo();
		$argPath  = explode('/',$orderPagePath);
		if(strpos($orderPagePath, 'checkout') == true  && strpos($orderPagePath, 'complete') == true){
			$order_id = $argPath[2];
			$orderObject = \Drupal\commerce_order\Entity\Order::load($order_id);
			$items = $orderObject->getItems();
      foreach ($items as $key => $value) {
        $purchased_entities =  $value->get('purchased_entity')->getValue();
        if(isset($purchased_entities) && !empty($purchased_entities)){
          foreach ($purchased_entities as $key => $entityPurchased) {
            if(isset($entityPurchased['target_id']) && !empty($entityPurchased['target_id'])){
              $variation = \Drupal\commerce_product\Entity\ProductVariation::load($entityPurchased['target_id']);	
              if($variation->bundle() == 'recurring_product'){
                $JoinThankDisplay = True;
              }else{
                if ($variation->bundle() == 'default') {
                   $node_id = $value->get('field_content_id')->getValue();
                    $node = \Drupal\node\Entity\Node::load($node_id[0]['target_id']);
                    $purchasedEntity = $value->getPurchasedEntity();
                    $no_of_days = $purchasedEntity->field_number_of_days->value;
                    $moderation_state = $node->moderation_state->value;
                  if ($moderation_state == 'published') {
                    $DefaultThankDisplay = True;
                  }
                
                }
              }
            }
          }
        }
      }			
		}
      if ($JoinThankDisplay) {
      $nid = 138;
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

      // $message  = '<li>Thank you for becoming a member of Choir Central.</li>'; 
      // $message .= '<ul>'; 
      // $message .= '<li>You can manage your listing from this link :<a href="/user/'.$user.'/listing">Manage Listing Link</a></li>'; 
      // $message .= '<li>Create a new listing from here : <a href="/node/add">Create Listing</a></li>'; 
      // $message .= '</ul>'; 
      $message  = '<h1 class="page_title">'.$node->title->value.'</h1>'; 
      $message  .= $node->body->value; 

      // $message ="<p>Subscription purchase successfully.<br />You can manage your listing from the  page or create a new listing by clicking <a href='/node/add'>here</a>.</p>";
    }else{
      if ($DefaultThankDisplay) {
         $message = '<h2>Thank you for choosing the option to promote your listing. You will get specified space on our premium listing section for a particular '.$no_of_days.' number of days at home page.</h2>'; 
      }else{
         $message = '<h2>Thank you for choosing to promote your listing. Your listing will regularly appear in rotation on the Home Page for '.$no_of_days.' days as soon as it is approved by our content reviewers.</h2>'; 
      }
    }
   
    $pane_form['message'] = [
      '#theme' => 'commerce_checkout_completion_message',
      '#order_entity' => $this->order,
      '#message' => [
        '#type' => 'processed_text',
        '#text' => $message,
        '#format' => 'full_html',

      ],
    ];

    return $pane_form;
  }

}
