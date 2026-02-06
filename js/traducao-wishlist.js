document.addEventListener('DOMContentLoaded', () => {
    const wishlistDataContainer = document.querySelector('.shopengine-wishlist');
    const headers = wishlistDataContainer.getElementsByTagName('th');
    headers[0].textContent = 'Produtos';
    headers[1].textContent = 'Preço Unitário';
    headers[2].textContent = 'Situação do Estoque';
    headers[3].textContent = 'Ação';
});