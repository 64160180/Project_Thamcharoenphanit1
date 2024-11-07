<?php
// ตรวจสอบว่ามี session ที่ถูกเริ่มต้นแล้วหรือยัง หากยังให้เริ่มต้น session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="main.php" class="brand-link">
        <img src="../assets/dist/img/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> ธรรมเจริญพาณิช </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Search Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- หน้าแรก -->
                <li class="nav-item">
                    <a href="main.php" class="nav-link">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <p>หน้าแรก</p>
                    </a>
                </li>

                <!-- Dashboard (เฉพาะผู้ดูแลระบบ) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                <li class="nav-item">
                    <a href="mainprofit.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php } ?>

                <!-- จัดการข้อมูลพนักงาน (เฉพาะผู้ดูแลระบบ) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                <li class="nav-item">
                    <a href="member.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>จัดการข้อมูลพนักงาน</p>
                    </a>
                </li>
                <?php } ?>

                <!-- จัดการหมวดหมู่สินค้า -->
                <li class="nav-item">
                    <a href="type.php" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>จัดการหมวดหมู่สินค้า</p>
                    </a>
                </li>

                <!-- จัดการข้อมูลสินค้า -->
                <li class="nav-item">
                    <a href="product.php" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>จัดการข้อมูลสินค้า</p>
                    </a>
                </li>

                <!-- นำเข้าสินค้า -->
                <li class="nav-item">
                    <a href="product_addnew.php" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>นำเข้าสินค้า</p>
                    </a>
                </li>

                <!-- รถเข็น -->
                <li class="nav-item">
                    <a href="cart.php" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>รถเข็น</p>
                    </a>
                </li>

                <!-- ประวัติการนำออกสินค้า -->
                <li class="nav-item">
                    <a href="order.php" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>ประวัติการนำออกสินค้า</p>
                    </a>
                </li>

                <!-- ปฏิทิน
                <li class="nav-item">
                    <a href="calendar.php" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>ปฏิทิน</p>
                    </a>
                </li> -->
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
