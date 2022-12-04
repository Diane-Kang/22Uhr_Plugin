<?php

/*
  Plugin Name: 22Uhr Plugin 
  Plugin URI: https://github.com/Diane-Kang/22Uhr_Plugin
  Description: Customized plugin for 22Uhr.net. 
  Version: 1.4.0
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
////////////// Unternehmen post 
require_once  PE_22Uhr_Plugin_Path . 'includes/ctp_unternehmen_frontend.php';


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
require_once  PE_22Uhr_Plugin_Path . 'includes/show_all_unternehmen_list.php';

////////////// Show ALL List of "Unternehmen"  and filter in front end
require_once  PE_22Uhr_Plugin_Path . 'includes/show_firmengruppe.php';

////////////// shortcodes
require_once  PE_22Uhr_Plugin_Path . 'includes/shortcodes.php';




///////////// Unternehmen Seite CSS  ///////////////////

function unternehmen_css() {
    if ( is_singular( 'unternehmen' )) {
        wp_enqueue_style( 'unternehmen_detail', plugin_dir_url( __FILE__ ) . 'css/unternehmen-detailseite.css', array(), '1.9', false);
    }
  }
  add_action( 'wp_enqueue_scripts', 'unternehmen_css', 20, 1 );
  
  
  




///////////// MAP ///////////////////

add_action( 'wp_enqueue_scripts', 'map_related_dependency', 10, 1 );

function map_related_dependency(){
  $target_page_name = 'firmenverzeichnis';
  global $post;

  // Diese Dependency loaded only when it is 'firmenverzeichnis' or only when its parents is 'firmenverzeichnis' // it can be checked with url  
  if (is_page($target_page_name) || $post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){

    // Get CSS for Leaflet Framework before (! Dependency !) JS
    wp_enqueue_style( 'leaflet-main-css',                   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.css' , array(), false, false);
    // wp_enqueue_style( 'leaflet-main-css',                   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.css' , array(), false, false);
    wp_enqueue_script( 'ionicon-js',                        plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/ionicons.js', array(), false, false );

    // Get 22Uhr Custom CSS & JS and Leaflet Framework JS
    wp_enqueue_script( 'leaflet-js',                        plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.js', array(), false, false );
    wp_enqueue_script( 'leaflet-marker-cluster-js',         plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js', array(), false, true);
    wp_enqueue_script( 'leaflet-marker-cluster-group-js',   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster.layersupport/dist/leaflet.markercluster.layersupport.js', array(), false, true);
    wp_enqueue_script( 'list_modify-js',                    plugin_dir_url( __FILE__ ) . 'js/list_modify.js', array('jquery'), false, true );
    wp_enqueue_script( 'pon-js-v2',                         plugin_dir_url( __FILE__ ) . 'pon.js', array('jquery'), '1.1', true);    
    wp_enqueue_script( 'geocoder-js',                       plugin_dir_url( __FILE__ ) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.js', array('leaflet-js'), false, true);
    //wp_enqueue_script( 'map_firmengruppen_js',              plugin_dir_url( __FILE__ ) . 'js/map_firmengruppen.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);


    // style 
    wp_enqueue_style( 'leaflet-marker-cluster-css',         plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.css', array(), false, false);
    wp_enqueue_style( 'leaflet-marker-cluster-default-css', plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css', array(), false, false);
    wp_enqueue_style( 'geocoder-css',                       plugin_dir_url( __FILE__ ) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.css', array(), false, false);
    wp_enqueue_style( 'font-awesome-css',                   '/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css', array(), false, false);


    // map-app-style.css, controled by .page-id-1303!!!!!
    wp_enqueue_style( 'map-app-style-css',                  plugin_dir_url( __FILE__ ) . 'css/map-app-style.css', array(), '3.3', false);
    
  }

  wp_enqueue_script( 'map_init_js',              plugin_dir_url( __FILE__ ) . 'js/map_intiialize.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);
  wp_enqueue_script( 'map_custom_fn_js',              plugin_dir_url( __FILE__ ) . 'js/map_custom_fn.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);
  
  if (is_page($target_page_name)){
    wp_enqueue_script( 'map_modify-js',                     plugin_dir_url( __FILE__ ) . 'js/map_modify.js', array('map_custom_fn_js', 'leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.4', true);

  }
  if ($post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){
    wp_enqueue_script( 'map_firmengruppen_js',              plugin_dir_url( __FILE__ ) . 'js/map_firmengruppen.js', array('map_custom_fn_js','leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);
    wp_enqueue_style( 'firmengruppen-style-css',            plugin_dir_url( __FILE__ ) . 'css/firmengruppen-seite.css', array(), '3.2', false);
  }
}



///////////// Main map Seite component ///////////////////
function nav_close_p() {
    if ( is_page(1303)) {
        ?>
<span class="navicon-close">Close</span>
<?php
    }
};

add_action('wp_head', 'nav_close_p');





?>