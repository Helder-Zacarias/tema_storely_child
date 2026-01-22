<?php
function storely_child_enqueue_styles() {
    wp_enqueue_style('storely-parent-style', get_template_directory_uri() . '/style.css');
	
	 wp_enqueue_style(
		'child-style',
        get_stylesheet_uri(),
        ['storely-parent-stylestyle'], // Carrega primeiro o estilo do tema pai, depois o tema filho
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'storely_child_enqueue_styles', 20);