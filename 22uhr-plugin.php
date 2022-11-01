<?php

/*
  Plugin Name: 22Uhr Plugin 
  Plugin URI: https://github.com/Diane-Kang/22Uhr_Plugin
  Description: Customized plugin for 22Uhr.net. 
  Version: 1.0.0
  Author: Page-effect
  Author URI: Page-effect.com


  under plugin directory, dependencies need to be installed, 
  with specific Node.js and npm 

  Nodejs : v14.19.2
  npm 8.12.1
*/

defined( 'ABSPATH' ) or die( 'Are you ok?' );

if ( ! defined( 'PE_22Uhr_Plugin_Path' ) ) {
	define( 'PE_22Uhr_Plugin_Path', plugin_dir_path( __FILE__ ) );
}


///////////// Setting Custom type Post, taxonomy  ///////////////////
require_once  PE_22Uhr_Plugin_Path . 'includes/ctp_unternehmen.php';


////////////// Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen
require_once  PE_22Uhr_Plugin_Path . 'includes/unternehmen_json.php';
////////////// Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen/{firmengruppe}
require_once  PE_22Uhr_Plugin_Path . 'includes/geojson_generate.php';
$data = array(
  "firmengruppe" => "G.U.T.",
  "firmengruppe_slug" => "g-u-t"
);

$hello = new geojson_generate_Class($data);


////////////// Show ALL List of "Unternehmen"  and filter in front end
require_once  PE_22Uhr_Plugin_Path . 'includes/show_all_unternehmen_list.php';
////////////// Show ALL List of "Unternehmen"  and filter in front end
require_once  PE_22Uhr_Plugin_Path . 'includes/show_firmengruppe.php';



///////////// MAP ///////////////////

add_action( 'wp_enqueue_scripts', 'map_related_dependency', 10, 1 );

