<?php
// Add a shortcode
add_shortcode('liste_unternehmen_diane', 'generate_unternehmen_list');

function generate_unternehmen_list() {
  $abschaltung_filter = generate_abschlatung_filter();


  // Quelle Query for list 
  $arg = 
    array(
      'post_type' => 'unternehmen', 
      'posts_per_page' => 10);
  
  $unternehmen_query = new WP_Query( $arg );
  $unternehmen_list_html = '';
  while ( $unternehmen_query->have_posts() ) {
    $unternehmen_query->the_post();
    $postId = get_the_ID();

    $firmengruppe             = get_post_meta($postId,  'firmengruppen', true);
    $firmengruppe_hierarchie  = get_post_meta($postId,  'firmengruppen-hierarchie', true);
    $firmengruppe_has_page    = get_post_meta($postId,  'is_fg_page', true);
    
    // Einzel Firmaeintrag
    if($firmengruppe == ""){
      $unternehmen_list_html .= generate_eintrag($postId);
    }else {
      $unternehmen_list_html .= generate_eintrag($postId, 'dropdown');
    }
  }
  $unternehmenListe = '<div class="unternehmen">' . $unternehmen_list_html .'</div>';
  // $unternehmenlist = generate_unternehmenlist();// this for firmenverzeichnis
  return  $abschaltung_filter . $unternehmenListe;
}

function generate_eintrag($postId, $type='basic'){

  // extra configuration for filter 
  $abschaltung_tag = get_the_terms($postId, 'abschaltung', true)[0];
  $abschaltung_underline = str_replace("-", "_",$abschaltung_tag->slug);
  $abschaltung_num = is_numeric(str_replace(" Uhr", "", $abschaltung_tag->name)) ? str_replace(" Uhr", "", $abschaltung_tag->name) : "";
  
  $unternehme = array (
    // j oder n
    'werbebeleuchtung'        => 'werbebeleuchtung_'.get_post_meta($postId, 'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true),  
    'abschaltung_data_group'  => 'abschaltung_'.$abschaltung_underline,
    'abschaltung_value'       => $abschaltung_num,
    'abschaltung_text'        => ($abschaltung_num == "") ? "Seit jeher kein Werbelicht vorhanden" : "Werbelicht-Abschaltung: ".$abschaltung_num. " Uhr", 
    'permalink'               => get_the_permalink(), 
    'thumbnail'               => get_the_post_thumbnail(),
    'title'                   => get_the_title(),
    'adresse-land'            => get_post_meta($postId, 'Land', true),
    'adresse-postzahl'        => get_post_meta($postId, 'Postleitzahl', true),
    'adresse-ort'             => get_post_meta($postId, 'Ort', true),
    'id'                      => $postId,
  );
  $string = "";
  switch ($type) {
    case 'basic':
      $string = eintrag_basic($unternehme);
      break;
    case 'dropdown':
      $string = eintrag_dropdown($unternehme);
      break;
  }

  return $string;
}

function eintrag_basic($unternehme){
  $string =
  '<div class="unternehmenseintrag '.$unternehme["werbebeleuchtung"].' '.$unternehme["abschaltung_data_group"].'" value='.$unternehme["abschaltung_value"].'>
    <div class="logo-wrapper">
      <a href="'.$unternehme["permalink"].'">'.$unternehme["thumbnail"].'</a>
    </div>
    <div class="text">
      <h3><a href="'.$unternehme["permalink"].'">'.$unternehme["title"].'</a></h3>
      <div class="adresse">('.$unternehme["adresse-land"].')&nbsp;'.$unternehme["adresse-postzahl"].' '.$unternehme["adresse-ort"].'</div>
      <div class="map_link_point" id="map_id_'.$unternehme["id"].'">Auf Karte zeigen </div>
      <div class="abschaltung_zeit">'.$unternehme["abschaltung_text"].'</div>
      '. get_post_meta($unternehme["id"],  'firmengruppen', true). get_post_meta($unternehme["id"],  'firmengruppen-hierarchie', true) . get_post_meta($unternehme["id"],  'is_fg_page', true).'
    </div>
  </div>';
  return $string; 
}

function eintrag_dropdown($unternehme){
  $string =
  '<div class="unternehmenseintrag unternehmenseintrag--dropdown '.$unternehme["werbebeleuchtung"].' '.$unternehme["abschaltung_data_group"].'" value='.$unternehme["abschaltung_value"].'>
    <div class="logo-wrapper">
      '.$unternehme["thumbnail"].'
    </div>
    <div class="text">
      <h3>'.$unternehme["title"].'</h3>
    </div>
  </div>';
  return $string; 
}

function generate_abschlatung_filter(){
  $abschaltung_zeit_all = get_terms( array(
    'taxonomy' => 'abschaltung',
    'hide_empty' => true,
  ));

  // make a list of option with abschlatungzeit 
  $options_array = [];
  foreach ($abschaltung_zeit_all as $abschaltung_zeit){
    // extract number from Abschaltung name: ex "22.30 Uhr" to "22.30" 
    $abschlatung_zeit_number = is_numeric(str_replace(" Uhr", "", $abschaltung_zeit->name)) ? str_replace(" Uhr", "", $abschaltung_zeit->name) : '' ;
    // text for the leaflet marker group ex "22_30"
    $data_group_format =  'abschaltung_' . str_replace(".", "_", $abschlatung_zeit_number) . '_uhr';
    
    if ('Nicht vorhanden' == $abschaltung_zeit->name ){
      // this value for sorting later
      $abschlatung_zeit_number = 0.1; 
      $text_label = 'Kein Werbelicht vorhanden';
      $data_group_format = 'abschaltung_' . str_replace("-", "_", $abschaltung_zeit->slug);
    }else if (is_numeric($abschlatung_zeit_number)){
      $text_label = 'Bis spätestens '.  $abschaltung_zeit->name;
    }else{
      $text_label = 'Error: Abschaltung Taxonomy';
    }

    $option = '<option data-group="' . $data_group_format . '" uhr_value='. $abschlatung_zeit_number . ' >'.
        $text_label . '
      </option>';
    
    // define array with key($uhr_value)
    $options_array[$abschlatung_zeit_number] = $option; 
  }
  // reverse order, late time to early time
  krsort($options_array); 
  
  $options_html = '<option data-group="abschaltung_all" value="25.0" selected >Alle Firmen zeigen</option>'; 
  foreach($options_array as $option){
    $options_html .= $option;
  }
  
  $filter_html = ' 
    <div class="abschaltung filter">
      <p>Filtern nach Abschaltzeit:</p>
      <select  name="uhrzeit" id="abschaltung_uhrzeit">'.
        $options_html.'
      </select>
    </div>';
    
  return $filter_html;
}