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