<?php defined('ABSPATH') or die();


// Add a shortcode
add_shortcode('liste_unternehmen', 'show_unternehmen');

function show_unternehmen() {

  $the_query = new WP_Query( array( 'post_type' => 'unternehmen', 'posts_per_page' => -1 ) );

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
              <option data-group="abschaltung_all" value="25.0" selected >Alle Firmen zeigen</option>';

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
          $filter_value = get_post_meta(get_the_ID(),  'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true);
          $filter_uhr = get_the_terms(get_the_ID(), 'abschaltung');

            if (! empty($filter_uhr)) {
                foreach($filter_uhr as $tag) {
                    $zeit = str_replace("-", "_", $tag->slug);
                    $zeit_num = is_numeric(str_replace(" Uhr", "", $tag->name))?str_replace(" Uhr", "", $tag->name):"";
                }
            }
            else {
                $zeit = "empty";
            }

        if(empty($firmengruppen)){ // Einzel Beitrag
              $string .= '  <div class="unternehmenseintrag-filter abschaltung_' . $zeit . '" value="'.$zeit_num.'">
                                <div class="unternehmenseintrag werbebeleuchtung_'. $filter_value .'">
                                    <div class="logo-wrapper">
                                        <a href="' . get_the_permalink() . '">'
                                    .   get_the_post_thumbnail() .
                                        '</a>
                                    </div>' .
                                    '<div class="text">
                                        <h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>
                                        <div class="adresse">(' . get_post_meta( get_the_ID(),  'Land', true ) . ')&nbsp;' . get_post_meta( get_the_ID(),  'Postleitzahl', true ) . ' '
                                            . 	get_post_meta( get_the_ID(),  'Ort', true ) .
                                        '</div>
                                        <div class="map_link_point" id="map_id_'. get_the_ID() . '">Auf Karte zeigen </div>
                                        <div class="abschaltung_zeit">'.$zeit_num. ' Uhr</div>
                                    </div>
                                </div>
                            </div>';
          }
          else if ($firmengruppen_hierarchie == 0){
             // $firmengruppen_seite_url = get_post_meta( get_the_ID(),  'firmengruppen-seite', true );
              $string .=  ' <div class="unternehmenseintrag-filter abschaltung_' . $zeit . '">
              <div class="unternehmenseintrag firmengruppen werbebeleuchtung_'. $filter_value .'">'
                               // <p>Firmengruppe Hauptverwaltung</p>
                              .'
                              <div class="logo-wrapper"><a rel="noopener" href="/firmenverzeichnis/g-u-t-gruppe/">'. get_the_post_thumbnail() . '</a></div>' .
                          '     <div class="text">
                              <h3><a href="/firmenverzeichnis/g-u-t-gruppe/">' . get_the_title() . '</a></h3>
                              <div class="adresse">(' . get_post_meta( get_the_ID(),  'Land', true ) . ')&nbsp;' . get_post_meta( get_the_ID(),  'Postleitzahl', true ) . ' ' . get_post_meta( get_the_ID(),  'Ort', true ) .'</div>
                              <div class="map_link_point" id="map_id_'. get_the_ID() . '">Auf Karte zeigen </div>
                              <div class="abschaltung_zeit">'. str_replace("-", " ", $filter_uhr[0]->slug)  . '</div>
                              <div class="alle"> <a href="/firmenverzeichnis/g-u-t-gruppe/">Alle '.show_child_unternehmen_nummer(array('firmenname' => "G.U.T.")).' unserer Standorte mit Abschaltzeit anzeigen</a> </div>
                          </div>
                          </div>
                          </div>';

          }
          else if ($firmengruppen_hierarchie == 1 || $firmengruppen_hierarchie == 2){
            $string .=  '<div class="unternehmenseintrag firmengruppen werbebeleuchtung_'. $filter_value .' abschaltung_' . $zeit . ' display-none">
                            <h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>
                            <div class="map_link_point" id="map_id_'. get_the_ID() . '">Auf Karte zeigen </div>
                            <div class="abschaltung_zeit">'. str_replace("-", " ", $filter_uhr[0]->slug)  . '</div>
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