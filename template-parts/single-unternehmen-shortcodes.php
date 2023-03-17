<?php defined('ABSPATH') or die();

add_action( 'astra_single_header_after', 'single_unternehmen_before_content');
add_action( 'astra_entry_content_after', 'after_content', 12);


function single_unternehmen_before_content() {
  if ( is_singular('unternehmen') ) {
    $abschaltung  = get_the_terms( get_the_ID(), 'abschaltung', true);
    $uhr = $abschaltung[0];
    $angepasst    = get_post_meta(get_the_ID(), 'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true);
    $branche = get_the_terms( get_the_ID(), 'branche' );
    $list_branchen = "";
    if (! empty($branche)) {
      foreach($branche as $tag) {
        $list_branchen .= '<span>' . $tag->name . '</span>';
        }
    }

    $zitat_text = '';
    if($uhr->name == 'Nicht vorhanden'){
      $zitat_text ='Wir <span class="orange">verzichten</span> schon seit jeher ganz gezielt auf <span class="orange">Werbebeleuchtung</span>.<br />
      Mit dem Wissen um die Problematik der Lichtverschmutzung werden wir dies im Zuge der Projektteilnahme nun ganz bewusst so beibehalten.';
    }
    else if($angepasst == n && $uhr->name != 'Nicht vorhanden'){
      $zitat_text = 'Wir schalten unsere im Freien sichtbare <span class="orange">Werbebeleuchtung seit jeher schon um '. $uhr->name .' aus</span>.<br /> 
      Mit dem Wissen um die Problematik der Lichtverschmutzung werden wir dies im Zuge der Projektteilnahme nun ganz bewusst so beibehalten.';
    } 
    else if ($angepasst == j && $uhr->name != 'Nicht vorhanden'){
      $zitat_text = 'Wir schalten unsere im Freien sichtbare <span class="orange">Werbebeleuchtung nun schon um '. $uhr->name .' aus</span>.<br /> 
      Dies haben wir im Zuge der Teilnahme an diesem Projekt herbeigeführt. 
      Mit dem Wissen um die Problematik der Lichtverschmutzung werden wir dies nun ganz bewusst so beibehalten.';
    } 

    

    $adresse = 
    '<div class="adresse grid-adress">
      <div class="strasse-hn"> 
        (' . get_post_meta( get_the_ID(), 'Land', true ) . ') 
        <span class="plz-ort">'.get_post_meta(get_the_ID(),'Postleitzahl', true).'</span>
        ' . get_post_meta(get_the_ID(), 'Ort', true) . ', 
        '. get_post_meta(get_the_ID(), 'Straße und Hausnummer', true) .'
      </div>
      <div>'. get_post_meta(get_the_ID(), 'Bundesland', true).'</div>
      <div class="branche"><span>Branche: </span>' . $list_branchen . '</div>
      <div class="internet"><a href="' . get_post_meta(get_the_ID(), 'Internet', true) . '" target="_blank" rel="noopener">Internetseite</a></div>
    </div>';

    
    $null_content = (get_the_content() == "")? "null_content": "" ;

    $header_unternehmen = 
    '<div class="header_22 unternehmen_header_grid ' .$null_content. '">
        <div class="post-thumb grid-logo">'
          . get_the_post_thumbnail(get_the_ID()) .
        '</div>
        <div class="abschaltung-angepasst grid-zitat">
          <h3 class="abschaltung-um">' . $zitat_text . '</h3>
        </div>
      <div class="entry_title grid-title"><h1>' . get_the_title(get_the_ID()) . '</h1></div>
      <div class="parent_unternehmen grid-parent-info">Ein Unternehmen der <a class="zurueck is_child top" href="/firmenverzeichnis/g-u-t-gruppe/">G.U.T.-GRUPPE</a>
      </div>' . 
      $adresse .
    ' <h2 class="dabei">Deswegen sind wir bei „22 Uhr – Licht aus“ dabei:</h2>
    </div>';

    echo $header_unternehmen;

  }
}



//After content
function after_content(){

  // check if Details zur Lichtabschaltung is empty
$detail_zum_licht_text = get_post_meta(get_the_ID(), 'Details zum Licht', true);

  if (empty($detail_zum_licht_text)) {
    $detail_zum_licht_content = '<!-- No Details -->';
  }

  else {
    $detail_zum_licht_content =
'<div class="abschaltung top-border">
<h2>Infos/Details zur Werbelicht-Abschaltung bzw. sonstigen Außenbeleuchtung:</h2>
  <p>' . $detail_zum_licht_text . '</p>
</div>
</div>';
  }

if ( is_singular('unternehmen') ) {

$after = '<div class="zertifikat top-border">
  <p class="beitrag-artenschutz">Dieser verantwortungsvolle Umgang mit der Werbebeleuchtung trägt zur Reduzierung der Lichtverschmutzung in ' . get_post_meta(get_the_ID(), 'Ort', true) . ' bei. Dadurch wird ein wertvoller Beitrag zum Artenschutz, Umweltschutz und Klimaschutz geleistet.</p>
  <a target="_blank" href="' . get_post_meta(get_the_ID(), 'PDF Pfad', true) .'">
    <img src="/wp-content/uploads/2022/05/Zertifikat-22-Uhr-Licht-aus-thumb-01.png" alt="Zertifikat 22 Uhr">
    <h2>Zertifikat (PDF)</h2>
  </a>
</div>' .
$detail_zum_licht_content .
'<div class="uber-uns top-border">
  <h2>Worum geht es bei „22 Uhr – Licht aus?“</h2>
  <p>Das Projekt "22 Uhr – Licht aus" dient der Reduzierung der Lichtverschmutzung. Teilnehmende Firmen haben sich
    freiwillig dazu bereiterklärt, nachts die gesamte im Freien sichtbare Werbebeleuchtung so früh wie möglich,
    spätestens jedoch um 22 Uhr abzuschalten. Oder aber es ist bislang keine derartige Beleuchtung installiert und die
    Teilnahme an diesem Projekt motiviert die betreffenden Firmen dazu, dies ganz bewusst auch in Zukunft so zu
    belassen. </p>
</div>
<a class="zurueck is_parent" href="/firmenverzeichnis/">zum Verzeichnis</a>
<a class="zurueck is_child" href="/firmenverzeichnis/g-u-t-gruppe/">zum Verzeichnis der G.U.T.-GRUPPE</a>';

echo $after;
}
}