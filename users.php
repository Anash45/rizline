<?php
include "db.php";

if (!isAdmin()) {
    header('location:orders.php');
}

$page = 'users';

$info = '';

if (isset($_REQUEST['approve']) && isAdmin()) {
    $id = $_REQUEST['approve'];
    $sql4 = "UPDATE users SET `status` = 1 WHERE `id` = $id";
    $result4 = mysqli_query($conn, $sql4);
    if ($result4) {
        $info = '<div class="alert alert-success">User approved!</div>';
    } else {
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
        <!-- Include Datepicker CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
            rel="stylesheet">
        <link rel="stylesheet" href="./style.css">
    </head>

    <body>
        <div class="container-fluid">
            <?php
            include './header.php';
            ?>
            <main class="mt-4">
                <div class="container-fluid">
                    <?php echo $info; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Reg Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql1 = "SELECT * FROM users ORDER BY id ASC";

                                $result = mysqli_query($conn, $sql1);

                                ?>
                                <?php while ($row = mysqli_fetch_assoc($result)):
                                    ?>
                                    <tr class="text-center">
                                        <td class="col-sm-1 align-middle">
                                            <?php echo $row['id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['username']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['email']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['user_type']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['status']; ?>
                                        </td>
                                        <td>
                                            <?php echo date('d.m.Y', strtotime($row['reg_date'])); ?>
                                        </td>
                                        <td>
                                            <?php echo $button = ($row['status'] == '0')? '<span class="font-weight-bold text-warning">Pending</span>' : '<span class="font-weight-bold text-success">Approved</span>'; ?>
                                        </td>
                                        <td>
                                            <?php echo $button = ($row['status'] == '0')? '<a class="btn btn-success text-white" href="./users.php?approve='.$row['id'].'">Approve</a>' : ''; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script defer
            src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
        <script defer src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
        <!-- Include Datepicker JS -->
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <!-- Include Turkish Locale -->
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.tr.min.js"></script>
        <script defer src="script.js"></script>
    </body>

</html>