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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( ! defined( 'PE_22Uhr_Plugin_Path' ) ) {
	define( 'PE_22Uhr_Plugin_Path', plugin_dir_path( __FILE__ ) );
}



///////////// Setting Custom type Post, taxonomy  ///////////////////
require_once  PE_22Uhr_Plugin_Path . 'includes/ctp_unternehmen_init.php';

////
require_once  PE_22Uhr_Plugin_Path . 'includes/shortcodes.php';

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

////////////// Only for G.U.T Gruppe shortcodes & functions 
require_once  PE_22Uhr_Plugin_Path . 'template-parts/g-u-t/firma-counting.php';
require_once  PE_22Uhr_Plugin_Path . 'includes/gut_gruppe_list.php';


////////////// Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen
require_once  PE_22Uhr_Plugin_Path . 'includes/geojson_unternehmen.php';
////////////// Class for Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen/{firmengruppe}
require_once  PE_22Uhr_Plugin_Path . 'includes/geojson_firmengruppen.php';
$data = array(
  "firmengruppe" => "G.U.T.",
  "firmengruppe_slug" => "g-u-t"
);
$hello = new geojson_generate_Class($data);   



////////////// Show ALL List of "Unternehmen"  and filter in front end
require_once  PE_22Uhr_Plugin_Path . 'includes/show_firmengruppe.php';
require_once  PE_22Uhr_Plugin_Path . 'includes/gut_gruppe_child_template.php';





///////////// Unternehmendetail Seite CSS  ///////////////////

// This applies for all the Unternehmendetailsite (CPT-unternehmen seite)
function unternehmen_css() {
    if ( is_singular( 'unternehmen' )) {
        wp_enqueue_style( 'unternehmen_detail', plugin_dir_url( __FILE__ ) . 'css/unternehmen-detailseite.css', array(), '1.9', false);
    }
  }
add_action( 'wp_enqueue_scripts', 'unternehmen_css', 20, 1 );
  
  
/// GUT Detailseite

function gut_main_addtional_style_js() {
  // g-u-t main page only 
  if ( is_single('g-u-t')) {
      wp_enqueue_style( 'gut_detail',                     plugin_dir_url( __FILE__ ) . 'css/detailseite_gut.css', array(), '1.9', false);
      wp_enqueue_script( 'alle_anzeigen_button_js',       plugin_dir_url( __FILE__ ) . 'js/gut_main_detailseite_button.js', array('jquery'), false, false );
      wp_enqueue_style( 'gut_detail_main',                     plugin_dir_url( __FILE__ ) . 'css/detailseite-gut-main.css', array(), '1.0', false);
  }
  
  // first call the $post variable 
  global $post;
  // check the post hast a parent &&(AND) the paerent is the post with slug('g-u-t')
  if ( ($post->post_parent != 0) && ('g-u-t' == basename(get_permalink($post->post_parent)))) {
    wp_enqueue_style( 'gut_detail',                     plugin_dir_url( __FILE__ ) . 'css/detailseite_gut.css', array(), '1.9', false);
  }
}
add_action( 'wp_enqueue_scripts', 'gut_main_addtional_style_js', 10, 1 );


///////////// MAP ///////////////////

add_action( 'wp_enqueue_scripts', 'map_related_dependency', 10, 1 );

function map_related_dependency(){
  $target_page_name = 'firmenverzeichnis';
  global $post;

  // Diese Dependency loaded only when it is 'firmenverzeichnis' or only when its parents is 'firmenverzeichnis' // it can be checked with url  
  if (is_page($target_page_name) || $post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){
    wp_enqueue_style( 'page-firmenverzeichnis-all',          plugin_dir_url( __FILE__ ) . 'css/page-firmenverzeichnis-all.css', array(), '3.2', false);

    wp_enqueue_script( 'map-seite-addtional-functions-js',  plugin_dir_url( __FILE__ ) . 'js/map_seite_addtional_functions.js', array('jquery'), false, true );

    // Get CSS for Leaflet Framework before (! Dependency !) JS
    wp_enqueue_style( 'leaflet-main-css',                   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.css' , array(), false, false);

    // Get 22Uhr Custom CSS & JS and Leaflet Framework JS
    wp_enqueue_script( 'leaflet-js',                        plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.js', array(), false, false );
    wp_enqueue_script( 'leaflet-marker-cluster-js',         plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js', array('leaflet-js'), false, false);
    wp_enqueue_script( 'leaflet-marker-cluster-group-js',   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster.layersupport/dist/leaflet.markercluster.layersupport.js', array('leaflet-marker-cluster-js'), false, false);
    //---------------------------------------------------------------------------------------------------------------------------- need to be called after all html ready---------
    wp_enqueue_script( 'list-modify-js',                    plugin_dir_url( __FILE__ ) . 'js/list_modify.js', array('jquery'), false, true );
    wp_enqueue_script( 'geocoder-js',                       plugin_dir_url( __FILE__ ) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.js', array('leaflet-js'), false, false);
    wp_enqueue_script( 'map-custom-fn-js',                  plugin_dir_url( __FILE__ ) . 'js/map_custom_fn.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);

    // style 
    wp_enqueue_style( 'leaflet-marker-cluster-css',         plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.css', array(), false, false);
    wp_enqueue_style( 'leaflet-marker-cluster-default-css', plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css', array(), false, false);
    wp_enqueue_style( 'geocoder-css',                       plugin_dir_url( __FILE__ ) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.css', array(), false, false);
    wp_enqueue_style( 'font-awesome-css',                   '/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css', array(), false, false);
  }
  if (is_page($target_page_name)){
    wp_enqueue_style( 'firmenverzeichnis-css',                  plugin_dir_url( __FILE__ ) . 'css/page-firmenverzeichnis.css', array(), '3.3', false);
    wp_enqueue_script( 'map_modify-js',                   plugin_dir_url( __FILE__ ) . 'js/map_modify.js', array( 'leaflet-js','leaflet-marker-cluster-js', 'geocoder-js', 'map-custom-fn-js'), false, true);
  }
  if ($post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){
    wp_enqueue_style( 'map-app-style-css',                  plugin_dir_url( __FILE__ ) . 'css/map-app-style.css', array(), '3.3', false);
    wp_enqueue_style( 'page-firmenverzeichnis-fg-gut',          plugin_dir_url( __FILE__ ) . 'css/page-firmenverzeichnis-fg-gut.css', array(), '3.2', false);
    wp_enqueue_script( 'map-firmengruppen-js',                   plugin_dir_url( __FILE__ ) . 'js/map_firmengruppen.js', array( 'leaflet-js','leaflet-marker-cluster-js', 'geocoder-js', 'map-custom-fn-js'), '3.0', true);
  }

  
}


///////////// Main map Seite component ///////////////////
function nav_close_p() {
  $target_page_name = 'firmenverzeichnis';
  global $post;

  if (is_page($target_page_name) || $post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){
        ?>
    <span class="navicon-close">Close</span>
    <?php
  }
};

add_action('wp_head', 'nav_close_p');



   

?>