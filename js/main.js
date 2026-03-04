// loader
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    const content = document.querySelector('main');

    const indexCards = document.querySelectorAll('.index-card');

    setTimeout(() => {
        loader.style.opacity = '0';
        loader.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
            loader.style.display = 'none';
            content.style.display = 'block';

            indexCards.forEach((card, index) => {
                card.style.setProperty('--order', index);
                card.classList.add('slide');
            });

        }, 500);
    }, 500); 
});

