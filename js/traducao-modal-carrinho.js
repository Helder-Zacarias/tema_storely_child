document.addEventListener('DOMContentLoaded', () => {
    const cartText = document.querySelector('.cart-text');
    const fecharModal = document.querySelector('.cart-close');
    const addButton = document.querySelector('.cart-ft-btn-cart');


    cartText.textContent = "Carrinho";
    fecharModal.textContent = "FECHAR";
    addButton.textContent = "Ver Carrinho";
})