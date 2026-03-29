// loader
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    const content = document.querySelector('main');

    if (!loader || !content) return;

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

            allFlipped();

        }, 500);
    }, 500);
});

// index
function indexFlip(card, index) {
    if (!card.classList.contains('flipped')) {
        card.classList.add('flipped');

        fetch('index_persist.php', {
            method: 'POST',
            body: JSON.stringify({ index: index }),
            headers: { 'Content-Type': 'application/json' }
        }) .then(res => res.json())
        .then(data => {
            if (data.success && data.newPoints !== undefined) {
                const pointsDisplay = document.querySelector('.nav-user-meta span:last-child');
                if (pointsDisplay) {
                    pointsDisplay.innerHTML = `⭐ ${Number(data.newPoints).toLocaleString()} pts`;
                }
            }
        })
        .catch(err => console.error("Error updating points:", err));;

        allFlipped();
    }
}

function allFlipped() {
    const allCards = document.querySelectorAll('.index-card');
    const flippedCards = document.querySelectorAll('.index-card.flipped');

    if (flippedCards.length === allCards.length) {
        const section = document.getElementById('index-gacha');
        const prompt = document.getElementById('index-register');

        section.classList.add('index-shift');
        prompt.classList.remove('d-none');
        setTimeout(() => {
            prompt.classList.add('index-show');
        }, 50);
    }
}

function indexReroll() {
    if (!confirm("Spend 15 points to reroll a new deck?")) return;

    fetch('index_reroll.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload(); 
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error("Repull error:", err);
        alert("An error occurred while repulling cards.");
    });
}
