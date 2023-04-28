<?php
// Add a shortcode
add_shortcode('liste_unternehmen', 'generate_unternehmen_list');

function generate_unternehmen_list() {
   
  $arg = 
    array(
      'post_type' => 'unternehmen', 
      'posts_per_page' => -1);
  
  $unternehmen_query = new WP_Query( $arg );

  $unternehmen_list_html = '';
  while ( $unternehmen_query->have_posts() ) {
    $unternehmen_query->the_post();
    $postId = get_the_ID();
    
    if(!has_post_parent()){
      $firmengruppe             = get_post_meta($postId,  'firmengruppen', true);
      $firmengruppe_hierarchie  = get_post_meta($postId,  'firmengruppen-hierarchie', true);
      $firmengruppe_has_page    = get_post_meta($postId,  'is_fg_page', true);
      
      $args = array(
        'post_parent' => $postId,
        'post_type'   => 'unternehmen', // Current post's ID
      );
      $children = get_children( $args );

      // Einzel Firmaeintrag : NO CHILD
      if(!$children){
        $unternehmen_list_html .= generate_eintrag($postId);
      // }else if($firmengruppe_hierarchie == 0 && $firmengruppe_has_page != 1) {
      }else if($firmengruppe_has_page == 0) {
        $unternehmen_list_html .= generate_eintrag($postId, 'dropdown');
      // }else if($firmengruppe_hierarchie == 0 && $firmengruppe_has_page == 1) {
      }else if($firmengruppe_has_page == 1) {
        if($firmengruppe == 'G.U.T.'){
          $unternehmen_list_html .= generate_eintrag($postId, 'fg_page_gut');
        }else{
          $unternehmen_list_html .= generate_eintrag($postId, 'fg_page');
        }
      }
      $unternehmenListe = '<div class="unternehmen">' . $unternehmen_list_html .'</div>';
      $abschaltung_filter = generate_abschlatung_filter();
    }
  }
  $abschaltung_message = '<div class="abschaltung_message"><div class="hover-icon">&#xf005</div>Werbelicht im Zuge der Teilnahme optimiert</div>';
  return  $abschaltung_filter . $abschaltung_message . $unternehmenListe;
}





