<?php
function storely_child_enqueue_styles() {
    wp_enqueue_style('storely-parent-style', get_template_directory_uri() . '/style.css');
	
	 wp_enqueue_style(
		'child-style',
        get_stylesheet_uri(),
        ['storely-parent-style'], // Carrega primeiro o estilo do tema pai, depois o tema filho
        filemtime(get_stylesheet_directory() . '/style.css')
    );

	addStyleToPage('front_page', 'home');
	addStyleToPage('cart', 'cart-page');
	addStyleToPage('account_page', 'minha-conta');
	addStyleToPage('shop', 'loja');
	addStyleToPage('product', 'produto');
}

// Carrega página dinamicamente, retirando 'if's desnecessários do código
function addStyleToPage($page, $cssFile) {
	if(checkCurrentPage($page)) {
		wp_enqueue_style(
			$cssFile . '-style',
			get_stylesheet_directory_uri() . '/' . $cssFile . '.css',
			['child-style'],
			filemtime(get_stylesheet_directory() . '/' . $cssFile . '.css')
		);
	}
}

function checkCurrentPage($page) {
	$function = 'is_' . $page;
	return function_exists($function) && $function();
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
add_action('get_header', 'add_top_offer_banner', 5);

function add_top_offer_banner() {
	if(is_front_page()) {
		?>
			<div class="top-offer-banner">
				🔥 Ofertas por tempo limitado! Frete grátis acima de R$199
			</div>
		<?php
	}
}
