<?php defined('ABSPATH') or die();


function debugging_shortcode() {
  // global $post;
  // var_dump($post->post_parent);
  global $post;
  var_dump(basename(get_permalink($post->post_parent)));
  var_dump($post->post_parent);
  return '';
}


add_shortcode('show_debugging_shortcode', 'debugging_shortcode');

function show_unternehmen_nummer() {
  
  $the_query = new WP_Query( array( 'post_type' => 'unternehmen', 'posts_per_page' => -1 ) );
  $total = wp_count_posts('unternehmen')->publish;
  return $total;
}
add_shortcode('unternehmen_nummer', 'show_unternehmen_nummer');



