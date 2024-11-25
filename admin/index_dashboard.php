<?php 
require_once '../config/condb.php';

// จำนวนหมวดหมู่สินค้า
$stmtCountCounter = $condb->prepare("SELECT COUNT(*) as totalView FROM tbl_type");
$stmtCountCounter->execute();
$rowC = $stmtCountCounter->fetch(PDO::FETCH_ASSOC);

// จำนวนสมาชิก
$stmtCountMember = $condb->prepare("SELECT COUNT(*) as totalMember FROM tbl_member");
$stmtCountMember->execute();
$rowM = $stmtCountMember->fetch(PDO::FETCH_ASSOC);

// จำนวนสินค้า
$stmtCountProduct = $condb->prepare("SELECT COUNT(*) as totalProduct FROM tbl_product");
$stmtCountProduct->execute();
$rowP = $stmtCountProduct->fetch(PDO::FETCH_ASSOC);

// นับจำนวนสินค้าที่นำออกทั้งหมดในวันนี้
$queryTodayOut = $condb->prepare("SELECT SUM(quantity) AS totalTodayOut FROM tbl_order WHERE DATE(date_out) = CURDATE()");
$queryTodayOut->execute();
$rowTodayOut = $queryTodayOut->fetch(PDO::FETCH_ASSOC);

$month = isset($_GET['month']) ? $_GET['month'] : date('m'); // ตั้งค่าเป็นเดือนปัจจุบันโดยอัตโนมัติ
$year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // ตั้งค่าเป็นปีปัจจุบันโดยอัตโนมัติ



// คิวรีเพื่อดึงข้อมูลรายจ่ายจาก tbl_newproduct เฉพาะในเดือนและปีที่เลือก
$queryExpenses = $condb->prepare("
    SELECT 
        DATE(dateCreate) AS expense_date,
        SUM(newcost_price * newproduct_qty) AS total_expenses
    FROM tbl_newproduct 
    WHERE YEAR(dateCreate) = :year AND MONTH(dateCreate) = :month
    GROUP BY DATE(dateCreate) 
    ORDER BY expense_date ASC
");
$queryExpenses->bindParam(':month', $month, PDO::PARAM_STR);
$queryExpenses->bindParam(':year', $year, PDO::PARAM_STR);
$queryExpenses->execute();
$expenseResults = $queryExpenses->fetchAll(PDO::FETCH_ASSOC);

$queryNewProductsCost = $condb->prepare("SELECT newproduct_name, SUM(newcost_price * newproduct_qty) AS total_cost, SUM(newproduct_qty) AS total_quantity FROM tbl_newproduct WHERE YEAR(dateCreate) = :year AND MONTH(dateCreate) = :month GROUP BY newproduct_name");
$queryNewProductsCost->bindParam(':year', $year, PDO::PARAM_STR);
$queryNewProductsCost->bindParam(':month', $month, PDO::PARAM_STR);
$queryNewProductsCost->execute();
$products = $queryNewProductsCost->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $product) {
    $average_cost = $product['total_cost'] / $product['total_quantity'];
    // echo "Product: " . $product['newproduct_name'] . " | Average Cost: " . number_format($average_cost, 2) . "\n";
}


// จำนวนหมวดหมู่สินค้า
$stmtCountCounter = $condb->prepare("SELECT COUNT(*) as totalView FROM tbl_type");
$stmtCountCounter->execute();
$rowC = $stmtCountCounter->fetch(PDO::FETCH_ASSOC);

// จำนวนสมาชิก
$stmtCountMember = $condb->prepare("SELECT COUNT(*) as totalMember FROM tbl_member");
$stmtCountMember->execute();
$rowM = $stmtCountMember->fetch(PDO::FETCH_ASSOC);

// จำนวนสินค้า
$stmtCountProduct = $condb->prepare("SELECT COUNT(*) as totalProduct FROM tbl_product");
$stmtCountProduct->execute();
$rowP = $stmtCountProduct->fetch(PDO::FETCH_ASSOC);

// นับจำนวนสินค้าที่นำออกทั้งหมดในวันนี้
$queryTodayOut = $condb->prepare("SELECT SUM(quantity) AS totalTodayOut FROM tbl_order WHERE DATE(date_out) = CURDATE()");
$queryTodayOut->execute();
$rowTodayOut = $queryTodayOut->fetch(PDO::FETCH_ASSOC);