function map_related_dependency(){
  $target_page_name = 'firmenverzeichnis';
  global $post;

  // Diese Dependency loaded only when it is 'firmenverzeichnis' or only when its parents is 'firmenverzeichnis' // it can be checked with url  
  if (is_page($target_page_name) || $post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){

    // Get CSS for Leaflet Framework before (! Dependency !) JS
    wp_enqueue_style( 'leaflet-main-css',                   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.css' , array(), false, false);

    // Get 22Uhr Custom CSS & JS and Leaflet Framework JS
    wp_enqueue_script( 'leaflet-js',                        plugin_dir_url( __FILE__ ) . 'node_modules/leaflet/dist/leaflet.js', array(), false, false );
    wp_enqueue_script( 'leaflet-marker-cluster-js',         plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js', array(), false, true);
    wp_enqueue_script( 'leaflet-marker-cluster-group-js',   plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster.layersupport/dist/leaflet.markercluster.layersupport.js', array(), false, true);
    wp_enqueue_script( 'list_modify-js',                    plugin_dir_url( __FILE__ ) . 'list_modify.js', array('jquery'), false, true );
    wp_enqueue_script( 'pon-js-v2',                         plugin_dir_url( __FILE__ ) . 'pon.js', array('jquery'), '1.1', true);    
    wp_enqueue_script( 'geocoder-js',                       plugin_dir_url( __FILE__ ) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.js', array('leaflet-js'), false, true);
    //wp_enqueue_script( 'map_firmengruppen_js',              plugin_dir_url( __FILE__ ) . 'js/map_firmengruppen.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);


    // style 
    wp_enqueue_style( 'leaflet-marker-cluster-css',         plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.css', array(), false, false);
    wp_enqueue_style( 'leaflet-marker-cluster-default-css', plugin_dir_url( __FILE__ ) . 'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css', array(), false, false);
    wp_enqueue_style( 'geocoder-css',                       plugin_dir_url( __FILE__ ) . 'node_modules/leaflet-control-geocoder/dist/Control.Geocoder.css', array(), false, false);
    wp_enqueue_style( 'font-awesome-css',                   '/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css', array(), false, false);


    // map-app-style.css, controled by .page-id-1303!!!!!
    wp_enqueue_style( 'map-app-style-css',                  plugin_dir_url( __FILE__ ) . '/map-app-style.css', array(), '3.2', false);
    wp_enqueue_style( 'firmengruppen-style-css',            plugin_dir_url( __FILE__ ) . '/firmengruppen-seite.css', array(), '3.2', false);
  }
  if (is_page($target_page_name)){
    wp_enqueue_script( 'map_modify-js',                     plugin_dir_url( __FILE__ ) . '/map_modify.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);

  }
  if ($post->post_parent == url_to_postid( site_url('firmenverzeichnis'))){
    wp_enqueue_script( 'map_firmengruppen_js',              plugin_dir_url( __FILE__ ) . '/js/map_firmengruppen.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);
  }
}



///////////// Unternehmen Seite CSS  ///////////////////

function unternehmen_css() {
    if ( is_singular( 'unternehmen' )) {
        wp_enqueue_style( 'unternehmen_detail', plugin_dir_url( __FILE__ ) . '/unternehmen-detailseite.css', array(), '1.9', false);
    }
}
add_action( 'wp_enqueue_scripts', 'unternehmen_css', 20, 1 );


///////////// Main map Seite component ///////////////////
function nav_close_p() {
    if ( is_page(1303)) {
        ?>
<span class="navicon-close">Close</span>
<?php
    }
};

add_action('wp_head', 'nav_close_p');









function show_unternehmen_nummer() {

    $the_query = new WP_Query( array( 'post_type' => 'unternehmen', 'posts_per_page' => -1 ) );

    $total = wp_count_posts('unternehmen')->publish;

    return $total;
}
add_shortcode('unternehmen_nummer', 'show_unternehmen_nummer');


// Modify Template "Unternehmen"
//Before Conten


function before_content() {
    if ( is_singular('unternehmen') ) {
		

        $branche = get_the_terms( get_the_ID(), 'branche' );
        if (! empty($branche)) {
            foreach($branche as $tag) {
                $list_branchen .= '<span>' . $tag->name . '</span>';
            }
        }

        $abschaltung = get_the_terms( get_the_ID(), 'abschaltung' );
		
		
		
        if (! empty($abschaltung)) {
            foreach($abschaltung as $uhr) {
				if ($uhr->name == 'Nicht vorhanden') $abschaltung_um_uhr .= "Wir <span class='orange'>verzichten</span> seit jeher bewusst auf <span 		class='orange'>Werbebeleuchtung</span>. ";
				else $abschaltung_um_uhr .= "Wir schalten unsere im Freien sichtbare <span class='orange'>Werbebeleuchtung</span> um spätestens <span class='orange'>" . $uhr->name . " aus</span>. ";
            }
        }
		
		
		$angepasst = get_post_meta(get_the_ID(), 'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true);
		
		
		if ($angepasst == 'j') $text .= "Dies haben wir im Zuge der Teilnahme an diesem Projekt herbeigeführt und wird auch fortan so belassen.";
		else $text .= "Dies war bislang schon so und wird im Zuge der Teilnahme an diesem Projekt auch fortan so belassen.";


        $adresse = '<div class="adresse">
                    <div class="strasse-hn">' . get_post_meta(get_the_ID(), 'Straße und Hausnummer', true) . '</div>    
                    <div class="plz-ort">(' . get_post_meta( get_the_ID(),  'Land', true ) . ')&nbsp;' . get_post_meta(get_the_ID(), 'Postleitzahl', true)
            . '&nbsp;' . get_post_meta(get_the_ID(), 'Ort', true) . '</div>
					<div class="internet"><span>Bundesland: </span>' . get_post_meta(get_the_ID(), 'Bundesland', true) . '</div>
					<div class="branche"><span>Branche: </span>' . $list_branchen . '</div>
                    <div class="internet"><a href="' . get_post_meta(get_the_ID(), 'Internet', true) . '" target="_blank" rel="noopener">Internetseite</a></div>
                </div>
				<div class="abschaltung-angepasst">
				 <h3 class="abschaltung-um">' . $abschaltung_um_uhr . $text . '</h3>
				</div>
                 <h2 class="dabei">Deswegen sind wir bei „22 Uhr – Licht aus“ dabei:</h2>';

        echo $adresse;

    }
}

add_action( 'astra_single_header_after', 'before_content');



//After content
function after_content(){
    if ( is_singular('unternehmen') ) {

        $after = '<div class="zertifikat top-border">
				  <p class="beitrag-artenschutz">Durch die Abschaltung trägt die Firma ' . get_the_title() . ' zur Reduzierung der Lichtverschmutzung in ' . 										get_post_meta(get_the_ID(), 'Ort', true) . ' bei und leistet somit einen wertvollen Beitrag zum Artenschutz, Umweltschutz und Klimaschutz.</p>
                        <a href="' . get_post_meta(get_the_ID(), 'PDF Pfad', true) .'">
                        <img src="/wp-content/uploads/2022/05/Zertifikat-22-Uhr-Licht-aus-thumb-01.png" alt="Zertifikat 22 Uhr">
                        <h2>Zertifikat (PDF)</h2></a>
                  </div>
				  <div class="abschaltung top-border">
                    <h2>Details zur Licht- bzw. Werbelicht-Abschaltung:</h2>
                    <p>' . get_post_meta(get_the_ID(), 'Abschaltung', true) . '</p>                
                  </div>
                  <div class="uber-uns top-border"><h2>Worum geht es bei „22 Uhr – Licht aus?“</h2>
					<p>Das Projekt "22 Uhr – Licht aus" dient der Reduzierung der Lichtverschmutzung. Teilnehmende Firmen haben sich freiwillig dazu bereiterklärt, nachts die 							gesamte im Freien sichtbare Werbebeleuchtung so früh wie möglich, spätestens jedoch um 22 Uhr abzuschalten. Oder aber es ist bislang keine derartige 							Beleuchtung installiert und die Teilnahme an diesem Projekt motiviert die betreffenden Firmen dazu, dies ganz bewusst auch in Zukunft so zu belassen.						</p>
				</div>
                  <button class="zurueck" onclick="window.close();">zurück zum verzeichnis</button>';

        echo $after;
    }
}



add_action( 'astra_entry_content_after', 'after_content');






?>