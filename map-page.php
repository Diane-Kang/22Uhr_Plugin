<?php

/*
  Plugin Name: Customized plugin for 22Uhr.net
  Plugin URI: 
  Description: Change wp-admin login to whatever you want. example: http://www.example.com/my-login. Go under Settings and then click on "Permalinks" and change your URL under "Change wp-admin login".
  Version: 1.1.0
  Author: Nuno Morais Sarmento
  Author URI: https://www.nuno-sarmento.com


    under plugin directory, dependencies need to be installed, 
    with specific Node.js and npm 

    Nodejs : v14.19.2
    npm 8.12.1
*/

defined( 'ABSPATH' ) or die( 'Are you ok?' );

// Get CSS for Leaflet Framework before (! Dependency !) JS
function leaflet_original_css() {

    //save target page name as a variable
    $target_page_name = 'firmenverzeichnis';

    if (is_page($target_page_name)){
        wp_enqueue_style( 'leaflet-main-css', plugin_dir_url( __FILE__ ) . '/node_modules/leaflet/dist/leaflet.css' , array(), false, false);
    }
}
add_action( 'wp_enqueue_scripts', 'leaflet_original_css', 10, 1 );


// Get 22Uhr Custom CSS & JS and Leaflet Framework JS

add_action( 'wp_enqueue_scripts', 'leaflet_related_js', 20, 1 );
function leaflet_related_js() {

    //save target page name as a variable
    $target_page_name = 'firmenverzeichnis';

	if ( is_page($target_page_name)) {
        wp_enqueue_script( 'leaflet-js',                        plugin_dir_url( __FILE__ ) . '/node_modules/leaflet/dist/leaflet.js', array(), false, false );
        wp_enqueue_script( 'leaflet-marker-cluster-js',         plugin_dir_url( __FILE__ ) . '/node_modules/leaflet.markercluster/dist/leaflet.markercluster.js', array(), false, true);
        wp_enqueue_script( 'leaflet-marker-cluster-group-js',   plugin_dir_url( __FILE__ ) . '/node_modules/leaflet.markercluster.layersupport/dist/leaflet.markercluster.layersupport.js', array(), false, true);
        wp_enqueue_script( 'list_modify-js',                    plugin_dir_url( __FILE__ ) . '/list_modify.js', array('jquery'), false, true );
        wp_enqueue_script( 'map_modify-js',                     plugin_dir_url( __FILE__ ) . '/map_modify.js', array('leaflet-js','leaflet-marker-cluster-js', 'geocoder-js' ), '1.3', true);
        wp_enqueue_script( 'pon-js-v2',                            plugin_dir_url( __FILE__ ) . '/pon.js', array('jquery'), '1.1', true);    
        wp_enqueue_script( 'geocoder-js',                       plugin_dir_url( __FILE__ ) . '/node_modules/leaflet-control-geocoder/dist/Control.Geocoder.js', array('leaflet-js'), false, true);


        // style 
        wp_enqueue_style( 'leaflet-marker-cluster-css',         plugin_dir_url( __FILE__ ) . '/node_modules/leaflet.markercluster/dist/MarkerCluster.css', array(), false, false);
        wp_enqueue_style( 'leaflet-marker-cluster-default-css', plugin_dir_url( __FILE__ ) . '/node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css', array(), false, false);
        wp_enqueue_style( 'geocoder-css',                       plugin_dir_url( __FILE__ ) . '/node_modules/leaflet-control-geocoder/dist/Control.Geocoder.css', array(), false, false);
        wp_enqueue_style( 'font-awesome-css',                   '/wp-content/plugins/elementor/assets/lib/font-awesome/css/font-awesome.min.css', array(), false, false);


        // map-app-style.css, controled by .page-id-1303!!!!!
        wp_enqueue_style( 'map-app-style-css', plugin_dir_url( __FILE__ ) . '/map-app-style.css', array(), '3.2', false);
        }
}
add_action( 'wp_enqueue_scripts', 'leaflet_related_js', 20, 1 );


