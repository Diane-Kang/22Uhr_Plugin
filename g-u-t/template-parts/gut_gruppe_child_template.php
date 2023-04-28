<?php defined('ABSPATH') or die();


// Add specific CSS class by filter.

add_filter( 'body_class', 'is_child'); 

function is_child( $classes ) {
    if ('g-u-t' == get_post(wp_get_post_parent_id( get_the_ID()))->post_name ) {
	    return array_merge( $classes, array( 'is_child' ) );
    }
    else {
        return array_merge( $classes, array( 'is_parent' ) );
    }
}




// // add lin to firmengruppen map

add_action( 'astra_single_header_after', 'before_content_gut_child', 9);

function before_content_gut_child() {

    if ('g-u-t' == get_post(wp_get_post_parent_id( get_the_ID()))->post_name ) {
        $string = '<div class="zurueck top"><a href="/firmenverzeichnis/g-u-t-gruppe/">Zum Verzeichnis der G.U.T.-GRUPPE</a></div>';
    }

    echo $string;
}
