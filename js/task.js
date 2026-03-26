const shopKey = `visitedShop_user_${CURRENT_USER_ID}`;
const copyKey = `hasCopiedLink_user_${CURRENT_USER_ID}`;

function copyAndVerify() {
    const copyText = document.getElementById("shareUrl");
    copyText.select();
    copyText.setSelectionRange(0, 99999);

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            handleCopySuccess();
        } else {
            alert("Copy failed. Please manually select and copy the link.");
        }
    } catch (err) {
        console.error('Fallback copy method failed:', err);
    }
}

function handleCopySuccess() {
    const feedback = document.getElementById("copyFeedback");
    if (feedback) feedback.style.display = "block";

    const today = new Date().toISOString().split('T')[0];
    const data = JSON.stringify({ status: 'true', date: today });
    
    sessionStorage.setItem(copyKey, data);

    const claimBtn = document.getElementById("btn-share-claim");
    if (claimBtn) claimBtn.classList.replace('btn-success', 'btn-warning');
}

function claimTask(taskId) {
    const btn = event.currentTarget || event.target;
    const today = new Date().toISOString().split('T')[0];
    const gameKey = `play_game_user_${CURRENT_USER_ID}`;

    const isTaskValid = (key) => {
        const rawData = sessionStorage.getItem(key);
        if (!rawData) return false;
        try {
            const data = JSON.parse(rawData);
            return data.status === 'true' && data.date === today;
        } catch (e) {
            return false;
        }
    };

    if (taskId === 'visit_shop' && !isTaskValid(shopKey)) {
        alert("Your shop visit from yesterday expired. Please visit the shop again today!");
        return;
    }

    if (taskId === 'share_social' && !isTaskValid(copyKey)) {
        alert("Please copy the URL again today!");
        return;
    }

    if (taskId === 'play_game' && !isTaskValid(gameKey)) {
        alert("You haven't finished the game today! Redirecting you now...");
        window.location.href = "game.php";
        return;
    }

    fetch('ajax/claim_tasks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `task_id=${taskId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.innerText = "Claimed!";
                btn.disabled = true;
                btn.classList.replace('btn-success', 'btn-secondary');
                btn.classList.replace('btn-warning', 'btn-secondary');
                alert(`Success! You earned ${data.new_points} points.`);
            } else {
                alert("Error: " + data.message);
            }
        });
}