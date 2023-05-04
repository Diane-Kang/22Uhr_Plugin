<?php

function generate_abschlatung_filter($fg_slug = "")
{
  $abschaltung_zeit_all = get_terms(array(
    'taxonomy' => 'abschaltung',
    'hide_empty' => true,
  ));

  $abshaltung_zeit_list = [];
  if ($fg_slug == "") {
    foreach ($abschaltung_zeit_all as $tax_obj) {
      if (!empty($tax_obj->name)) {
        array_push($abshaltung_zeit_list, $tax_obj->name);
      }
    }
  } else {
    $arg = array(
      'post_type'       => 'unternehmen',
      'posts_per_page'  => -1,
      'post_parent'     => get_page_by_path($fg_slug, OBJECT, array('unternehmen'))->ID,
    );

    $fg = new WP_Query($arg);

    while ($fg->have_posts()) {
      $fg->the_post();
      array_push($abshaltung_zeit_list, get_the_terms(get_the_ID(), 'abschaltung')[0]->name);
    }
    $abshaltung_zeit_list = array_unique($abshaltung_zeit_list);
  }


  // make a list of option with abschlatungzeit 
  $options_array = [];
  foreach ($abschaltung_zeit_all as $abschaltung_zeit) {
    $abschaltung_tax_name = $abschaltung_zeit->name;
    // extract number from Abschaltung name: ex "22.30 Uhr" to "22.30" 
    $abschlatung_zeit_number = is_numeric(str_replace(" Uhr", "", $abschaltung_tax_name)) ? str_replace(" Uhr", "", $abschaltung_tax_name) : '';
    // text for the leaflet marker group ex "22_30"
    $data_group_format =  'abschaltung_' . str_replace(".", "_", $abschlatung_zeit_number) . '_uhr';

    if ('Nicht vorhanden' == $abschaltung_tax_name) {
      // this value for sorting later
      $abschlatung_zeit_number = 0.1;
      $text_label = 'Kein Werbelicht vorhanden';
      $data_group_format = 'abschaltung_' . str_replace("-", "_", $abschaltung_zeit->slug);
    } else if (is_numeric($abschlatung_zeit_number)) {
      $text_label = 'Bis spätestens ' .  $abschaltung_tax_name;
    } else {
      $text_label = 'Error: Abschaltung Taxonomy';
    }

    $option_values = 'data-group="' . $data_group_format . '" uhr_value=' . $abschlatung_zeit_number . ' >' . $text_label;
    if (in_array($abschaltung_tax_name, $abshaltung_zeit_list)) {
      $option = '<option ' . $option_values . '</option>';
    } else {
      $option = '<option style="display: none" ' . $option_values . '</option>';
    }
    // define array with key($uhr_value)
    $options_array[$abschlatung_zeit_number] = $option;
  }
  // reverse order, late time to early time
  krsort($options_array);
  if ($fg_slug == "") {
    $options_html = '<option data-group="abschaltung_all" value="25.0" selected >Alle Firmen zeigen</option>';
  } else {
    $options_html = '<option data-group="abschaltung_all" value="25.0" selected >Alle Standorte zeigen</option>';
  }

  foreach ($options_array as $option) {
    $options_html .= $option;
  }

  $filter_html = ' 
    <div class="abschaltung filter">
      <p>Filtern nach Abschaltzeit:</p>
      <select  name="uhrzeit" id="abschaltung_uhrzeit">' .
    $options_html . '
      </select>
    </div>';

  return $filter_html;
}
