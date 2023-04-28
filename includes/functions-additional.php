<?php defined('ABSPATH') or die();


add_shortcode('show_debugging_shortcode', 'debugging_shortcode');

function show_unternehmen_nummer()
{

  $the_query = new WP_Query(array('post_type' => 'unternehmen', 'posts_per_page' => -1));
  $total = wp_count_posts('unternehmen')->publish;
  return $total;
}
add_shortcode('unternehmen_nummer', 'show_unternehmen_nummer');


class local_fonts
{
  function __construct()
  {
    add_action('wp_enqueue_scripts', array($this, 'fonts'));
  }
  function fonts()
  {
    // Generate correspond fonts.css by https://gwfh.mranftl.com/fonts
    wp_enqueue_style('fonts_css', plugins_url('22uhr-plugin/css/fonts.css'), array(), 1.0, false);
  }
}
new local_fonts();


function custom_post_order($query){
  $post_type = $query->get('post_type');
  /* Check post types. */
  if(in_array($post_type , ["unternehmen"])){
      /* Post Column: e.g. title */
      if($query->get('orderby') == 'menu_order title'){
          $query->set('orderby', 'date');
          $query->set('order', 'DESC');
      }

  }
}
if(is_admin()){
add_action('pre_get_posts', 'custom_post_order');
}