// กำหนดเดือนและปี
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// คิวรีสำหรับรายได้และกำไร
$queryRevenueAndProfit = $condb->prepare("
  SELECT 
    DATE(o.date_out) AS order_date,
    SUM(o.sell_price * o.quantity) AS revenue,
    SUM((o.sell_price - o.historical_cost) * o.quantity) AS profit
  FROM tbl_order o
  WHERE YEAR(o.date_out) = :year AND MONTH(o.date_out) = :month
  GROUP BY DATE(o.date_out)
  ORDER BY order_date ASC
");
$queryRevenueAndProfit->bindParam(':month', $month, PDO::PARAM_STR);
$queryRevenueAndProfit->bindParam(':year', $year, PDO::PARAM_STR);
$queryRevenueAndProfit->execute();
$revenueProfitResults = $queryRevenueAndProfit->fetchAll(PDO::FETCH_ASSOC);

// คิวรีสำหรับรายจ่าย
$queryExpenses = $condb->prepare("
  SELECT 
    DATE(dateCreate) AS expense_date,
    SUM(newcost_price * newproduct_qty) AS total_expense
  FROM tbl_newproduct
  WHERE YEAR(dateCreate) = :year AND MONTH(dateCreate) = :month
  GROUP BY DATE(dateCreate)
  ORDER BY expense_date ASC
");
$queryExpenses->bindParam(':month', $month, PDO::PARAM_STR);
$queryExpenses->bindParam(':year', $year, PDO::PARAM_STR);
$queryExpenses->execute();
$expenseResults = $queryExpenses->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับกราฟ
$dates = [];
$revenueValues = [];
$profitValues = [];
$expenseValues = [];
$expenseMap = [];

// จัดการข้อมูลรายจ่าย
foreach ($expenseResults as $row) {
    $expenseMap[$row['expense_date']] = $row['total_expense'];
}

// จัดการข้อมูลรายได้และกำไร
foreach ($revenueProfitResults as $row) {
    $dates[] = $row['order_date'];
    $revenueValues[] = $row['revenue'];
    $profitValues[] = $row['profit'];

    // ตรวจสอบว่ามีค่าใช้จ่ายสำหรับวันเดียวกันหรือไม่
    $expenseFound = array_filter($expenseResults, function($expense) use ($row) {
        return $expense['expense_date'] === $row['order_date'];
    });

    // ถ้าพบค่าใช้จ่าย ให้เพิ่มค่าใช้จ่ายนั้น; ถ้าไม่พบ ให้เพิ่มค่าเป็น 0
    $expenseValues[] = $expenseFound ? array_values($expenseFound)[0]['total_expense'] : 0;
}

?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>หน้าแรก</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3> <?=$rowC['totalView'];?> </h3>
                                                <p>หมวดหมู่</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3> <?=$rowM['totalMember'];?> </h3>
                                                <p>สมาชิก</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3><?=$rowP['totalProduct'];?></h3>
                                                <p>สินค้า</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-danger">
                                            <div class="inner">
                                                <h3> <?= $rowTodayOut['totalTodayOut'] ?: 0; ?> </h3>
                                                <p>นำสินค้าออกทั้งหมดของวันนี้</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <form method="GET" action="">
                                    <label for="monthSelect">เลือกเดือน:</label>
                                    <select name="month" id="monthSelect" onchange="this.form.submit()">
                                        <?php
                                        
                                        // Thai month names array
                                        $thaiMonths = [
                                            '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน', '05' => 'พฤษภาคม',
                                            '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน', '10' => 'ตุลาคม',
                                            '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                                        ];
                                        
                                        for ($m = 1; $m <= 12; $m++) {
                                            $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT); // Ensure two-digit format for months
                                            $monthName = $thaiMonths[$monthValue]; // Get Thai month name from the array
                                            echo "<option value=\"$monthValue\" " . ($month == $monthValue ? 'selected' : '') . ">$monthName</option>";
                                        }
                                        
                                        
                                        ?>
                                    </select>
                                    <label for="yearSelect">เลือกปี:</label>
                                    <select name="year" id="yearSelect" onchange="this.form.submit()">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = 0; $i < 5; $i++) {
                                            $yearOption = $currentYear - $i;
                                            echo '<option value="' . $yearOption . '" ' . ($year == $yearOption ? 'selected' : '') . '>' . $yearOption . '</option>';
                                        }
                                        ?>
                                    </select>
                                </form>

                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟรายรับ-จ่ายและกำไรรายวัน</h3>
                                        <canvas id="profitChart"></canvas>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <script>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                            const ctxProfit = document.getElementById('profitChart').getContext('2d');
                            const profitChart = new Chart(ctxProfit, {
                                type: 'bar',
                                data: {
                                    labels: <?= json_encode($dates); ?>.map(date => {
                                        const d = new Date(date);
                                        return d.toLocaleDateString('th-TH', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric'
                                        });
                                    }),
                                    datasets: [{
                                        label: 'รายรับ',
                                        data: <?= json_encode($revenueValues); ?>,
                                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    }, {
                                        label: 'รายจ่าย',
                                        data: <?= json_encode($expenseValues); ?>,
                                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                                        borderColor: 'rgba(255, 159, 64,  1)',
                                        borderWidth: 1
                                    }, {
                                        label: 'กำไร',
                                        data: <?= json_encode($profitValues); ?>,  // กำไรคำนวณจากค่าเฉลี่ยต้นทุน
                                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'วัน/เดือน/ปี'
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'จำนวนเงิน (บาท)'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        }
                                    }
                                }
                            });
                            <?php endif; ?>
                            </script>



                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>