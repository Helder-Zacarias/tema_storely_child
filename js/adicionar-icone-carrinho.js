document.addEventListener('DOMContentLoaded', () => {
    const rootStyles = getComputedStyle(document.documentElement);
    const header = document.querySelector("#main-header");
    header.style.setProperty('margin-bottom', '1rem', 'important');

    // Tira a margem desse elemento para que a div de carrrinho fique centralizada
    const browseSection = document.querySelector('#browse-section');
    browseSection.style.setProperty('margin', '0', 'important');

    const cartIconContainer = document.createElement('div');
    const cartIcon = document.createElement('i');
    const cartText = document.createElement('h5');

    cartIconContainer.style.display = 'flex';
    cartIconContainer.style.alignItems = 'center';
    cartIconContainer.style.justifyContent = 'center';
    cartIconContainer.style.gap = '0.5rem';
    cartIconContainer.style.paddingTop = '1rem';
    cartIconContainer.style.paddingBottom = '1rem';
    cartIconContainer.style.color = rootStyles.getPropertyValue('--color-text-secondary');

    cartIcon.classList.add('fa', 'fa-shopping-cart');
    
    cartText.textContent = 'Carrinho';
    cartText.style.margin = 0;

    cartIconContainer.appendChild(cartIcon);
    cartIconContainer.appendChild(cartText);
    header.insertAdjacentElement('afterend', cartIconContainer);
});
