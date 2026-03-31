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
	addStyleToPage('checkout', 'checkout');
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

		wp_enqueue_script(
			'adicionar-icone-carrinho-js',
			get_stylesheet_directory_uri() . '/js/adicionar-icone-carrinho.js',
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

	if(is_account_page()) {
		wp_enqueue_script(
			'traducao-wishlist-js',
			get_stylesheet_directory_uri() . '/js/traducao-wishlist.js',
			[],
			'1.0',
			true
		);
	}
}

function add_top_offer_banner() {
	if(is_front_page()) {
		?>
			<div class="top-offer-banner">
				🔥 Ofertas por tempo limitado! Frete grátis acima de R$199
			</div>
		<?php
	}
}

add_action('wp_enqueue_scripts', 'storely_child_enqueue_styles', 20);
add_action('wp_enqueue_scripts', 'storely_child_enqueue_scripts');
add_action('get_header', 'add_top_offer_banner', 5);
add_filter('upload_mimes', function ($mimes) {
    $mimes['jpg']  = 'image/jpeg';
    $mimes['jpeg'] = 'image/jpeg';
    $mimes['png']  = 'image/png';
    return $mimes;
});

add_filter('post_thumbnail_html', function($html, $post_id) {
    if (get_post_type($post_id) === 'product' && empty($html)) {
        return '<img src="http://localhost/ecommerce-rede-software/wp-content/uploads/2026/03/produto-sem-imagem.png" alt="Placeholder">';
    }
    return $html;
}, 10, 2);


/**
 * 1. CONFIGURAÇÕES DE AMBIENTE
 */
add_filter('https_ssl_verify', '__return_false'); 
add_filter('http_request_host_is_external', '__return_true');

/**
 * 2. GATILHOS DE ENVIO
 */
add_action('woocommerce_checkout_order_processed', 'enviar_payload_full_backend', 10, 1);
add_action('woocommerce_payment_complete', 'enviar_payload_full_backend', 10, 1);

/**
 * 3. FUNÇÃO PRINCIPAL
 */
function enviar_payload_full_backend($order_id) {

    // Se o hook passar o objeto da ordem em vez do ID, extraímos o ID
    if (is_object($order_id)) {
        $order_id = $order_id->get_id();
    }

    // Trava para evitar duplicidade de envio na mesma execução
    static $processados = [];
    if (in_array($order_id, $processados)) {
        return;
    }
    $processados[] = $order_id;

    $order = wc_get_order($order_id);
    if (!$order || !is_a($order, 'WC_Order')) {
        return;
    }

    $order_data = $order->get_data();

    /**
     * A. CAMPOS EXTRAS DE BILLING
     */
    $raw_birthdate = $order->get_meta('_billing_birthdate');
    $formatted_birthdate = "";
    if (!empty($raw_birthdate)) {
        $timestamp = strtotime(str_replace('/', '-', $raw_birthdate));
        $formatted_birthdate = $timestamp ? date('Y-m-d', $timestamp) : sanitize_text_field($raw_birthdate);
    }

    $billing_extra = [
        "number"       => (string)$order->get_meta('_billing_number'),
        "neighborhood" => (string)$order->get_meta('_billing_neighborhood'),
        "persontype"   => ($order->get_meta('_billing_persontype') == '2') ? 'J' : 'F',
        "cpf"          => (string)$order->get_meta('_billing_cpf'),
        "cnpj"         => (string)$order->get_meta('_billing_cnpj'),
        "birthdate"    => $formatted_birthdate,
        "cellphone"    => (string)$order->get_meta('_billing_cellphone'),
    ];
    
    // Merge seguro dos arrays
    $order_data['billing'] = array_merge($order_data['billing'], $billing_extra);

    /**
     * B. TRATAMENTO DE DATAS (Seguro contra objetos nulos)
     */
    $date_keys = ['date_created', 'date_modified', 'date_paid', 'date_completed'];
    foreach ($date_keys as $key) {
        if (isset($order_data[$key]) && is_object($order_data[$key]) && method_exists($order_data[$key], 'date')) {
            $order_data[$key] = $order_data[$key]->date('Y-m-d\TH:i:s');
        } else {
            $order_data[$key] = "";
        }
    }

    /**
     * C. CAPTURA DE PARCELAS DO MERCADO PAGO
     */
    $mp_installments = $order->get_meta('mp_installments');
    // Fallback para o POST caso o meta ainda não exista
    if (empty($mp_installments) && isset($_POST['installments'])) {
        $mp_installments = sanitize_text_field($_POST['installments']);
    }

    $total_pedido = (float)$order->get_total();
    $qtd_parcelas = (!empty($mp_installments) && (int)$mp_installments > 0) ? (int)$mp_installments : 1;
    $valor_parcela = $total_pedido / $qtd_parcelas;

    $meta_data = [];
    $mp_map = [
        'mp_installments'        => (string)$qtd_parcelas,
        'mp_transaction_amount'  => number_format($total_pedido, 2, '.', ''),
        'mp_transaction_details' => number_format($valor_parcela, 2, '.', ''),
        'mp_total_paid_amount'   => number_format($total_pedido, 2, '.', ''),
        'PAYMENT_ID: DATE'       => $order->get_transaction_id() . ": " . date('Y-m-d\TH:i:sP')
    ];

    foreach ($mp_map as $key => $value) {
        $meta_data[] = [
            "id"    => rand(1000, 9999),
            "key"   => $key,
            "value" => (string)$value
        ];
    }
    $order_data['meta_data'] = $meta_data;

    /**
     * D. ITENS DO PEDIDO (Prevenção de EInvalidCast no Delphi)
     */
    $formatted_items = [];
    foreach ($order->get_items() as $item_id => $item) {
        $product = $item->get_product();
        $img_id  = $product ? $product->get_image_id() : null;
        $img_src = ($img_id) ? wp_get_attachment_url($img_id) : "";

        $formatted_items[] = [
            "id"         => $item_id,
            "name"       => $item->get_name(),
            "product_id" => $item->get_product_id(),
            "quantity"   => (int)$item->get_quantity(),
            "total"      => number_format((float)$item->get_total(), 2, '.', ''),
            "sku"        => $product ? (string)$product->get_sku() : "",
            "image"      => [
                "id"  => $img_id ? (string)$img_id : "",
                "src" => $img_src ? (string)$img_src : "" // Nunca envia boolean false
            ]
        ];
    }
    $order_data['line_items'] = $formatted_items;

    /**
     * E. ENVIO PARA O BACKEND
     */
    $url_backend = 'https://resumptive-suzette-spiniferous.ngrok-free.dev/api/pedido-woocommerce/2433/90';

    $args = [
        'method'      => 'POST',
        'timeout'     => 45,
        'headers'     => [
            'Content-Type' => 'application/json; charset=utf-8'
        ],
        'body'        => json_encode($order_data),
        'sslverify'   => false,
        'blocking'    => true
    ];

    $response = wp_remote_post($url_backend, $args);

    // Grava log de erro se a requisição falhar
    if (is_wp_error($response)) {
        error_log("BACKEND ERROR PEDIDO " . $order_id . ": " . $response->get_error_message());
    }
}