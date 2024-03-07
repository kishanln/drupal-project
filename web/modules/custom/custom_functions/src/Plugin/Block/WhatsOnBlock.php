<?php

namespace Drupal\custom_functions\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Plugin\views\access\Permission;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Views;

/**
 * Provides a 'WhatsOnBlock' block.
 *
 * @Block(
 *  id = "whats_on_block",
 *  admin_label = @Translation("Whats on block"),
 * )
 */
class WhatsOnBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['whats_on_block']['#markup'] = 'Implement WhatsOnBlock.';
    // $name = "whats_on";
    // $display_id = "block_1";
    // $view = Views::getView($name);
    //  if (is_string($display_id)) {
    //   $view->setDisplay($display_id);
    // }
    // $view->preExecute();
    // $view->execute();
    // $view = views_embed_view($name, $display_id);
    // $view1 = views_get_view_result($name, $display_id);
    // dsm($view1);
    // $nid = '';
    // $count = count($view1);
    // $i = 0;
    // foreach($view1 as $data){
    // 	dsm($data->nid);
    // 	$nid .= $data->nid;
    // 	$i++;
    // 	if($i < $count){

    // 		$nid.= '+';
    // 	}
    // }
    // dsm($nid);
    // $view2 = views_embed_view($name, 'attachment_2',$nid);
    // dsm($view2);
    // $output[] = $view;
    // $output[] = $view2;
    return $build;
  }

}
