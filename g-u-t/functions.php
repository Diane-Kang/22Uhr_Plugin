<?php 
////////////// Only for G.U.T Gruppe shortcodes & functions 
///////Class for Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen/g-u-t
require_once  PE_22Uhr_Plugin_Path . 'g-u-t/geojsonAPI_generate_fg_gut.php';
$data = array(
  "firmengruppe" => "G.U.T.",
  "firmengruppe_slug" => "g-u-t"
);
new geojsonAPI_generate_fg_gut($data);


require_once  PE_22Uhr_Plugin_Path . 'g-u-t/template-parts/firma-counting.php';
require_once  PE_22Uhr_Plugin_Path . 'g-u-t/template-parts/gut_gruppe_list.php';
require_once  PE_22Uhr_Plugin_Path . 'g-u-t/template-parts/liste_fg_gut.php';
require_once  PE_22Uhr_Plugin_Path . 'g-u-t/template-parts/gut_gruppe_child_template.php';

/// GUT Detailseite

function gut_main_addtional_style_js()
{
  //Since this file is not 22uhr-plugin/ instead, 22uhr-plugin/ 
  $plugin_dir_url = plugin_dir_url(dirname(__FILE__));
  // g-u-t main page only 
  if (is_single('g-u-t')) {
    wp_enqueue_style('gut_detail',                     $plugin_dir_url . 'g-u-t/css/detailseite_gut.css', array(), '1.9', false);
    wp_enqueue_style('gut_detail_main',                $plugin_dir_url . 'g-u-t/css/detailseite-gut-main.css', array(), '1.0', false);
    wp_enqueue_script('alle_anzeigen_button_js',       $plugin_dir_url . 'g-u-t/js/gut_main_detailseite_button.js', array('jquery'), false, false);
  }
  // first call the $post variable 
  global $post;
  // check the post hast a parent &&(AND) the paerent is the post with slug('g-u-t')
  if (($post->post_parent != 0) && ('g-u-t' == basename(get_permalink($post->post_parent)))) {
    wp_enqueue_style('gut_detail',                     $plugin_dir_url . 'g-u-t/css/detailseite_gut.css', array(), '1.9', false);
  }
}
add_action('wp_enqueue_scripts', 'gut_main_addtional_style_js', 10, 1);