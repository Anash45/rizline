<?php
include "db.php";

if (!isLoggedIn()) {
    header('signin.php');
}
if (!isset($_REQUEST['order_id'])) {
    header('location:order.php');
}

$order_id = $_GET['order_id'];
$page = 'orders';

$info = '';
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
                        <div class="table-responsive">
                            <div class="mb-2 text-right">
                                <button class="btn btn-primary"
                                    onclick="exportIntoExcel('<?php echo $order_id; ?>')">Export into Excel</button>
                            </div>
                            <table class="table table-bordered" id="order_details" style="width:100%">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="text-center"> Order ID: <?php echo $order_id; ?>
                                        </th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql3 = "SELECT * FROM order_lines WHERE `order_id` = '$order_id'";
                                    $result3 = mysqli_query($conn, $sql3);
                                    $total = 0;
                                    $items = 0;
                                    if (mysqli_num_rows($result3) > 0) {
                                        while ($row3 = mysqli_fetch_assoc($result3)) {

                                            $item_id = $row3['item_id'];
                                            $sql2 = "SELECT * FROM rizline_list WHERE `ID` = '$item_id'";
                                            $result2 = mysqli_query($conn, $sql2);
                                            $row2 = mysqli_fetch_assoc($result2);
                                            if (!empty($row2)) {
                                                $item = $row2['ITEM'];
                                            } else {
                                                $item = 'NULL';
                                            }

                                            $itemTotal = $row3['quantity'] * $row3['price'];
                                            $total += $itemTotal;
                                            $items++;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $item; ?>
                                                </td>
                                                <td>
                                                    <?php echo '$ ' . number_format($row3['price'], 2, '.', ','); ?>
                                                </td>
                                                <td>
                                                    <?php echo $row3['quantity']; ?>
                                                </td>
                                                <td>
                                                    <?php echo '$ ' . number_format($itemTotal, 2, '.', ','); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>Total: </b> </td>
                                        <td>
                                            <b><?php echo '$ ' . number_format($total, 2, '.', ','); ?></b>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </main>
            </form>
        </div>
        <script defer src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script defer
            src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
        <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
        <script defer src="script.js"></script>
    </body>

</html>