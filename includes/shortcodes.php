<?php defined('ABSPATH') or die();


add_shortcode('show_debugging_shortcode', 'debugging_shortcode');

function show_unternehmen_nummer()
{

  $the_query = new WP_Query(array('post_type' => 'unternehmen', 'posts_per_page' => -1));
  $total = wp_count_posts('unternehmen')->publish;
  return $total;
}
add_shortcode('unternehmen_nummer', 'show_unternehmen_nummer');
