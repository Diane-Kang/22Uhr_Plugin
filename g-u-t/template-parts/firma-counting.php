<?php




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