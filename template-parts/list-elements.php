<?php
function generate_eintrag($postId, $type = 'basic')
{

  // extra configuration for filter 
  $abschaltung_tag = get_the_terms($postId, 'abschaltung', true)[0];
  if ($abschaltung_tag == null) {
    print_r(get_the_title($postId) . "\t<br>");
  }
  if ($abschaltung_tag->slug == null) {
    print_r(get_the_title($postId) . "\t<br>");
  }

  $abschaltung_underline = str_replace("-", "_", $abschaltung_tag->slug);
  $abschaltung_name = $abschaltung_tag->name;
  $abschaltung_num = is_numeric(str_replace(" Uhr", "", $abschaltung_name)) ? str_replace(" Uhr", "", $abschaltung_name) : "";
  switch ($abschaltung_underline) {
    case 'nicht_vorhanden';
      $abschaltung_text = "Seit jeher kein Werbelicht vorhanden";
      break;
    case 'sonderfall';
      $abschaltung_text = "Werbelicht-Abschaltung: <span>Sonderfall</span>";
      break;
    default;
      $abschaltung_text = "Werbelicht-Abschaltung: " . $abschaltung_num . " Uhr";
      break;
  }


  $unternehme = array(
    // j oder n
    'werbebeleuchtung'        => 'werbebeleuchtung_' . get_post_meta($postId, 'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true),
    'abschaltung_data_group'  => 'abschaltung_' . $abschaltung_underline,
    'abschaltung_value'       => $abschaltung_num,
    'abschaltung_text'        => $abschaltung_text,
    'permalink'               => get_the_permalink(),
    'thumbnail'               => get_the_post_thumbnail(),
    'title'                   => get_the_title(),
    'adresse-land'            => get_post_meta($postId, 'Land', true),
    'adresse-postzahl'        => get_post_meta($postId, 'Postleitzahl', true),
    'adresse-ort'             => get_post_meta($postId, 'Ort', true),
    'id'                      => $postId,
    'fg_f_abschaltungszeit'   => get_post_meta($postId, 'fg_fruehste_abschaltungszeit', true),
    'fg_s_abschaltungszeit'   => get_post_meta($postId, 'fg_spaeteste_abschaltungszeit', true),
    'has_fg_sternchen'        => 'werbebeleuchtung_' . (get_post_meta($postId, 'has_fg_sternchen', true) ? 'j' : 'n'),
    'fg_adresse_land'         => get_post_meta($postId, 'fg_adresse-land', true),
    'fg_adresse_postzahl'     => get_post_meta($postId, 'fg_adresse-postzahl', true),
    'fg_adresse_ort'          => get_post_meta($postId, 'fg_adresse-ort', true),
  );
  $string = "";
  switch ($type) {
    case 'basic':
      $string = eintrag_basic($unternehme);
      break;
    case 'dropdown':
      $string = eintrag_dropdown($unternehme);
      break;
    case 'dropdown-child':
      $string = eintrag_dropdown_child($unternehme);
      break;
    case 'fg_page':
      $string = eintrag_fg_Page($unternehme);
      break;
    case 'fg_page_gut':
      $string = eintrag_fg_Page_gut($unternehme);
      break;
  }

  return $string;
}

function eintrag_basic($unternehme)
{
  $string =
    '<div class="unternehmenseintrag ' . $unternehme["werbebeleuchtung"] . ' ' . $unternehme["abschaltung_data_group"] . '" value=' . $unternehme["abschaltung_value"] . '>
      <div class="logo-wrapper">
        <a href="' . $unternehme["permalink"] . '">' . $unternehme["thumbnail"] . '</a>
      </div>
      <div class="text">
        <h3><a href="' . $unternehme["permalink"] . '">' . $unternehme["title"] . '</a></h3>
        <div class="adresse">(' . $unternehme["adresse-land"] . ')&nbsp;' . $unternehme["adresse-postzahl"] . ' ' . $unternehme["adresse-ort"] . '</div>
        <div class="map_link_point" id="map_id_' . $unternehme["id"] . '">Auf Karte zeigen </div>
        <div class="abschaltung_zeit">
          <div class="hover-wrapper">
            ' . $unternehme["abschaltung_text"] . '
            <div class="hover-icon">&#xf005</div>
            <div class="hover-text">Werbelicht im Zuge der Teilnahme optimiert</div>
          </div>
        </div>
      </div>
    </div>';
  return $string;
}

function eintrag_dropdown($unternehme)
{
  $args = array(
    'post_type' => 'unternehmen',
    'post_parent' => $unternehme["id"],
    'posts_per_page' => -1,
  );
  $child_query = new WP_Query($args);

  $string =
    ' <div class="unternehmenseintrag unternehmenseintrag--dropdown ' . $unternehme["has_fg_sternchen"] . ' ' . $unternehme["abschaltung_data_group"] . '" value=' . $unternehme['fg_f_abschaltungszeit'] . '>
        <div class="icon-click-area">
          <svg class="ionicon-chevron-down" viewBox="0 0 512 512">
            <title>Chevron Down</title>
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M112 184l144 144 144-144"></path>
          </svg>
        </div>
        <div class="logo-wrapper">
          ' . $unternehme["thumbnail"] . '
        </div>
        <div class="text">
          <div class="firmengruppe_num">Firmen-Gruppe mit ' . $child_query->post_count . ' Standorten</div>
          <h3>' . $unternehme["title"] . '</h3>
          <div class="adresse">(' . $unternehme["fg_adresse_land"] . ')&nbsp;' . $unternehme["fg_adresse_postzahl"] . ' ' . $unternehme["fg_adresse_ort"] . '</div>
          <div class="abschaltung_zeit">
            <div class="hover-wrapper">
              Werbelicht-Abschaltung: Bis spätestens ' . $unternehme['fg_s_abschaltungszeit'] . ' Uhr
              <div class="hover-icon">&#xf005</div>
              <div class="hover-text">Werbelicht im Zuge der Teilnahme optimiert</div>
            </div>
          </div>
          <div class="alle">Alle Standorte zeigen</div>
         </div>
      </div>';

  $string .= '<div class="child-unternehmen-block">';
  while ($child_query->have_posts()) {
    $child_query->the_post();
    $postId = get_the_ID();
    $string .= generate_eintrag($postId, 'dropdown-child');
  }
  $string .= '</div>';
  return '<div class="dropdown-wrapper">' . $string . '</div>';
}

function eintrag_dropdown_child($unternehme)
{
  $string =
    '<div class="unternehmenseintrag ' . $unternehme["werbebeleuchtung"] . ' ' . $unternehme["abschaltung_data_group"] . '" value=' . $unternehme["abschaltung_value"] . '>
      <div class="text">
        <h3><a href="' . $unternehme["permalink"] . '">' . $unternehme["title"] . '</a></h3>
        <div class="adresse">(' . $unternehme["adresse-land"] . ')&nbsp;' . $unternehme["adresse-postzahl"] . ' ' . $unternehme["adresse-ort"] . '</div>
        <div class="map_link_point" id="map_id_' . $unternehme["id"] . '">Auf Karte zeigen </div>
        <div class="abschaltung_zeit">
          <div class="hover-wrapper">
            ' . $unternehme["abschaltung_text"] . '
            <div class="hover-icon">&#xf005</div>
            <div class="hover-text">Werbelicht im Zuge der Teilnahme optimiert</div>
          </div>
        </div>
      </div>
    </div>';
  return $string;
}

function eintrag_fg_Page_gut($unternehme)
{
  $string =
    '<div class="unternehmenseintrag firmengruppen ' . $unternehme["werbebeleuchtung"] . ' ' . $unternehme["abschaltung_data_group"] . '" value=' . $unternehme["abschaltung_value"] . '>
      <div class="logo-wrapper">
        <a href="' . $unternehme["permalink"] . '">' . $unternehme["thumbnail"] . '</a>
      </div>
      <div class="text">
        <h3><a href="/firmenverzeichnis/' . get_post_meta($unternehme["id"], "firmengruppen-seite", true) . '">' . $unternehme["title"] . '</a></h3>
        <div class="adresse">(' . $unternehme["adresse-land"] . ')&nbsp;' . $unternehme["adresse-postzahl"] . ' ' . $unternehme["adresse-ort"] . '</div>
        <div class="alle"> <a href="/firmenverzeichnis/' . get_post_meta($unternehme["id"], "firmengruppen-seite", true) . '"><div> Alle ' . show_child_unternehmen_nummer(array('firmenname' => "G.U.T.")) . ' Standorte mit Abschaltzeit anzeigen<i class="fas fa-external-link-alt"></i></div></a> </div>
        <div class="abschaltung_zeit">
          <div class="hover-wrapper">
            Werbelicht-Abschaltung aller Standorte: Bis spätestens 21 Uhr
            <div class="hover-icon">&#xf005</div>
            <div class="hover-text">Werbelicht im Zuge der Teilnahme optimiert</div>
          </div>
        </div>
      </div>
    </div>';
  return $string;
}


function eintrag_fg_Page($unternehme)
{
  $args = array(
    'post_type' => 'unternehmen',
    'post_parent' => $unternehme["id"],
    'posts_per_page' => -1,
  );
  $child_query = new WP_Query($args);
  $string =
    ' <div class="unternehmenseintrag unternehmenseintrag--fg-Page ' . $unternehme["has_fg_sternchen"] . ' ' . $unternehme["abschaltung_data_group"] . '" value=' . $unternehme['fg_f_abschaltungszeit'] . '>
        <div class="logo-wrapper">
          <a href="/firmenverzeichnis/' . get_post_meta($unternehme["id"], "firmengruppen-seite", true) . '">' . $unternehme["thumbnail"] . '</a>
        </div>
        <div class="text">
          <div class="firmengruppe_num">Firmen-Gruppe mit ' . $child_query->post_count . ' Standorten</div>
          <h3><a href="/firmenverzeichnis/' . get_post_meta($unternehme["id"], "firmengruppen-seite", true) . '">' . $unternehme["title"] . '</a></h3>
          <div class="adresse">(' . $unternehme["fg_adresse_land"] . ')&nbsp;' . $unternehme["fg_adresse_postzahl"] . ' ' . $unternehme["fg_adresse_ort"] . '</div>
          <div class="alle"> 
            <a href="/firmenverzeichnis/' . get_post_meta($unternehme["id"], "firmengruppen-seite", true) . '">
              <div> 
                <i class="fas fa-solid fa-map"></i>
                Alle Standorte auf Karte anzeigen
                <i class="fas fa-external-link-alt"></i>
              </div>
            </a>
          </div>
          <div class="abschaltung_zeit">
            <div class="hover-wrapper">
              Werbelicht-Abschaltung: Bis spätestens ' . $unternehme['fg_s_abschaltungszeit'] . ' Uhr
              <div class="hover-icon">&#xf005</div>
              <div class="hover-text">Werbelicht im Zuge der Teilnahme optimiert</div>
            </div>
          </div>
         </div>
      </div>';
  return $string;
}