function unternehmen_css() {
    if ( is_singular( 'unternehmen' )) {
        wp_enqueue_style( 'unternehmen_detail', plugin_dir_url( __FILE__ ) . '/unternehmen-detailseite.css', array(), '1.9', false);
    }
}
add_action( 'wp_enqueue_scripts', 'unternehmen_css', 20, 1 );

function nav_close_p() {
    if ( is_page(1303)) {
        ?>
<span class="navicon-close">Close</span>
<?php
    }
};

add_action('wp_head', 'nav_close_p');

// create custom post type unternehmen
function create_posttype() {

    register_post_type( 'unternehmen',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Unternehmen' ),
                'singular_name' => __( 'Unternehmen' )
            ),
            'supports' => array('custom-fields', 'thumbnail', 'excerpt', 'revisions', 'editor', 'title', 'author','page-attributes'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'unternehmen'),
            'show_in_rest' => true,
            'hierarchical' => true,
            'publicly_queryable' => true,
            'taxonomies' =>  array( 'branche' ),
        )
    );
}

add_action( 'init', 'create_posttype' );

//create a custom taxonomy unternehmenskategorie

function create_custom_taxonomy_branche() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

    $labels = array(
        'name' => _x( 'Branche', 'taxonomy general name' ),
        'singular_name' => _x( 'Branchen', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Branche' ),
        'all_items' => __( 'Alle Branchen' ),
        'parent_item' => __( 'Parent Branche' ),
        'parent_item_colon' => __( 'Parent Branche:' ),
        'edit_item' => __( 'Edit Branche' ),
        'update_item' => __( 'Update Branche' ),
        'add_new_item' => __( 'Add New Branche' ),
        'new_item_name' => __( 'New Branche Name' ),
        'menu_name' => __( 'Branche' ),
    );

// Now register the taxonomy
    register_taxonomy('branche',array('unternehmen'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'public' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'branche' ),
    ));

}

//hook into the init action and call create_xy_taxonomies when it fires

add_action( 'init', 'create_custom_taxonomy_branche', 0 );


function create_custom_taxonomy_schlagwort() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

    $labels = array(
        'name' => _x( 'Schlagwort', 'taxonomy general name' ),
        'singular_name' => _x( 'Schlagwort', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Schlagwort' ),
        'all_items' => __( 'Alle Schlagwort' ),
        'parent_item' => __( 'Parent Schlagwort' ),
        'parent_item_colon' => __( 'Parent Schlagwort:' ),
        'edit_item' => __( 'Edit Schlagwort' ),
        'update_item' => __( 'Update Schlagwort' ),
        'add_new_item' => __( 'Add New Schlagwort' ),
        'new_item_name' => __( 'New Schlagwort Name' ),
        'menu_name' => __( 'Schlagwort' ),
    );

// Now register the taxonomy
    register_taxonomy('schlagwort',array('unternehmen'), array(
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'schlagwort' ),
    ));

}

//hook into the init action and call create_xy_taxonomies when it fires

add_action( 'init', 'create_custom_taxonomy_schlagwort', 0 );


// New Taxonomie Abschaltung

function create_custom_taxonomy_abschaltung() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

    $labels = array(
        'name' => _x( 'Abschaltung', 'taxonomy general name' ),
        'singular_name' => _x( 'Abschaltung', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Abschaltung' ),
        'all_items' => __( 'Alle Abschaltung' ),
        'parent_item' => __( 'Parent Abschaltung' ),
        'parent_item_colon' => __( 'Parent Abschaltung:' ),
        'edit_item' => __( 'Edit Abschaltung' ),
        'update_item' => __( 'Update Abschaltung' ),
        'add_new_item' => __( 'Add New Abschaltung' ),
        'new_item_name' => __( 'New Abschaltung Name' ),
        'menu_name' => __( 'Abschaltung' ),
    );

// Now register the taxonomy
    register_taxonomy('abschaltung',array('unternehmen'), array(
        'labels' => $labels,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'abschaltung' ),
    ));

}

