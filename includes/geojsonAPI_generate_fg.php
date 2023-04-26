<?php defined('ABSPATH') or die();

////////////// Register own Endpoint for API - /wp-json/22uhr-plugin/v1/{unternehmengruppe}
class geojsonAPI_generate_fg
{
  public $endpoint;

  function __construct($fg_slug)
  {
    $this->endpoint = $fg_slug;
    add_action('rest_api_init', array($this, 'geojson_generate_api'));
  }
  function geojson_generate_api()
  {
    $endpoint_url = '/' . $this->endpoint . '/';
    register_rest_route('22uhr-plugin/v1', $endpoint_url, array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => array($this, 'unternehmen_geojson_generator')
    ));
  }

  function unternehmen_geojson_generator()
  {

    $arg = array(
      'post_type'       => 'unternehmen',
      'posts_per_page'  => -1,
      'post_parent'     => get_page_by_path($this->endpoint, OBJECT, array('unternehmen'))->ID,
    );

    $unternehmen = new WP_Query($arg);

    $unternehmen_geojson = array();

    while ($unternehmen->have_posts()) {
      $unternehmen->the_post();

      // longitude, latitude 
      $longi = get_post_meta(get_the_ID(), $key = "2-Laengengrad", true);
      settype($longi, "float");

      $lati = get_post_meta(get_the_ID(), $key = "1-Breitengrad", true);
      settype($lati, "float");

      //variable type string
      $werbebeleuchtung_jn = get_post_meta(get_the_ID(), $key = "Werbebeleuchtung wurde im Projektrahmen angepasst (j/n)", true);

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
          'url' => get_permalink()
        ),
        'filter' => array(
          'werbebeleuchtung' => $werbebeleuchtung_jn,
          'abschaltung' => $uhrzeit
        ),
        'firmengruppen' => get_post_meta(get_the_ID(), 'firmengruppen', true),
        'firmengruppen_hierarchie' => get_post_meta(get_the_ID(), 'firmengruppen-hierarchie', true)
      ));
    }
    $wrapper_array = array(
      "type" => "FeatureCollection",
      "test" => get_page_by_path($this->endpoint, OBJECT, array('unternehmen'))->ID,
      "features" => $unternehmen_geojson
    );

    return $wrapper_array;
  }
}
