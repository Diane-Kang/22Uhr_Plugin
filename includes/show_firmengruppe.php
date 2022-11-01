<?php defined('ABSPATH') or die();


// Add a shortcode
add_shortcode('liste_firmengruppen', 'show_firmengruppen');

function show_firmengruppen() {


        $args = array(
            'post_type' => 'unternehmen',
            'post_parent' => 3724,
            'posts_per_page' => -1,
            // 'meta_query'  => array(
            //        array(
            //            'key' => 'firmengruppen',
            //            'meta_compare' => '=', // see documentation for compare options
            //            'value' => 'G.U.T.'
            //        )
            //    )
       
       );

    
       $the_query = new WP_Query($args);


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
    
    

    function generate_list_entry($post_id, $child=0){
        $string = '
        <div class="'. ($child == 1 ? 'child-unternehmen' : 'parent-unternehmen') . ' unternehmenseintrag werbebeleuchtung_'. $filter_value .' abschaltung_' . $zeit . '">
            <div class="logo-wrapper">
                <a target="_blank" rel="noopener" href="' . get_the_permalink($post_id) . '">
                '. get_the_post_thumbnail($post_id) . '
                </a>
            </div>
            <div class="text">
                <h3><a target="_blank" rel="noopener" href="' . get_the_permalink($post_id) . '">' . get_the_title($post_id) . '</a></h3>
                <div class="adresse">('. get_post_meta($post_id, 'Land', true ) . ')&nbsp;' . get_post_meta($post_id,  'Postleitzahl', true ) . ' '. get_post_meta($post_id,  'Ort', true ) . '
                </div>
                <div class="map_link_point" id="map_id_'. $post_id . '">Auf Karte zeigen </div>
            </div>
        </div>';

        return $string;

    }

  // Unternehmen List section: 
  if ( $the_query->have_posts() ) {
    

      // Unternehmen List 
      $string .= '<div class="unternehmen">';
      while ( $the_query->have_posts() ) {
          $the_query->the_post();
          $firmengruppen = get_post_meta(get_the_ID(),  'firmengruppen', true);
          $firmengruppen_hierarchie = get_post_meta(get_the_ID(),  'firmengruppen-hierarchie', true);
          
          $string .= '<div class="unternehme">';
          $string .= generate_list_entry(get_the_ID());
          $args = array(
            'post_type' => 'unternehmen',
            'post_parent' => get_the_ID(),
            'posts_per_page' => -1,
            );  
          $child_query = new WP_Query($args);
          if ($child_query->have_posts()){
            $string.= '<div class="child-unternehmen-block">';
            while ($child_query->have_posts()){
                $child_query->the_post();
                $string .= generate_list_entry(get_the_ID(),1);
            }
            $string .= '</div>' ;
          }
          $string.='</div>';
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