//hook into the init action and call create_xy_taxonomies when it fires

add_action( 'init', 'create_custom_taxonomy_abschaltung', 0 );


// Register own Endpoint for API - /wp-json/22uhr-plugin/v1/unternehmen

function geojson_generate_api() {
    register_rest_route( '22uhr-plugin/v1', '/unternehmen/', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'unternehmen_geojson_generator'
    ) );
}

function unternehmen_geojson_generator() {
    $unternehmen = new WP_Query(array(
        'post_type' => 'unternehmen'
    ));

    $unternehmen_geojson = array();

    while ($unternehmen->have_posts()) {
        $unternehmen->the_post();

        $longi = get_post_meta( get_the_ID(), $key = "2-Laengengrad", true);
        settype ($longi, "float");

        $lati = get_post_meta( get_the_ID(), $key = "1-Breitengrad", true);
        settype ($lati, "float");

        //variable type string
        $werbebeleuchtung_jn = get_post_meta( get_the_ID(), $key = "Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)", true);

        $abschaltung = get_the_terms( get_the_ID(), 'abschaltung' );
        if (!empty($abschaltung)) {
            foreach ($abschaltung as $tag) {
                $uhrzeit = $tag;
            }
        }

        array_push($unternehmen_geojson, array(
            'type'=> 'Feature',
            'id' => get_the_ID(),
            'geometry'=> array(
                'type'=> 'Point',
                'coordinates' =>  array($longi,$lati)
            ),
            'properties'=>array(
                'name' => get_the_title(),
                'post_id' => get_the_ID(),
                'url' => get_permalink()
            ),
            'filter'=> array(
                'werbebeleuchtung' => $werbebeleuchtung_jn,
                'abschaltung' => $uhrzeit
            ),
            'firmengruppen' => get_post_meta(get_the_ID(), 'firmengruppen',true),
            'firmengruppen-hierarchie' =>get_post_meta(get_the_ID(), 'firmengruppen-hierarchie', true)
        ));
    }

    $wrapper_array = array(
        "type" => "FeatureCollection",
        "features" => $unternehmen_geojson
    );

    return $wrapper_array;
}

add_action( 'rest_api_init', 'geojson_generate_api');


// Show List of "Unternehmen"  and filter in front end

