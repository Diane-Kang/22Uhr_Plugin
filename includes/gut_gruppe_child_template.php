<?php defined('ABSPATH') or die();




add_action( 'astra_single_header_after', 'before_content_gut_child');

function before_content_gut_child() {

    if ('g-u-t' == get_post(wp_get_post_parent_id( get_the_ID()))->post_name ) {
        $string = '<a href="/firmenverzeichnis/g-u-t-gruppe/">Zum Verzeichnis der G.U.T.-GRUPPE</a>';
    }

    echo $string;
}
