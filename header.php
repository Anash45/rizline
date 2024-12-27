<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="./index.php">
                <h3 class="mb-0 font-weight-bold">Rizline</h3>
            </a>
            <!-- Toggle button for mobile view -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menu items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">
                    <?php
                    if (isLoggedIn()) {
                        ?>
                        <li class="nav-item px-2 mr-2">
                            <span class="text-primary text-nowrap">Hi <?php echo $_SESSION['username']; ?></span>
                        </li>
                        <?php
                    }
                    ?>
                    <li class="nav-item px-2 <?php echo $active = ($page == 'items') ? 'active' : ''; ?>">
                        <a class="nav-link" href="./index.php">Home</a>
                    </li>
                    <li class="nav-item px-2 <?php echo $active = ($page == 'orders') ? 'active' : ''; ?>">
                        <a class="nav-link" href="./orders.php">Orders</a>
                    </li>
                    <?php
                    if (isLoggedIn() && $page == 'items') {
                        ?>
                        <li class="nav-item px-2 d-flex flex-column text-center align-items-center">
                            <span class="text-success">Total</span>
                            <a
                                class="nav-link font-weight-bold text-success py-0 d-flex justify-content-center align-items-center gap-1"><span
                                    class="price-sign"></span> <span class="order-total price-amount"></span> </a>
                        </li>
                        <li class="nav-item px-2">
                            <button type="submit" class="btn btn-success text-nowrap" name="submit">Confirm Order</button>
                        </li>
                        <?php
                    }
                    ?>
                    <?php
                    if (isLoggedIn() && $page == 'items') {
                        ?>
                        <select class="form-control" id="current_currency" name="order_currency">
                            <option value="eur">EUR - &euro;</option>
                            <option value="usd">USD - &dollar;</option>
                        </select>
                        <?php
                    }
                    ?>
                    <?php
                    if(isLoggedIn()){
                        echo '<li class="nav-item px-2">
                        <a href="./logout.php" class="btn btn-danger">Logout</a>
                    </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>