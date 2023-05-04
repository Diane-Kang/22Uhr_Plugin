<?php
// Add a shortcode
add_shortcode('liste_firmengruppe', 'generate_firmengruppe_list');

function generate_firmengruppe_list($atts = array())
{

  $fg_slug = $atts['fg_slug'];
  // find head post 
  $postId = ($post = get_page_by_path($fg_slug, OBJECT, 'unternehmen')) ?  $post->ID : 0;

  // if there is no head post 
  if (!$postId) {
    return "check again fg_slug";
  }

  $arg =
    array(
      'post_type' => 'unternehmen',
      'posts_per_page' => -1,
      'post_parent' => $postId,
    );

  $unternehmen_query = new WP_Query($arg);
  $number_of_fg = $unternehmen_query->found_posts;
  $unternehmen_list_html = '';
  while ($unternehmen_query->have_posts()) {
    $unternehmen_query->the_post();
    $postId = get_the_ID();

    $unternehmen_list_html .= generate_eintrag($postId);
  }
  wp_reset_query();
  $unternehmenListe = '<div class="unternehmen">' . $unternehmen_list_html . '</div>';

  $abschaltung_filter = generate_abschlatung_filter($fg_slug);

  $abschaltung_message = '<div class="abschaltung_message"><div class="hover-icon">&#xf005</div>Werbelicht im Zuge der Teilnahme optimiert</div>';
  $toogle = list_toogle();
  $back_to_all = '
    <div class="zurueck_alle">
      <a href="/firmenverzeichnis/"> Zurück zum Hauptverzeichnis </a> 
    </div>';
  $title_text = "<h1>Verzeichnis der " .  get_the_title() . ":</h1>";
  $page_description = '<div class="page-description number">' . $number_of_fg . ' unserer Standorte sind dabei</div>';
  return
    $back_to_all .
    $title_text .
    $page_description .
    $abschaltung_filter .
    $abschaltung_message .
    $unternehmenListe .
    $toogle;
}


function list_toogle()
{
  ob_start();
?>
  <div id="change" class="firmen-toggle">
    <div class="firmen-hide">Firmenliste ausblenden</div>
    <div class="firmen-show">Firmenliste eiblenden</div>
  </div>
<?php
  return ob_get_clean();
}
