<?php
function storely_child_enqueue_styles() {
    wp_enqueue_style('storely-parent-style', get_template_directory_uri() . '/style.css');
	
	 wp_enqueue_style(
		'child-style',
        get_stylesheet_uri(),
        ['storely-parent-style'], // Carrega primeiro o estilo do tema pai, depois o tema filho
        filemtime(get_stylesheet_directory() . '/style.css')
    );

	if(is_front_page()) {
		wp_enqueue_style(
			'home-style',
			get_stylesheet_directory_uri() . '/home.css',
			['child-style'],
			filemtime(get_stylesheet_directory() . '/home.css')
		);
	}

	if(is_cart()) {
		wp_enqueue_style(
			'cart-page-style',
			get_stylesheet_directory_uri() . '/cart-page.css',
			['child-style'],
			filemtime(get_stylesheet_directory() . '/cart-page.css')
		);
	}

	if(is_account_page()) {
		wp_enqueue_style(
			'minha-conta-style',
			get_stylesheet_directory_uri() . '/minha-conta.css',
			['child-style'],
			filemtime(get_stylesheet_directory() . './minha-conta.css')
		);
	}

	if(is_shop()) {
		wp_enqueue_style(
			'loja-style',
			get_stylesheet_directory_uri() . '/loja.css',
			['child-style'],
			filemtime(get_stylesheet_directory() . '/loja.css')
		);
	}
}

function storely_child_enqueue_scripts() {
	if(is_shop()) {
		wp_enqueue_script(
			'card-product-js',
			get_stylesheet_directory_uri() . '/js/card-product.js',
			[],
			'1.0',
			true
		);
	}

	// Remove a caixa de seleção de categorias e a barra de pesquisa
	// quando o usuário está na página "Carrinho", ou em "Minha Conta"
	if(is_cart() || is_account_page() || is_checkout()) {
		remove_action('storely_hdr_browse_cat', 'storely_hdr_browse_cat');
		remove_action('storely_hdr_product_search', 'storely_hdr_product_search');
	} else {
		wp_enqueue_script(
			'traducao-js',
			get_stylesheet_directory_uri() . '/js/traducao.js',
			[],
			'1.0',
			true
		);
	}
}

add_action('wp_enqueue_scripts', 'storely_child_enqueue_styles', 20);
add_action('wp_enqueue_scripts', 'storely_child_enqueue_scripts');
