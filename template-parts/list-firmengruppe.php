<?php
// Add a shortcode
add_shortcode('liste_firmengruppe', 'generate_firmengruppe_list');

function generate_firmengruppe_list($atts = array()) {

  $fg_slug = $atts['fg_slug'];
  // find head post 
  $postId = ( $post = get_page_by_path( $fg_slug , OBJECT, 'unternehmen' ) ) ?  $post->ID : 0;

  // if there is no head post 
  if(!$postId){
    return "check again fg_slug";
  }
  else{
    return "here we are";
  }
  

  $arg = 
    array(
      'post_type' => 'unternehmen', 
      'posts_per_page' => -1);
  
  $unternehmen_query = new WP_Query( $arg );

  $unternehmen_list_html = '';
  while ( $unternehmen_query->have_posts() ) {
    $unternehmen_query->the_post();
    if(!has_post_parent()){
      $postId = get_the_ID();

      $firmengruppe             = get_post_meta($postId,  'firmengruppen', true);
      $firmengruppe_hierarchie  = get_post_meta($postId,  'firmengruppen-hierarchie', true);
      $firmengruppe_has_page    = get_post_meta($postId,  'is_fg_page', true);
      


      // Einzel Firmaeintrag
      if($firmengruppe == ""){
        $unternehmen_list_html .= generate_eintrag($postId);
      }else if($firmengruppe_hierarchie == 0 && $firmengruppe_has_page != 1) {
        $unternehmen_list_html .= generate_eintrag($postId, 'dropdown');
      }else if($firmengruppe_hierarchie == 0 && $firmengruppe_has_page == 1) {
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





