document.addEventListener('DOMContentLoaded', () => {
// 	Tradução do botão de navegação de categorias
    const btn = document.querySelector('.product-category-btn');
    if (!btn) return;
	
	const span = btn.querySelector('span');
	if(!span) return;

    span.childNodes.forEach(node => {
        if (node.nodeType === Node.TEXT_NODE) {
            node.textContent = ' Todas as Categorias';
        }
    });
	
	btn.style.visibility = 'visible';
// 
	
// 	Tradução do placeholder da barra de pesquisa
	const headerSearchInput = document.querySelector('.header-search-input');
	headerSearchInput.setAttribute('placeholder', "Buscar Produtos");
})