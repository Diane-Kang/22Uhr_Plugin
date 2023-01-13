<?php defined('ABSPATH') or die();


function debugging_shortcode() {
  global $post;
  var_dump($post->post_parent);
  var_dump('g-u-t' == basename(get_permalink($post->post_parent)));
  return '';
}

add_shortcode('show_debugging_shortcode', 'debugging_shortcode');


function show_unternehmen_nummer() {

  $the_query = new WP_Query( array( 'post_type' => 'unternehmen', 'posts_per_page' => -1 ) );
  $total = wp_count_posts('unternehmen')->publish;
  return $total;
}

add_shortcode('unternehmen_nummer', 'show_unternehmen_nummer');

function show_child_unternehmen_nummer($atts) {

  $arg = array( 
    'post_type' => 'unternehmen', 
    'posts_per_page' => -1,
    'meta_query' => array(
      'relation' => 'and',
      array(
          'key'       => 'firmengruppen',
          'value'        => $atts['firmenname'],
          'compare' => '='
      ),
    ),
  );

  $the_query = new WP_Query($arg);

  $total = $the_query ->post_count;
  
  return $total;
  }
  add_shortcode('child_unternehmen_nummer', 'show_child_unternehmen_nummer');