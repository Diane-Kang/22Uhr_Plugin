<?php defined('ABSPATH') or die();


// Add a shortcode
add_shortcode('liste_fg_gut', 'liste_fg_gut_fn');

function liste_fg_gut_fn()
{
  // find Hauptverantwwortung

  $firmengruppen_name = 'G.U.T.';


  $arg = array(
    'post_type' => 'unternehmen',
    'posts_per_page' => -1,
    'meta_query' => array(
      'relation' => 'and',
      array(
        'key'       => 'firmengruppen',
        'value'        => $firmengruppen_name,
        'compare' => '='
      ),
    ),
  );

  $the_query = new WP_Query($arg);


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
      $abschlatung_zeit_number = "1";
      $text_label = 'Kein Werbelicht vorhanden';
      $data_group_format = 'abschaltung_' . str_replace("-", "_", $abschaltung_zeit->slug);
    } else if ('Sonderfall' == $abschaltung_tax_name) {
      // this value for sorting later
      $abschlatung_zeit_number = "0";
      $text_label = 'Sonderfälle';
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

  // Addtional Text for 
  $comment = '
  <div class = "abschaltung_filter_comment">
  Diese Firmen verzichten seit jeher auf Werbebeleuchtung und werden dies im Zuge der Teilnahme nun noch bewusster so beibehalten
  </div>';

  $filter_html = ' 
    <div class="abschaltung filter">
      <p>Filtern nach Abschaltzeit:</p>
      <select  name="uhrzeit" id="abschaltung_uhrzeit">' .
    $options_html . '
      </select>' .
    $comment . '
      </div>';

  $string .= $filter_html;


  // Unternehmen List section: 
  if ($the_query->have_posts()) {


    // Unternehmen List 
    $string .= '<div class="unternehmen">';
    $list_content = "";
    $i = 0;
    while ($the_query->have_posts()) {
      $the_query->the_post();
      $firmengruppen = get_post_meta(get_the_ID(),  'firmengruppen', true);
      $firmengruppen_hierarchie = get_post_meta(get_the_ID(),  'firmengruppen-hierarchie', true);


      $haupt = "";
      $neben = "";

      if ($firmengruppen_hierarchie == 0) {

        $haupt = '<div class="unternehme hauptverwaltung">' . generate_list_entry(get_the_ID()) . '</div>';
      } else if ($firmengruppen_hierarchie == 1) {
        $i = $i + 1;
        $args = array(
          'post_type' => 'unternehmen',
          'post_parent' => get_the_ID(),
          'posts_per_page' => -1,
        );
        $child_query = new WP_Query($args);

        $neben = "";
        $neben .= '<div class="here unternehme ' . $i . '">';

        $child_n = $child_query->found_posts;
        $neben .= generate_list_entry(get_the_ID(), "parent", $child_n);
        if ($child_query->have_posts()) {
          $neben .= '<div class="child-unternehmen-block">';
          while ($child_query->have_posts()) {
            $child_query->the_post();
            $neben .= generate_list_entry(get_the_ID(), "child", 0);
          }
          $neben .= '</div>';
        }
        $neben .= '</div>'; // '<div class="unternehme">'
      } else {
        $neben = "";
      }

      $list_content .= $neben;
    }

    $string .= $haupt . $list_content . '</div>'; //<div class="unternehmen">

  } else {
    $string = '<h3>Aktuell gibt es keine eingetragenen Unternehmen</h3>';
  }

  /* Restore original Post Data*/
  wp_reset_postdata();

  $string .= '</div>';

  return $string;
}


function generate_list_entry($post_id, $identity = 'haupt', $n_child = 0)
{

  $filter_uhr = get_the_terms($post_id, 'abschaltung');
  $filter_value = get_post_meta(get_the_ID(),  'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true);

  $abschaltung_tag = get_the_terms($post_id, 'abschaltung', true)[0];
  $abschaltung_name = $abschaltung_tag->name;
  $abschaltung_num = is_numeric(str_replace(" Uhr", "", $abschaltung_name)) ? str_replace(" Uhr", "", $abschaltung_name) : "";


  if (!empty($filter_uhr)) {
    foreach ($filter_uhr as $tag) {
      $zeit = str_replace("-", "_", $tag->slug);
      $zeit_num = is_numeric(str_replace(" Uhr", "", $tag->name)) ? str_replace(" Uhr", "", $tag->name) : "";
    }
  } else {
    $zeit = "empty";
  }


  $string = "";
  $string .= '<div class=" unternehmenseintrag-filter abschaltung_' . $zeit . '">';

  if ($n_child) {
    $string .= '<div class = icon-click-area>
                        <svg class="ionicon-chevron-down" viewBox="0 0 512 512">
                            <title>Chevron Down</title>
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M112 184l144 144 144-144"/>
                        </svg>
                    </div>';
  }


  switch ($identity) {
    case 'haupt':
      $string .= '<div class=" unternehmenseintrag werbebeleuchtung_' . $filter_value . '" value=' . $abschaltung_num . '>';
      break;
    case 'parent':
      $string .= '<div class=" parent-unternehmen unternehmenseintrag werbebeleuchtung_' . $filter_value . '" value=' . $abschaltung_num . '>';
      break;
    case 'child':
      $string .= '<div class="child-unternehmen unternehmenseintrag werbebeleuchtung_' . $filter_value . '" value=' . $abschaltung_num . '>';
      break;
  }

  $abschaltung_zeit_text = "";
  if ($zeit_num == "") {
    $abschaltung_zeit_text = "Seit jeher kein Werbelicht vorhanden";
  } else {
    $abschaltung_zeit_text = "Werbelicht-Abschaltung: " . $zeit_num . " Uhr";
  }
  //<img src="http://localhost:10008/wp-content/uploads/2022/10/GUT-Logo.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="" decoding="async" srcset="http://localhost:10008/wp-content/uploads/2022/10/GUT-Logo.jpg 580w, http://localhost:10008/wp-content/uploads/2022/10/GUT-Logo-300x200.jpg 300w" sizes="(max-width: 580px) 100vw, 580px" width="580" height="387">

  $string .= '      
        <div class="logo-wrapper">
            <a href="' . get_the_permalink($post_id) . '">
            ' . get_the_post_thumbnail($post_id) . '
            </a>
        </div>
        <div class="text">
            <h3><a href="' . get_the_permalink($post_id) . '">' . get_the_title($post_id) . '</a></h3>
            <div class="adresse">(' . get_post_meta($post_id, 'Land', true) . ')&nbsp;' . get_post_meta($post_id,  'Postleitzahl', true) . ' ' . get_post_meta($post_id,  'Ort', true) . '
            </div>
            <div class="map_link_point" id="map_id_' . $post_id . '">Auf Karte zeigen </div>
            <div class="abschaltung_zeit">' . $abschaltung_zeit_text . '</div>';



  $string .=
    '</div> 
    </div>' . // close unternehmenseintrag 
    '</div>';  // close unternehmenseintrag-filter

  return $string;
}
