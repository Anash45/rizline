<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <h3 class="mb-0 font-weight-bold">Rizline</h3>
            </a>
            <!-- Toggle button for mobile view -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menu items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item px-2 <?php echo $active = ($page == 'items') ? 'active' : ''; ?>">
                        <a class="nav-link" href="./index.php">Home</a>
                    </li>
                    <li class="nav-item px-2 <?php echo $active = ($page == 'orders') ? 'active' : ''; ?>">
                        <a class="nav-link" href="./orders.php">Orders</a>
                    </li>
                    <?php
                    if(isLoggedIn() && isAdmin()){
                        ?>
                        
                        <?php
                    }
                    ?>
                    <?php
                    if(isLoggedIn() && $page == 'items'){
                        ?>
                        <li class="nav-item px-2">
                            <a class="nav-link order-total font-weight-bold text-success"></a>
                        </li>
                        <li class="nav-item px-2">
                            <button type="submit" class="btn btn-success" name="submit">Confirm Order</button>
                        </li>
                        <?php
                    }
                    ?>
                    <li class="nav-item px-2">
                        <a href="./logout.php" class="btn btn-danger">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>