function show_unternehmen() {

    $the_query = new WP_Query( array( 'post_type' => 'unternehmen', 'posts_per_page' => 400 ) );

    $total = wp_count_posts('unternehmen')->publish;

    $string =
        '<div class="wrapper-liste">
        <div class="search_bar">
            <form action="/" method="get" autocomplete="off">
                <input type="text" name="s" placeholder="Suchen..." id="keyword" class="input_search" onkeyup="fetch()">
                <button>Suchen</button>
            </form>
        <div class="search_result" id="datafetch">
            <ul>
                <li></li>
            </ul>
         </div>
    </div>';




    // Unternehmen List section: 
    if ( $the_query->have_posts() ) {



        // List of abshaltung tags
        $abschaltung_tags = get_terms( array(
            'taxonomy' => 'abschaltung',
            'hide_empty' => true,
        ));


        // Begining of Selector html
        $string .= '
        <div class="abschaltung filter">
            <p>Filtern nach Abschaltzeit:</p>
            <select  name="uhrzeit" id="abschaltung_uhrzeit">
                <option data-group="abschaltung_all" value="0" selected >Alle Firmen zeigen</option>';

        // make a list of option with abschaltung_tags
        $options_array = [];

        foreach ($abschaltung_tags as $uhr_tag){

            $i = 0;
            // In oder to use slug for a data-group 
            $abschaltung_slug_without_middle =  str_replace("-", "_", $uhr_tag->slug); 
            $uhr_value = str_replace(["abschaltung_", "-uhr"],"", $uhr_tag->slug);
            $uhr_value = str_replace("-",".", $uhr_value);


            if ("Nicht vorhanden" != $uhr_tag->name ) {
                $temp_string =
                '<option data-group="abschaltung_' . $abschaltung_slug_without_middle . '"  
                value="' . $uhr_tag->term_id . '"
                uhr_value=' . $uhr_value . ' > Bis spätestens '.  $uhr_tag->name .'</option>';
                
            }

            else { 
                $uhr_value = "0.1";//for the last position 
                $temp_string = 
                '<option data-group="abschaltung_' . $abschaltung_slug_without_middle . '"  
                value="' . $uhr_tag->term_id . '"
                uhr_value=' . $uhr_value . ' > Kein Werbelicht vorhanden </option>';
            }

            $options_array[$uhr_value] = $temp_string; // define array with key($uhr_value)

        }


        krsort($options_array); // reverse order, late time to early time
 
        foreach($options_array as $option_array){
            $string .= $option_array;
        }


        $string .=
            '</select>
        </div>';
        

        // Unternehmen List 
        $string .= '<div class="unternehmen">';
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $firmengruppen = get_post_meta(get_the_ID(),  'firmengruppen', true);
            $firmengruppen_hierarchie = get_post_meta(get_the_ID(),  'firmengruppen-hierarchie', true);

            if(empty($firmengruppen)){ // Einzel Beitrag
                $filter_value = get_post_meta(get_the_ID(),  'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true);


                $filter_uhr = get_the_terms(get_the_ID(), 'abschaltung');

                if (! empty($filter_uhr)) {
                    foreach($filter_uhr as $tag) {
                        $zeit = str_replace("-", "_", $tag->slug);
                    }
                }
                else {
                    $zeit = "empty";
                }




                $string .= '<div class="unternehmenseintrag werbebeleuchtung_'. $filter_value .' abschaltung_' . $zeit . '">
                        <div class="logo-wrapper"><a target="_blank" rel="noopener" href="' . get_the_permalink() . '">'
                    . get_the_post_thumbnail() .
                    '</a></div>' .
                    '<div class="text">
                        <h3><a target="_blank" rel="noopener" href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>
                        <div class="adresse">(' . get_post_meta( get_the_ID(),  'Land', true ) . ')&nbsp;' . get_post_meta( get_the_ID(),  'Postleitzahl', true ) . ' '
                    . 	get_post_meta( get_the_ID(),  'Ort', true ) .
                    '</div>
                        <div class="map_link_point" id="map_id_'. get_the_ID() . '">Auf Karte zeigen </div>
                        </div></div>';
            }
            else if ($firmengruppen_hierarchie == 0){
                $firmengruppen_seite_url = get_post_meta( get_the_ID(),  'firmengruppen-seite', true );
                $string .=  '<div class="unternehmenseintrag firmengruppen">
                                <div class="logo-wrapper"><a target="_blank" rel="noopener" href="' . $firmengruppen_seite_url . '">'. get_the_post_thumbnail() . '</a></div>' .
                            '<div class="text">
                                <h3><a target="_blank" rel="noopener" href="' . $firmengruppen_seite_url . '">' . get_the_title() . '</a></h3>
                                <a href="'.$firmengruppen_seite_url . '">Hier alle 270 Standorte anzeigen</a>
                            </div>
                            </div>';

            }
        }
        $string .='</div>';

    }
    else {
        $string = '<h3>Aktuell gibt es keine eingetragenen Unternehmen</h3>';
    }

    /* Restore original Post Data*/
    wp_reset_postdata();

    $string .= '</div>';

    return $string;
}

// Add a shortcode
add_shortcode('liste_unternehmen', 'show_unternehmen');

function show_unternehmen_nummer() {

    $the_query = new WP_Query( array( 'post_type' => 'unternehmen', 'posts_per_page' => 400 ) );

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