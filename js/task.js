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

    sessionStorage.setItem(copyKey, 'true');

    const claimBtn = document.getElementById("btn-share-claim");
    if (claimBtn) claimBtn.classList.replace('btn-success', 'btn-warning');
}

function claimTask(taskId) {
    const btn = event.currentTarget || event.target;

    if (taskId === 'visit_shop' && sessionStorage.getItem(shopKey) !== 'true') {
        alert("You must visit the shop page first!");
        return;
    }

    if (taskId === 'share_social' && sessionStorage.getItem(copyKey) !== 'true') {
        alert("Please copy the URL first!");
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