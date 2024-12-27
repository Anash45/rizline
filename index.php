<?php
include "db.php";
if (!isLoggedIn()) {
    header('location:signin.php');
}

$page = "items";
$info = '';

if (isset($_POST['submit'])) {
    // Assign user ID and input data
    $order_currency = $_POST['order_currency'] ?? 'usd'; // Default to 'usd' if not provided
    $user_id = $_SESSION['user_id'] ?? null;
    $items = $_POST['item_ids'] ?? null; // Array of item IDs
    $quantities = $_POST['quantities'] ?? null; // Array of quantities
    $prices = $_POST['prices'] ?? null; // Array of prices

    // Validate input
    if (
        ($items !== null && $quantities !== null && $prices !== null) &&
        is_array($items) &&
        is_array($quantities) &&
        is_array($prices) &&
        count($items) === count($quantities) &&
        count($items) === count($prices)
    ) {
        // Insert data into orders table
        $order_status = 2; // Example status
        $created_at = date('Y-m-d H:i:s');

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_status, order_currency, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $order_status, $order_currency, $created_at);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id; // Get the last inserted order ID

            // Insert data into order_lines table
            $stmt_lines = $conn->prepare("INSERT INTO order_lines (order_id, item_id, quantity, price, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt_lines->bind_param("iiids", $order_id, $item_id, $quantity, $price, $created_at);

            // Loop through each item and insert into order_lines
            foreach ($items as $key => $item_id) {
                $quantity = $quantities[$key];
                if ($quantity != '' && $quantity > 0) {
                    $price = $prices[$key];
                    $created_at = date('Y-m-d H:i:s');

                    if (!$stmt_lines->execute()) {
                        $info = "<div class='alert alert-danger'>Error inserting order line for item ID $item_id: " . $stmt_lines->error . "</div>";
                        break;
                    }
                }
            }

            $stmt_lines->close();

            if (empty($info)) {
                $info = "<div class='alert alert-success'>Order successfully created with ID: $order_id </div>";
                header("refresh:2,url=./index.php");
            }
        } else {
            $info = "<div class='alert alert-danger'>Error inserting order: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $info = "<div class='alert alert-danger'>Invalid input data. Please check your items, quantities, and prices.</div>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rizline</title>
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css"> -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
            integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="./style.css">
    </head>

    <body>
        <div class="container-fluid">
            <form action="" method="post">
                <?php
                include './header.php';
                ?>
                <main class="mt-4">
                    <div class="container">
                        <?php echo $info; ?>
                    </div>
                    <div class="table-responsive">
                        <table id="rizline" class="table table-bordered" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>Id</th>
                                    <th>EAN</th>
                                    <th>Item <br>
                                        <div class="dropdown w-100">
                                            <button class="btn w-100 btn-light dropdown-toggle" type="button"
                                                id="itemDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                Filter Items </button>
                                            <ul class="dropdown-menu" aria-labelledby="itemDropdown">
                                                <?php
                                                $items = mysqli_query($conn, "SELECT DISTINCT ITEM FROM rizline_list");
                                                while ($item = mysqli_fetch_assoc($items)) {
                                                    if ($item['ITEM'] != '') {
                                                        echo '<li>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="item-filter" value="' . htmlspecialchars($item['ITEM']) . '"> ' . htmlspecialchars($item['ITEM']) . '
                                </label>
                              </li>';
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </th>
                                    <th>Brand</th>
                                    <th class="text-left">Model</th>
                                    <th>Year</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Sum</th>
                                    <th>Image</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $result = mysqli_query($conn, "SELECT ID, ITEM, EAN, BRAND, MODEL, YEAR, QTY, PRICE, IMAGE FROM rizline_list"); ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr class="text-center" id="item_<?php echo $row['ID']; ?>">
                                        <td class="col-sm-1 align-middle">
                                            <span class="item-id"><?php echo $row['ID']; ?></span>
                                        </td>
                                        <td class="col-sm-1 align-middle"><?php echo $row['EAN']; ?></td>
                                        <td class="col-sm-2 align-middle"><?php echo $row['ITEM']; ?></td>
                                        <td class="col-sm-1 align-middle"><?php echo $row['BRAND']; ?></td>
                                        <td class="text-left align-middle" style="padding-left: 10px;">
                                            <?php echo $row['MODEL']; ?>
                                        </td>
                                        <td class="col-sm-1 align-middle"><?php echo $row['YEAR']; ?></td>
                                        <td class="col-sm-1 align-middle"><input class="form-control input-sm item-qty"
                                                min="0" type="number"></td>
                                        <td class="col-sm-1 align-middle"><span class="price-sign">&euro;</span> 
                                            <span class="item-price price-amount eur"><?php echo $row['PRICE']; ?></span>
                                        </td>
                                        <td class="col-sm-1 align-middle"><span class="price-sign"></span> <span class="item-sum price-amount">0.00</span></td>
                                        <td class="col-sm-1 align-middle"><a href="<?php echo $row['IMAGE']; ?>"
                                                target="_blank"><i class="bi bi-image"></i></a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7"></th>
                                    <th>TOTAL</th>
                                    <th class="text-success"><span class="order-total">00</span></th>
                                    <th><button class="btn btn-primary btn-lg" name="submit">Confirm Order</button></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </main>
            </form>
        </div>
        <script defer src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script defer
            src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
        <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
        <script defer src="script.js"></script>
    </body>

</html>