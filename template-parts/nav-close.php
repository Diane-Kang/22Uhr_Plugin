<?php

///////////// Main map Seite component ///////////////////
function nav_close_p()
{
  $target_page_name = 'firmenverzeichnis';
  global $post;

  if (is_page($target_page_name) || $post->post_parent == url_to_postid(site_url('firmenverzeichnis'))) {
?>
    <span class="navicon-close">Close</span>
<?php
  }
};

add_action('wp_head', 'nav_close_p');