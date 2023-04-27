<?php

/*
  Plugin Name: 22Uhr Plugin 
  Plugin URI: https://github.com/Diane-Kang/22Uhr_Plugin
  Description: Customized plugin for 22Uhr.net. 
  Version: 2.0.0
  Author: Page-effect
  Author URI: Page-effect.com


  under plugin directory, dependencies need to be installed, 
  with specific Node.js and npm 

  ionicons/ionicons.js
  Nodejs : v14.19.2
  npm 8.12.1
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!defined('PE_22Uhr_Plugin_Path')) {
  define('PE_22Uhr_Plugin_Path', plugin_dir_path(__FILE__));
}



///////////// Setting Custom type Post, taxonomy  ///////////////////
require_once  PE_22Uhr_Plugin_Path . 'includes/ctp_unternehmen_init.php';
//// ??
require_once  PE_22Uhr_Plugin_Path . 'includes/shortcodes.php';


// VIEW

////////////// Unternehmen post 
require_once  PE_22Uhr_Plugin_Path . 'template-parts/single-unternehmen.php';

////////////// Show List of "Unternehmen"  and filter in frontend
//components to build list 
require_once  PE_22Uhr_Plugin_Path . 'template-parts/list-filter.php';
require_once  PE_22Uhr_Plugin_Path . 'template-parts/list-elements.php';
// all
require_once  PE_22Uhr_Plugin_Path . 'template-parts/list-unternehmen.php';
// Firmengruppe
require_once  PE_22Uhr_Plugin_Path . 'template-parts/list-firmengruppe.php';


////////////// Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen
require_once  PE_22Uhr_Plugin_Path . 'includes/geojsonAPI_generate_all.php';
////////////// Class for Register own Endpoint for API - /wp-json/22uhr-plugin/v1/{firmengruppe_slug}
require_once  PE_22Uhr_Plugin_Path . 'includes/geojsonAPI_generate_fg.php';
new geojsonAPI_generate_fg("hagebaumarkt-gruppe-muenchen");





////////////// Only for G.U.T Gruppe shortcodes & functions 
///////Class for Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen/g-u-t
require_once  PE_22Uhr_Plugin_Path . 'includes/geojsonAPI_generate_fg_gut.php';
$data = array(
  "firmengruppe" => "G.U.T.",
  "firmengruppe_slug" => "g-u-t"
);
new geojsonAPI_generate_fg_gut($data);

require_once  PE_22Uhr_Plugin_Path . 'template-parts/g-u-t/firma-counting.php';
require_once  PE_22Uhr_Plugin_Path . 'template-parts/g-u-t/gut_gruppe_list.php';
require_once  PE_22Uhr_Plugin_Path . 'includes/g-u-t/liste_fg_gut.php';
require_once  PE_22Uhr_Plugin_Path . 'includes/gut_gruppe_child_template.php';





///////////// Unternehmendetail Seite CSS  ///////////////////

// This applies for all the Unternehmendetailsite (CPT-unternehmen seite)
function unternehmen_css()
{
  if (is_singular('unternehmen')) {
    wp_enqueue_style('unternehmen_detail', plugin_dir_url(__FILE__) . 'css/single-unternehmen.css', array(), '1.9', false);
  }
}
add_action('wp_enqueue_scripts', 'unternehmen_css', 20, 1);


/// GUT Detailseite

function gut_main_addtional_style_js()
{
  // g-u-t main page only 
  if (is_single('g-u-t')) {
    wp_enqueue_style('gut_detail',                     plugin_dir_url(__FILE__) . 'css/g-u-t/detailseite_gut.css', array(), '1.9', false);
    wp_enqueue_style('gut_detail_main',                plugin_dir_url(__FILE__) . 'css/g-u-t/detailseite-gut-main.css', array(), '1.0', false);
    wp_enqueue_script('alle_anzeigen_button_js',       plugin_dir_url(__FILE__) . 'js/g-u-t/gut_main_detailseite_button.js', array('jquery'), false, false);
  }
  // first call the $post variable 
  global $post;
  // check the post hast a parent &&(AND) the paerent is the post with slug('g-u-t')
  if (($post->post_parent != 0) && ('g-u-t' == basename(get_permalink($post->post_parent)))) {
    wp_enqueue_style('gut_detail',                     plugin_dir_url(__FILE__) . 'css/g-u-t/detailseite_gut.css', array(), '1.9', false);
  }
}
add_action('wp_enqueue_scripts', 'gut_main_addtional_style_js', 10, 1);


///////////// MAP ///////////////////

add_action('wp_enqueue_scripts', 'map_related_dependency', 10, 1);

function map_related_dependency()
{
  global $post;

  // Diese Dependency loaded only when it is 'firmenverzeichnis' or only when its parents is 'firmenverzeichnis' // it can be checked with url  
  if (is_page('firmenverzeichnis') || $post->post_parent == url_to_postid(site_url('firmenverzeichnis'))) {
    wp_enqueue_style('page-firmenverzeichnis-1',         plugin_dir_url(__FILE__) . 'css/page-firmenverzeichnis-1.css', array(), '3.2', false);
    wp_enqueue_script('map_seite_nav',  plugin_dir_url(__FILE__) . 'js/map_seite_nav.js', array('jquery'), false, true);

    // Get CSS for Leaflet Framework before (! Dependency !) JS
    wp_enqueue_style('leaflet-main-css',                   plugin_dir_url(__FILE__) . 'node_modules/leaflet/dist/leaflet.css', array(), false, false);

    // Get 22Uhr Custom CSS & JS and Leaflet Framework JS
    wp_enqueue_script('leaflet-js',                        plugin_dir_url(__FILE__) . 'node_modules/leaflet/dist/leaflet.js', array(), false, false);
    wp_enqueue_script('leaflet-marker-cluster-js',         plugin_dir_url(__FILE__) . 'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js', array('leaflet-js'), false, false);
    wp_enqueue_script('leaflet-marker-cluster-group-js',   plugin_dir_url(__FILE__) . 'node_modules/leaflet.markercluster.layersupport/dist/leaflet.markercluster.layersupport.js', array('leaflet-marker-cluster-js'), false, false);
    //---------------------------------------------------------------------------------------------------------------------------- need to be called after all html ready---------
    wp_enqueue_script('list-modify-js',                    plugin_dir_url(__FILE__) . 'js/list_modify.js', array('jquery'), false, true);
    wp_enqueue_script('geocoder-js',                       plugin_dir_url(__FILE__) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.js', array('leaflet-js'), false, false);
    wp_enqueue_script('map-helper-fn',                     plugin_dir_url(__FILE__) . 'js/map_helper.js', array('leaflet-js', 'leaflet-marker-cluster-js', 'geocoder-js'), '1.3', true);

    // style 
    wp_enqueue_style('leaflet-marker-cluster-css',         plugin_dir_url(__FILE__) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.css', array(), false, false);
    wp_enqueue_style('leaflet-marker-cluster-default-css', plugin_dir_url(__FILE__) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css', array(), false, false);
    wp_enqueue_style('geocoder-css',                       plugin_dir_url(__FILE__) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.css', array(), false, false);
    wp_enqueue_style('font-awesome-css',                   '/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css', array(), false, false);
  }

  if (is_page('firmenverzeichnis') || $post->post_parent == url_to_postid(site_url('firmenverzeichnis'))) {
    // map_javascript for all map except g-u-t
    if (!is_page('g-u-t-gruppe')) {
      wp_register_script('map_modify-js',                  plugin_dir_url(__FILE__) . 'js/map.js', array('leaflet-js', 'leaflet-marker-cluster-js', 'geocoder-js', 'map-helper-fn'), false, true);
      wp_localize_script('map_modify-js', 'current_page_info', ['slug' => $post->post_name]);
      wp_enqueue_script('map_modify-js');
    } else if (is_page('g-u-t-gruppe')) {
      wp_enqueue_script('map_fg_gut',                      plugin_dir_url(__FILE__) . 'js/g-u-t/map_fg_gut.js', array('leaflet-js', 'leaflet-marker-cluster-js', 'geocoder-js', 'map-helper-fn'), '3.0', true);
    }
  }


  if (is_page('firmenverzeichnis')) {
    wp_enqueue_style('page-firmenverzeichnis-2',           plugin_dir_url(__FILE__) . 'css/page-firmenverzeichnis-2.css', array(), '3.3', false);
  } else if ($post->post_parent == url_to_postid(site_url('firmenverzeichnis')) && !is_page('g-u-t-gruppe')) {
    // hagebau 
    wp_enqueue_style('page-firmenverzeichnis-2',           plugin_dir_url(__FILE__) . 'css/page-firmenverzeichnis-2.css', array(), '3.3', false);
    wp_enqueue_style('page-firmenverzeichnis-fg',          plugin_dir_url(__FILE__) . 'css/page-firmenverzeichnis-fg.css', array(), '3.2', false);
    // wp_enqueue_style('map-app-style-css',                   plugin_dir_url(__FILE__) . 'css/map-app-style.css', array(), '3.3', false);
  } else if (is_page('g-u-t-gruppe')) {
    // G.U.T. Gruppe
    wp_enqueue_style('old-map-app-style-css',              plugin_dir_url(__FILE__) . 'css/g-u-t/old-map-app-style.css', array(), '3.3', false);
    wp_enqueue_style('page-firmenverzeichnis-fg',          plugin_dir_url(__FILE__) . 'css/page-firmenverzeichnis-fg.css', array(), '3.2', false);
    wp_enqueue_style('page-firmenverzeichnis-fg-gut',      plugin_dir_url(__FILE__) . 'css/page-firmenverzeichnis-fg-gut.css', array(), '3.2', false);
  }
}


///////////// Main map Seite component ///////////////////
function nav_close_p()
{
  $target_page_name = 'firmenverzeichnis';
  global $post;

  if (is_page($target_page_name) || $post->post_parent == url_to_postid(site_url('firmenverzeichnis'))) {
?>
    <span class="navicon-close">Close</span>
<?php
  }
};

add_action('wp_head', 'nav_close_p');



class local_fonts
{
  function __construct()
  {
    add_action('wp_enqueue_scripts', array($this, 'fonts'));
  }
  function fonts()
  {
    // Generate correspond fonts.css by https://gwfh.mranftl.com/fonts
    wp_enqueue_style('fonts_css', plugins_url('22uhr-plugin/css/fonts.css'), array(), 1.0, false);
  }
}
new local_fonts();
