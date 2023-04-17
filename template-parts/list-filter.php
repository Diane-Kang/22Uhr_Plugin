<?php

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