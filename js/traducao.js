document.addEventListener('DOMContentLoaded', () => {
// 	
    const btn = document.querySelector('.product-category-btn');
    if (!btn) return;
	
	const span = btn.querySelector('span');
	if(!span) return;

    span.childNodes.forEach(node => {
        if (node.nodeType === Node.TEXT_NODE) {
            node.textContent = ' Buscar Categorias';
        }
    });
	
	btn.style.visibility = 'visible';
// 	
})