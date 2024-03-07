<?php

namespace Drupal\subscription_updates\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Url;
use Drupal\commerce\commerce_product;
use Drupal\commerce;
use Drupal\commerce_cart;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
 
/**
* Controller routines for products routes.
*/
class CartsController extends ControllerBase {
 
/**
* The cart manager.
*
* @var \Drupal\commerce_cart\CartManagerInterface
*/
protected $cartManager;
 
/**
* The cart provider.
*
* @var \Drupal\commerce_cart\CartProviderInterface
*/
protected $cartProvider;
 
/**
* Constructs a new CartController object.
*
* @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
*   The cart provider.
*/
public function __construct(CartManagerInterface $cart_manager,CartProviderInterface $cart_provider) {
 $this->cartManager = $cart_manager;
 $this->cartProvider = $cart_provider;
}
 
/**
* {@inheritdoc}
*/
public static function create(ContainerInterface $container) {
 return new static(
   $container->get('commerce_cart.cart_manager'),
   $container->get('commerce_cart.cart_provider')
 );
}
 
 public function addToCart() {
  dd('test');
   $product_id = 4;  
   $destination = \Drupal::service('path.current')->getPath();
   $productObj = \Drupal\commerce_product\Entity\Product::load($product_id);
 
   $product_variation_id = $productObj->get('variations')
     ->getValue()[0]['target_id'];
   $storeId = $productObj->get('stores')->getValue()[0]['target_id'];
   $variationobj = \Drupal::entityTypeManager()
     ->getStorage('commerce_product_variation')
     ->load($product_variation_id);
   $store = \Drupal::entityTypeManager()
     ->getStorage('commerce_store')
     ->load($storeId);
 
  $cart = $this->cartProvider->getCart('default', $store);
 
   if (!$cart) {
    $cart = $this->cartProvider->createCart('default', $store);
 
   }
 
   $line_item_type_storage = \Drupal::entityTypeManager()
     ->getStorage('commerce_order_item_type');
// Process to place order programatically.
   $cart_manager = \Drupal::service('commerce_cart.cart_manager');
   $line_item = $cart_manager->addEntity($cart, $variationobj);

  // $response = new RedirectResponse(Url::fromRoute('commerce_cart.page')->toString());
  $response = new RedirectResponse('/welcome-my-choir-central-1');
  
  return $response;
  
 }
 
}