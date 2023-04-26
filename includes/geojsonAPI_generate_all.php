<?php defined('ABSPATH') or die();

add_action('rest_api_init', 'geojson_generate_api');

function geojson_generate_api()
{
  register_rest_route('22uhr-plugin/v1', '/firmenverzeichnis/', array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'unternehmen_geojson_generator'
  ));
}

function unternehmen_geojson_generator()
{
  $unternehmen = new WP_Query(array(
    'post_type' => 'unternehmen'
  ));

  $unternehmen_geojson = array();

  while ($unternehmen->have_posts()) {
    $unternehmen->the_post();

    $longi = get_post_meta(get_the_ID(), $key = "2-Laengengrad", true);
    settype($longi, "float");

    $lati = get_post_meta(get_the_ID(), $key = "1-Breitengrad", true);
    settype($lati, "float");

    //variable type string
    $werbebeleuchtung_jn = get_post_meta(
      get_the_ID(),
      $key = "Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)",
      true
    );

    $abschaltung = get_the_terms(get_the_ID(), 'abschaltung');
    if (!empty($abschaltung)) {
      $uhrzeit = $abschaltung[0];
    } else $uhrzeit = 'nicht-vorhanden';

    array_push($unternehmen_geojson, array(
      'type' => 'Feature',
      'id' => get_the_ID(),
      'geometry' => array(
        'type' => 'Point',
        'coordinates' => array($longi, $lati)
      ),
      'properties' => array(
        'name' => get_the_title(),
        'post_id' => get_the_ID(),
        'url' => get_post_meta(get_the_ID(), 'firmengruppen-hierarchie', true) == '0' ? get_post_meta(get_the_ID(), 'firmengruppen-seite', true) : get_permalink()
      ),
      'filter' => array(
        'werbebeleuchtung' => $werbebeleuchtung_jn,
        'abschaltung' => $uhrzeit
      ),
      'firmengruppen' => get_post_meta(get_the_ID(), 'firmengruppen', true),
      'firmengruppen_hierarchie' => get_post_meta(get_the_ID(), 'firmengruppen-hierarchie', true),
    ));
  }

  $wrapper_array = array(
    "type" => "FeatureCollection",
    "features" => $unternehmen_geojson
  );

  return $wrapper_array;
}
