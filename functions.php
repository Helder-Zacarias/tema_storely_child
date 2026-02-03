<?php
function storely_child_enqueue_styles() {
    wp_enqueue_style('storely-parent-style', get_template_directory_uri() . '/style.css');
	
	 wp_enqueue_style(
		'child-style',
        get_stylesheet_uri(),
        ['storely-parent-style'], // Carrega primeiro o estilo do tema pai, depois o tema filho
        filemtime(get_stylesheet_directory() . '/style.css')
    );

	checkCurrentPage('front_page', 'home');
	checkCurrentPage('cart', 'cart-page');
	checkCurrentPage('account_page', 'minha-conta');
	checkCurrentPage('shop', 'loja');
	checkCurrentPage('product', 'produto');
}

// Caheca página dinamicamente, retirando 'if's desenecessários do código
function checkCurrentPage($page, $cssFile) {
	$function = 'is_' . $page;

	if(function_exists($function) && $function()) {
		wp_enqueue_style(
			$cssFile . '-style',
			get_stylesheet_directory_uri() . '/' . $cssFile . '.css',
			['child-style'],
			filemtime(get_stylesheet_directory() . '/' . $cssFile . '.css')
		);
	}
}

function storely_child_enqueue_scripts() {
	wp_enqueue_script(
		'traducao-modal-carrinho-js',
		get_stylesheet_directory_uri() . '/js/traducao-modal-carrinho.js',
		[],
		'1.0',
		true
	);
	

	if(is_cart()) {
		wp_enqueue_script(
			'traducao-resumo-carrinho-js',
			get_stylesheet_directory_uri() . '/js/traducao-resumo-carrinho.js',
			[],
			'1.0',
			true
		);
	}

	if(is_shop() || is_product()) {
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
