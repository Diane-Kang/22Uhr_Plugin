<?php defined('ABSPATH') or die();


// create custom post type unternehmen
function create_posttype() {

  register_post_type( 'unternehmen',
      // CPT Options
      array(
          'labels' => array(
              'name' => __( 'Unternehmen' ),
              'singular_name' => __( 'Unternehmen' )
          ),
          'supports' => array('custom-fields', 'thumbnail', 'excerpt', 'revisions', 'editor', 'title', 'author','page-attributes'),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'unternehmen'),
          'show_in_rest' => true,
          'hierarchical' => true,
          'publicly_queryable' => true,
          'taxonomies' =>  array( 'branche' ),
      )
  );
}

add_action( 'init', 'create_posttype' );

//create a custom taxonomy unternehmenskategorie

function create_custom_taxonomy_branche() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

  $labels = array(
      'name' => _x( 'Branche', 'taxonomy general name' ),
      'singular_name' => _x( 'Branchen', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Branche' ),
      'all_items' => __( 'Alle Branchen' ),
      'parent_item' => __( 'Parent Branche' ),
      'parent_item_colon' => __( 'Parent Branche:' ),
      'edit_item' => __( 'Edit Branche' ),
      'update_item' => __( 'Update Branche' ),
      'add_new_item' => __( 'Add New Branche' ),
      'new_item_name' => __( 'New Branche Name' ),
      'menu_name' => __( 'Branche' ),
  );

// Now register the taxonomy
  register_taxonomy('branche',array('unternehmen'), array(
      'hierarchical' => true,
      'labels' => $labels,
      'show_ui' => true,
      'public' => true,
      'show_in_rest' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'branche' ),
  ));

}

//hook into the init action and call create_xy_taxonomies when it fires

add_action( 'init', 'create_custom_taxonomy_branche', 0 );


function create_custom_taxonomy_schlagwort() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

  $labels = array(
      'name' => _x( 'Schlagwort', 'taxonomy general name' ),
      'singular_name' => _x( 'Schlagwort', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Schlagwort' ),
      'all_items' => __( 'Alle Schlagwort' ),
      'parent_item' => __( 'Parent Schlagwort' ),
      'parent_item_colon' => __( 'Parent Schlagwort:' ),
      'edit_item' => __( 'Edit Schlagwort' ),
      'update_item' => __( 'Update Schlagwort' ),
      'add_new_item' => __( 'Add New Schlagwort' ),
      'new_item_name' => __( 'New Schlagwort Name' ),
      'menu_name' => __( 'Schlagwort' ),
  );

// Now register the taxonomy
  register_taxonomy('schlagwort',array('unternehmen'), array(
      'labels' => $labels,
      'show_ui' => true,
      'show_in_rest' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'schlagwort' ),
  ));

}

//hook into the init action and call create_xy_taxonomies when it fires

add_action( 'init', 'create_custom_taxonomy_schlagwort', 0 );


// New Taxonomie Abschaltung

function create_custom_taxonomy_abschaltung() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

  $labels = array(
      'name' => _x( 'Abschaltung', 'taxonomy general name' ),
      'singular_name' => _x( 'Abschaltung', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Abschaltung' ),
      'all_items' => __( 'Alle Abschaltung' ),
      'parent_item' => __( 'Parent Abschaltung' ),
      'parent_item_colon' => __( 'Parent Abschaltung:' ),
      'edit_item' => __( 'Edit Abschaltung' ),
      'update_item' => __( 'Update Abschaltung' ),
      'add_new_item' => __( 'Add New Abschaltung' ),
      'new_item_name' => __( 'New Abschaltung Name' ),
      'menu_name' => __( 'Abschaltung' ),
  );

// Now register the taxonomy
  register_taxonomy('abschaltung',array('unternehmen'), array(
      'labels' => $labels,
      'show_ui' => true,
      'show_in_rest' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'abschaltung' ),
  ));

}

//hook into the init action and call create_xy_taxonomies when it fires

add_action( 'init', 'create_custom_taxonomy_abschaltung', 0 );