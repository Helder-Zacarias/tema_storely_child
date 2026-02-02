document.addEventListener('DOMContentLoaded', () => {
    const cupom = document.querySelector('.wc-block-components-panel__button');
    const total = document.querySelector('.wc-block-components-totals-item__label');

    cupom.textContent = "Inserir Cupons";
    total.textContent = "Total";
});