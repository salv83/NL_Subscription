<?php
add_action( 'wp_enqueue_scripts', 'storefront_child_enqueue_styles' );

function storefront_child_enqueue_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

/* Add the webpack script  - bundle.js - */
add_action('wp_enqueue_scripts', 'storefront_child_scripts');

function storefront_child_scripts() {
    wp_enqueue_script( 'theme_js', get_stylesheet_directory_uri() . '/public/js/bundle.js', array('jquery'), '', true );
}
