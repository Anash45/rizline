<?php
include "db.php";

if (!isLoggedIn()) {
    header('signin.php');
}

$page = 'orders';

$info = '';

if(isset($_REQUEST['confirm']) && isAdmin()){
    $order_id = $_REQUEST['confirm'];
    $sql4 = "UPDATE orders SET `order_status` = 1 WHERE `order_id` = $order_id";
    $result4 = mysqli_query($conn,$sql4);
    if($result4){
        $info = '<div class="alert alert-success">Order status updated!</div>';
    }else{
        $info = '<div class="alert alert-danger">An error occurred!</div>';
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
                        <div class="table-responsive">
                            <table class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr class="text-center">
                                        <th>Order ID</th>
                                        <?php
                                        if (isLoggedIn() && isAdmin()) {
                                            echo '<th>User</th>';
                                        }
                                        ?>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $user_id = $_SESSION['user_id'];
                                    if(isAdmin()){
                                        $sql1 = "SELECT * FROM orders ORDER BY order_id DESC";
                                    }else{
                                        $sql1 = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_id DESC";
                                    }
                                    
                                    $result = mysqli_query($conn, $sql1);

                                    ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="text-center">
                                            <td class="col-sm-1 align-middle">
                                                <?php echo $row['order_id']; ?>
                                            </td>
                                            <?php
                                            if (isLoggedIn() && isAdmin()) {
                                                echo '
                                        <td>';
                                                $user_id = $row['user_id'];
                                                $sql2 = "SELECT * FROM users WHERE `id` = '$user_id'";
                                                $result2 = mysqli_query($conn, $sql2);
                                                $row2 = mysqli_fetch_assoc($result2);
                                                if (!empty($row2)) {
                                                    $username = $row2['username'];
                                                } else {
                                                    $username = 'NULL';
                                                }
                                                echo $username;
                                            }

                                            echo '
                                        </td>';
                                            ?>
                                            <td>
                                                <?php

                                                $order_id = $row['order_id'];
                                                $sql3 = "SELECT * FROM order_lines WHERE `order_id` = '$order_id'";
                                                $result3 = mysqli_query($conn, $sql3);
                                                $total = 0;
                                                $items = 0;
                                                if (mysqli_num_rows($result3) > 0) {
                                                    while ($row3 = mysqli_fetch_assoc($result3)) {
                                                        $itemTotal = $row3['quantity'] * $row3['price'];
                                                        $total += $itemTotal;
                                                        $items++;
                                                    }
                                                }

                                                echo $items;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo '$' . number_format($total, 2, '.', ',');
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $status = ($row['order_status'] == 1) ? '<span class="font-weight-bold text-success">Confirmed</span>' : '<span class="font-weight-bold text-warning">Pending</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $row['created_at'];
                                                ?>
                                            </td>
                                            <td>
                                                <a href="./order_details.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">Details</a>
                                                <?php
                                                echo $status = ($row['order_status'] == 2 && isAdmin()) ? '<a href="./orders.php?confirm='.$order_id.'" class="btn btn-success">Confirm</a>' : '';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
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
        <script defer src="script.js"></script>
    </body>

</html>