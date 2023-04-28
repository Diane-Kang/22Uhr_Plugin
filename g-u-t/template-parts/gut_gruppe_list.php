<?php defined('ABSPATH') or die();


add_action( 'astra_entry_content_after', 'after_content_gut_main', 11);

function after_content_gut_main() {
    if (is_single('g-u-t')) {
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

        if ( $the_query->have_posts() ) {
            $string ="";
            // Unternehmen List 
            $string .= 
            '<div class="list_wrapper">
            <div class="haupthaus-list show-only-short-list">
            <h2>Übersicht aller Häuser der G.U.T.-GRUPPE</h2>';
            $list_content = "";
            $i = 0 ;
              while ( $the_query->have_posts() ) {
                  $the_query->the_post();
                  $firmengruppen = get_post_meta(get_the_ID(),  'firmengruppen', true);
                  $firmengruppen_hierarchie = get_post_meta(get_the_ID(),  'firmengruppen-hierarchie', true);
      
      
                 if ($firmengruppen_hierarchie == 1){
                    $string .= '<div class="item"><a href=' . get_the_permalink() .'>' . get_the_title().'</a></div>';

                  }
    
                  
              }
      
              $string .=  
              
              '</div>
              <button class="show_all">alle anzeigen</button>
              </div>
              <div class="zertifikat top-border firmengruppe"> 
              <p class="beitrag-artenschutz">Die <span>G.U.T.-GRUPPE</span>, eine bundesweit agierende Unternehmensgruppe für Gebäude- und Umwelttechnik, trägt durch die Werbelicht-Abschaltungen aller beteiligten Standorte zur Reduzierung der Lichtverschmutzung in Deutschland bei und leistet somit einen wertvollen Beitrag zum Artenschutz, Umweltschutz und Klimaschutz.</p>
              </div>'; 
      
        }
        else {
            $string = '<h3>Aktuell gibt es keine eingetragenen Unternehmen</h3>';
        }
      
        /* Restore original Post Data*/
        wp_reset_postdata();
      echo $string;
    }

   
}
