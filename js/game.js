const gameGrid = document.getElementById('game-grid');

let cards = [...cardImages, ...cardImages]; // Create pairs
let hasFlippedCard = false;
let lockBoard = false;
let firstCard, secondCard;
let matchesFound = 0;

function shuffle() {
    // Use a more thorough shuffle for the 12-card grid
    cards.sort(() => Math.random() - 0.5);
}

function createBoard() {
    gameGrid.innerHTML = ''; // Clear board
    shuffle();

    cards.forEach(imgSrc => {
        const card = document.createElement('div');
        card.classList.add('memory-card');
        card.dataset.framework = imgSrc;

        // --- ADD THIS BLOCK ---
        // This puts the actual front (image) and back (gray) into the card
        card.innerHTML = `
            <img class="front-face" src="${imgSrc}">
            <div class="back-face"></div>
        `;
        // -----------------------

        card.addEventListener('click', flipCard);
        gameGrid.appendChild(card);
    });
}

function flipCard() {
    if (lockBoard || this === firstCard) return;
    this.classList.add('flip');

    if (!hasFlippedCard) {
        hasFlippedCard = true;
        firstCard = this;
        return;
    }

    secondCard = this;
    checkForMatch();
}

function checkForMatch() {
    let isMatch = firstCard.dataset.framework === secondCard.dataset.framework;
    isMatch ? disableCards() : unflipCards();
}

function disableCards() {
    firstCard.removeEventListener('click', flipCard);
    secondCard.removeEventListener('click', flipCard);
    matchesFound++;

    if (matchesFound === cardImages.length) {
        finishGame();
    }
    resetBoard();
}

function unflipCards() {
    lockBoard = true;
    setTimeout(() => {
        firstCard.classList.remove('flip');
        secondCard.classList.remove('flip');
        resetBoard();
    }, 1000);
}

function resetBoard() {
    [hasFlippedCard, lockBoard] = [false, false];
    [firstCard, secondCard] = [null, null];
}

function finishGame() {
    const gameKey = `play_game_user_${CURRENT_USER_ID}`;
    const today = new Date().toISOString().split('T')[0];

    // Save completion with date for midnight reset logic
    const data = JSON.stringify({ status: 'true', date: today });
    sessionStorage.setItem(gameKey, data);

    alert("Congratulations! You completed the game. Go claim your 25 points!");
    window.location.href = 'tasks.php';
}

createBoard();
