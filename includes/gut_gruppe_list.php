<?php defined('ABSPATH') or die();




add_action( 'astra_single_header_after', 'before_content_gut_main');

function before_content_gut_main() {
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
    
            // Unternehmen List 
            $string .= 
            '<div class="haupthaus-list">
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
      
              $string .=  '</div>'; //<div class="unternehmen">
      
        }
        else {
            $string = '<h3>Aktuell gibt es keine eingetragenen Unternehmen</h3>';
        }
      
        /* Restore original Post Data*/
        wp_reset_postdata();
    
    }

    echo $string;
}
