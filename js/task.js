let hasCopiedLink = false; // Internal flag to track if the user has copied the link
let hasVisitedShop = localStorage.getItem('visitedShopToday') === 'true'; // Check if the user has visited the shop today
let hasLoggedIn = localStorage.getItem('isLoggedIn') === 'true'; // Check if the user has logged in today

function copyAndVerify() {
    const copyText = document.getElementById("shareUrl");

    navigator.clipboard.writeText(copyText.value).then(() => {
        // Show the feedback text
        document.getElementById("copyFeedback").style.display = "block";

        // Set our internal flag to true
        hasCopiedLink = true;

        // Visual feedback on the claim button
        const claimBtn = document.getElementById("btn-share-claim");
        claimBtn.classList.replace('btn-success', 'btn-warning');

        console.log("Link copied. Task can now be claimed.");
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}

function claimTask(taskId) {
    const btn = event.target; // Get the specific button clicked

    // Logic Gate for Login
    if (taskId === 'login' && !hasLoggedIn) {
        alert("You must be logged in to claim this reward!");
        return;
    }

    // Logic Gate for Visit Shop
    if (taskId === 'visit_shop' && !hasVisitedShop) {
        alert("You must visit the shop page first!");
        // Optional: Automatically redirect them
        // window.location.href = 'shop.php'; 
        return;
    }

    // Logic Gate for Spread Social (Existing)
    if (taskId === 'share_social' && !hasCopiedLink) {
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

                // Clear the local flag after claiming
                if (taskId === 'visit_shop') {
                    localStorage.removeItem('visitedShopToday');
                }
                alert(`Success! You earned ${data.new_points} points.`);
            } else {
                alert("Error: " + data.message);
            }
        });
}

// Add this script to the login page to set the login flag when the user logs in successfully
{/* <script>
    // Since this page is only accessible if logged in, set the flag automatically
    sessionStorage.setItem('isLoggedIn', 'true');
    console.log("Login verified for daily task.");
</script> */}

// Add this script to the shop page to set the visited shop flag when the user visits
{/* <script>
    // Set the flag in the browser's memory
    localStorage.setItem('visitedShopToday', 'true');
    console.log("Shop visit verified for daily task.");
</script> */}