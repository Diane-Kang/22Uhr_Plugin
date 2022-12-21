<?php defined('ABSPATH') or die();




add_action( 'astra_single_header_after', 'before_content');
add_action( 'astra_entry_content_after', 'after_content', 12);


function before_content() {
if ( is_singular('unternehmen') ) {


$branche = get_the_terms( get_the_ID(), 'branche' );
if (! empty($branche)) {
foreach($branche as $tag) {
$list_branchen .= '<span>' . $tag->name . '</span>';
}
}

$abschaltung = get_the_terms( get_the_ID(), 'abschaltung' );



if (! empty($abschaltung)) {
foreach($abschaltung as $uhr) {
if ($uhr->name == 'Nicht vorhanden') $abschaltung_um_uhr .= "Wir <span class='orange'>verzichten</span> seit jeher
bewusst auf <span class='orange'>Werbebeleuchtung</span>. ";
else $abschaltung_um_uhr .= "Wir schalten unsere im Freien sichtbare <span class='orange'>Werbebeleuchtung</span> um
spätestens <span class='orange'>" . $uhr->name . " aus</span>. ";
}
}


$angepasst = get_post_meta(get_the_ID(), 'Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)', true);


if ($angepasst == 'j') $text .= "Dies haben wir im Zuge der Teilnahme an diesem Projekt herbeigeführt und wird auch
fortan so belassen.";
else $text .= "Dies war bislang schon so und wird im Zuge der Teilnahme an diesem Projekt auch fortan so belassen.";


$adresse = '<div class="adresse">
  <div class="strasse-hn">' . get_post_meta(get_the_ID(), 'Straße und Hausnummer', true) . '</div>
  <div class="plz-ort">(' . get_post_meta( get_the_ID(), 'Land', true ) . ')&nbsp;' . get_post_meta(get_the_ID(),
    'Postleitzahl', true)
    . '&nbsp;' . get_post_meta(get_the_ID(), 'Ort', true) . '</div>
  <div class="internet"><span>Bundesland: </span>' . get_post_meta(get_the_ID(), 'Bundesland', true) . '</div>
  <div class="branche"><span>Branche: </span>' . $list_branchen . '</div>
  <div class="internet"><a href="' . get_post_meta(get_the_ID(), 'Internet', true) . '" target="_blank"
      rel="noopener">Internetseite</a></div>
</div>
<div class="abschaltung-angepasst">
  <h3 class="abschaltung-um">' . $abschaltung_um_uhr . $text . '</h3>
</div>
<h2 class="dabei">Deswegen sind wir bei „22 Uhr – Licht aus“ dabei:</h2>';

echo $adresse;

}
}


//After content
function after_content(){
if ( is_singular('unternehmen') ) {

$after = '<div class="zertifikat top-border">
  <p class="beitrag-artenschutz">Durch die Abschaltung trägt die Firma ' . get_the_title() . ' zur Reduzierung der
    Lichtverschmutzung in ' . get_post_meta(get_the_ID(), 'Ort', true) . ' bei und leistet somit einen wertvollen
    Beitrag zum Artenschutz, Umweltschutz und Klimaschutz.</p>
  <a href="' . get_post_meta(get_the_ID(), 'PDF Pfad', true) .'">
    <img src="/wp-content/uploads/2022/05/Zertifikat-22-Uhr-Licht-aus-thumb-01.png" alt="Zertifikat 22 Uhr">
    <h2>Zertifikat (PDF)</h2>
  </a>
</div>
<div class="abschaltung top-border">
  <h2>Details zur Licht- bzw. Werbelicht-Abschaltung:</h2>
  <p>' . get_post_meta(get_the_ID(), 'Abschaltung', true) . '</p>
</div>
<div class="uber-uns top-border">
  <h2>Worum geht es bei „22 Uhr – Licht aus?“</h2>
  <p>Das Projekt "22 Uhr – Licht aus" dient der Reduzierung der Lichtverschmutzung. Teilnehmende Firmen haben sich
    freiwillig dazu bereiterklärt, nachts die gesamte im Freien sichtbare Werbebeleuchtung so früh wie möglich,
    spätestens jedoch um 22 Uhr abzuschalten. Oder aber es ist bislang keine derartige Beleuchtung installiert und die
    Teilnahme an diesem Projekt motiviert die betreffenden Firmen dazu, dies ganz bewusst auch in Zukunft so zu
    belassen. </p>
</div>
<a class="zurueck is_parent" href="/firmenverzeichnis/">zurück zum Verzeichnis</a>
<a class="zurueck is_child" href="/firmenverzeichnis/g-u-t-gruppe/">zurück zum Verzeichnis der G.U.T.-GRUPPE</a>';

echo $after;
}
}