<?php
include "db.php";

$info = '';
if (isset($_REQUEST['submit'])) {
    // Assign user ID and input data
    $user_id = 1;
    $items = $_REQUEST['item_id']; // Array of item IDs
    $quantities = $_REQUEST['quantity']; // Array of quantities
    $prices = $_REQUEST['price']; // Array of prices
    

    // Validate input
    if (is_array($items) && is_array($quantities) && is_array($prices) && count($items) === count($quantities) && count($items) === count($prices)) {
        // Insert data into orders table
        $order_status = 2; // Example status
        $created_at = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_status, created_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $order_status, $created_at);

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
            }
        } else {
            $info = "<div class='alert alert-danger'>Error inserting order: " . $stmt->error ."</div>";
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
        <div class="container-fluid py-4">
            <?php echo $info; ?>
            <form action="" method="post">
                <div class="table-responsive">
                    <table id="rizline" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr class="text-center">
                                <th>Id</th>
                                <th>EAN</th>
                                <th>Item</th>
                                <th>Brand</th>
                                <th class="text-left">Model</th>
                                <th>Year</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Sum</th>
                                <th>Image</th>
                            </tr>
                            <tr>
                                <th><input class="filter-inp form-control" type="text" placeholder="Search Id"></th>
                                <th><input class="filter-inp form-control" type="text" placeholder="Search EAN"></th>
                                <th><input class="filter-inp form-control" type="text" placeholder="Search Item"></th>
                                <th><input class="filter-inp form-control" type="text" placeholder="Search Brand"></th>
                                <th><input class="filter-inp form-control" type="text" placeholder="Search Model"></th>
                                <th><input class="filter-inp form-control" type="text" placeholder="Search Year"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $result = mysqli_query($conn, "SELECT ID, ITEM, EAN, BRAND, MODEL, YEAR, QTY, PRICE, IMAGE FROM rizline_list"); ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="text-center">
                                    <td class="col-sm-1 align-middle">
                                        <input type="hidden" name="item_id[]" value="<?php echo $row['ID']; ?>">
                                        <?php echo $row['ID']; ?>
                                    </td>
                                    <td class="col-sm-1 align-middle"><?php echo $row['EAN']; ?></td>
                                    <td class="col-sm-2 align-middle"><?php echo $row['ITEM']; ?></td>
                                    <td class="col-sm-1 align-middle"><?php echo $row['BRAND']; ?></td>
                                    <td class="text-left align-middle" style="padding-left: 10px;">
                                        <?php echo $row['MODEL']; ?>
                                    </td>
                                    <td class="col-sm-1 align-middle"><?php echo $row['YEAR']; ?></td>
                                    <td class="col-sm-1 align-middle"><input class="form-control input-sm item-qty"
                                            name="quantity[]" min="0" type="number"></td>
                                    <td class="col-sm-1 align-middle">
                                        <input type="hidden" name="price[]" value="<?php echo $row['PRICE']; ?>">
                                        <span class="item-price"><?php echo $row['PRICE']; ?></span>
                                    </td>
                                    <td class="col-sm-1 align-middle"><span class="item-sum">30</span></td>
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
                                <th><button class="btn btn-primary btn-lg" name="submit">Confirm</button></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
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