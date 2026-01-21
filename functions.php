<?php
function storely_child_enqueue_styles() {
    wp_enqueue_style('storely-parent-style', get_template_directory_uri() . '/style.css');
//     wp_enqueue_style('storely-child-style', get_stylesheet_uri(), array('storely-parent-style'));
	
	 wp_enqueue_style('child-style',
        get_stylesheet_uri() . '/style.css',
        ['storely-parent-stylestyle'], // Make sure it loads after parent
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'storely_child_enqueue_styles');