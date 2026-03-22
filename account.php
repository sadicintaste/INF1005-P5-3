<?php
session_start();
$isIn = isset($_SESSION['user_id']);

$inventory = [];
$errorMsg = "";
$success = true;


function getUserInventory($user_id)
{

    global $errorMsg, $success;
    $conn = DBConnect::connect();
    $stmt = $conn->prepare("SELECT * FROM User_Inventory
                                            WHERE user_id = ?");
    // Bind & execute the query statement:
    if (!$stmt) {
        $errorMsg = "Prepare failed: (" . $conn->errno . ") " .
            $conn->error;
        $success = false;
    } else {
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            $errorMsg = "Execute failed: (" . $stmt->errno . ") " .
                $stmt->error;
            $success = false;
        } else {
            $result = $stmt->get_result();
            if (!$result) {
                $errorMsg = "Get result failed: (" . $stmt->errno . ") " .
                    $stmt->error;
                $success = false;
            } elseif ($result->num_rows > 0) {
                $inventory = [];
                while ($row = $result->fetch_assoc()) {
                    $inventory[] = $row;
                }
                $stmt->close();
                return $inventory;
            } else {
                $stmt->close();
                return [];
            }
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<?php
$style = "account.css";
include "inc/head.inc.php";
include "inc/db_connect.php";
require_once __DIR__ . '/vendor/autoload.php';

use TCGdex\TCGdex;

$tcgdex = new TCGdex("en");

// suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED);
?>

<body>
    <?php include 'inc/nav.inc.php'; ?>

    <main>
        <section class="py-4">
            <div class="container">
                <h1 class="text-center mb-4">My Account</h1>
                <p class="text-center mb-5">Manage your account details and view your points.</p>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card bg-dark text-light">
                            <div class="card-body">
                                <?php
                                try {
                                    if (!$isIn) {
                                        throw new Exception("You must be logged in to view your account details.");
                                    }

                                    $user_id = (int)$_SESSION['user_id'];
                                    $user = DBConnect::getUserDetails($user_id);

                                    if ($user === null) {
                                        $errorMsg = "User not found.";
                                        $success = false;
                                    } else {
                                        $username = $user['username'];
                                        $email = $user['email'];
                                        $points = $user['points'];
                                        $success = true;
                                    }
                                } catch (Exception $e) {
                                    $errorMsg = $e->getMessage();
                                    $success = false;
                                }
                                if ($success) { ?>
                                    <h5 class='card-title'>Username: <span class='text-primary'> <?php echo $username; ?></span></h5>
                                    <p class='card-text'>Email: <span class='text-primary'><?php echo $email; ?></span></p>
                                    <p class='card-text'>Points: <span class='text-primary'><?php echo $points; ?></span></p>
                                    <div class="btn-container mt-4 text-center">
                                        <a href="signout_process.php" class="btn btn-outline-danger ms-2">Sign Out</a>
                                        <a href="account_settings.php" class="btn btn-outline-light ms-2">Account Settings</a>
                                    </div>
                                <?php } else { ?>
                                    <p class='text-danger text-center'>Error fetching user details: <?php echo $errorMsg; ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">My Inventory</h2>

                <?php
                $inventory = getUserInventory($user_id);
                DBConnect::close();

                if ($success && !empty($inventory)) { ?>
                    <div class="row g-4">
                        <?php foreach ($inventory as $item) {
                            $card = $tcgdex->card->get($item['card_id']);
                            $float = $item['quality_value'];
                            $qualities = ['Common', 'Rare', 'Epic', 'Legendary'];
                            $hash = crc32($card->id);
                            $quality = $qualities[$hash % 4];

                            $quality_color = '';
                            switch (strtolower($quality)) {
                                case 'common':
                                    $quality_color = '#b0bec5';
                                    break;
                                case 'rare':
                                    $quality_color = '#29b6f6';
                                    break;
                                case 'epic':
                                    $quality_color = '#ab47bc';
                                    break;
                                case 'legendary':
                                    $quality_color = '#ffca28';
                                    break;
                            }
                        ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="card bg-dark text-light h-100 position-relative p-2"
                                    style="border: 4px solid <?php echo $quality_color; ?>; box-shadow: 0 4px 8px black, 0 0 15px <?php echo $quality_color; ?>60; box-shadow 0.2s;">
                                    <img src="<?php echo $card->image . '/low.webp'; ?>" class="card-img-top w-100" alt="Card Image" style="object-fit: contain;">
                                    <div class="card-body d-flex flex-column text-center p-3">
                                        <h5 class="card-title fw-bold mb-2"><?php echo $card->name; ?></h5>
                                        <h6 class="card-text mb-2">Float: <?php echo number_format($float, 15); ?></h6>
                                        <div class="mb-2">
                                            <span class="badge rounded-pill"
                                                style="background-color: <?php echo $quality_color; ?>; color: #111; font-weight: 800; letter-spacing: 1px; padding: 0.5em 1em;">
                                                <?php echo strtoupper($quality); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else if ($success) {
                    echo "<p class='text-center text-muted mt-5'>Your inventory is empty.</p>";
                } else {
                    echo "<p class='text-center text-danger'>Error fetching inventory: $errorMsg</p>";
                }
                ?>
            </div>
        </section>
    </main>

</body>

</html>