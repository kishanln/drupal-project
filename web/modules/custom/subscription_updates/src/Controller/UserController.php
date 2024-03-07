<?php

namespace Drupal\subscription_updates\Controller;
use Drupal\Core\Controller\ControllerBase;
class UserController extends ControllerBase {

  /**
  * Returns the page content.
  */
  public function content() {
//get latitude and longitude 

// We define our address



$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

$list = \Drupal::entityTypeManager()
  ->getStorage('profile')
  ->loadByProperties([
    'uid' => \Drupal::currentUser()->id(),
    'type' => 'customer',
  ]);
foreach($list as $value){
$address_val=$value->get('address')->getValue()[0];
// $locality=$address_val->get('locality')->getValue();
// $postal_code=$address_val->get('postal_code')->getValue();

}
$address = 'Dehradun, UK 248001';

$point = geocoder('google', $address);
$lat = $point->coords[1];
$lon = $point->coords[0];
dd($lat);
// function to get  the address
function get_lat_long($address) {
  
   $array = array();
   $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');

   // We convert the JSON to an array
   $geo = json_decode($geo, true);

   // If everything is cool
   if ($geo['status'] = 'OK') {
      $latitude = $geo['results'][0]['geometry']['location']['lat'];
      $longitude = $geo['results'][0]['geometry']['location']['lng'];
      $array = array('lat'=> $latitude ,'lng'=>$longitude);
   }

   return $array;
}

  }
  
  }