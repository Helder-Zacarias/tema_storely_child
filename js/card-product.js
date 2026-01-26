// Remove a descrição do produto no card de produto
document.addEventListener('DOMContentLoaded', () => {
	const productContentCollection = document.getElementsByClassName("product-content");
	
	for(const productContent of productContentCollection) {
		for(const node of productContent.childNodes) {
			if(node.nodeType === Node.TEXT_NODE) {
				node.textContent = "";
			}
		}
	}
});

// Adiciona dinamicamente uma classe a div do card de produto
// Evita FOUC (Flash of Unstyled Content)
window.addEventListener('load', () => {
	const productSingleCollection = document.getElementsByClassName("product-single");
	
	for(const productSingle of productSingleCollection) {
		productSingle.classList.add("product-single-ready");
	}
})