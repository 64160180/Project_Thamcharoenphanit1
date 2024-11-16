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

$month = isset($_GET['month']) && is_numeric($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : date('Y');

$rowTodayOut['totalTodayOut'] = $rowTodayOut['totalTodayOut'] ?: 0;


// คิวรีเพื่อดึงข้อมูลรายรับและกำไร จาก tbl_order
$queryRevenueAndProfit = $condb->prepare("
    SELECT 
        DATE(date_out) AS order_date,
        SUM(sell_price * quantity) AS revenue,
        SUM((sell_price - cost_price) * quantity) AS profit
    FROM tbl_order 
    WHERE YEAR(date_out) = :year AND MONTH(date_out) = :month
    GROUP BY DATE(date_out) 
    ORDER BY order_date ASC
");
$queryRevenueAndProfit->bindParam(':month', $month, PDO::PARAM_STR);
$queryRevenueAndProfit->bindParam(':year', $year, PDO::PARAM_STR);
$queryRevenueAndProfit->execute();
$results = $queryRevenueAndProfit->fetchAll(PDO::FETCH_ASSOC);

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

// เตรียมข้อมูลสำหรับกราฟรายรับ กำไร และรายจ่าย
$dates = [];
$revenueValues = [];
$profitValues = [];
$expenseValues = [];

foreach ($results as $result) {
    $dates[] = $result['order_date'];
    $revenueValues[] = $result['revenue'];
    $profitValues[] = $result['profit'];
    
    // ตรวจสอบว่ามีค่าใช้จ่ายสำหรับวันเดียวกันหรือไม่
    $expenseFound = array_filter($expenseResults, function($expense) use ($result) {
        return $expense['expense_date'] === $result['order_date'];
    });

    // ถ้าพบค่าใช้จ่าย ให้เพิ่มค่าใช้จ่ายนั้น; ถ้าไม่พบ ให้เพิ่มค่าเป็น 0
    $expenseValues[] = $expenseFound ? array_values($expenseFound)[0]['total_expenses'] : 0;
}

// ดึงข้อมูลสินค้าจาก `tbl_order` ที่ขายออกล่าสุด
function getLatestOrder($condb) {
    $queryLatestOrder = $condb->prepare("
        SELECT 
            product_id, -- แก้ไขให้ใช้ product_id
            sell_price,
            quantity AS sell_qty
        FROM tbl_order
        WHERE DATE(date_out) = CURDATE() -- เฉพาะรายการขายวันนี้
        ORDER BY date_out DESC
        LIMIT 1
    ");
    $queryLatestOrder->execute();
    return $queryLatestOrder->fetch(PDO::FETCH_ASSOC);
}


// ดึงข้อมูลสินค้าที่ค้างอยู่ในคลัง
function getStockData($condb, $productId) {
    $queryStock = $condb->prepare("
        SELECT 
            product_qty AS current_qty,
            cost_price AS current_cost_price
        FROM tbl_product
        WHERE id = :product_id
    ");
    $queryStock->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $queryStock->execute();
    return $queryStock->fetch(PDO::FETCH_ASSOC);
}

// ดึงข้อมูลสินค้าที่เพิ่มเข้ามาใหม่
function getNewStockData($condb, $productId) {
    $queryNewStock = $condb->prepare("
        SELECT 
            SUM(newproduct_qty) AS new_qty,
            AVG(newcost_price) AS new_cost_price
        FROM tbl_newproduct
        WHERE id = :product_id -- ใช้ id แทน ref_product_id
    ");
    $queryNewStock->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $queryNewStock->execute();
    return $queryNewStock->fetch(PDO::FETCH_ASSOC);
}


// คำนวณค่าเฉลี่ยต้นทุน
function calculateAverageCost($currentQty, $currentCostPrice, $newQty, $newCostPrice) {
    $totalQty = $currentQty + $newQty;
    return $totalQty > 0
        ? (($currentQty * $currentCostPrice) + ($newQty * $newCostPrice)) / $totalQty
        : 0;
}

// คำนวณกำไร
function calculateProfit($sellPrice, $averageCostPrice, $sellQty) {
    return ($sellPrice - $averageCostPrice) * $sellQty;
}

// ดึงข้อมูลคำสั่งซื้อสินค้าล่าสุด
$latestOrder = getLatestOrder($condb);

if ($latestOrder) {
    $productId = $latestOrder['product_id'];
    $sellPrice = $latestOrder['sell_price'];
    $sellQty = $latestOrder['sell_qty'];

    // ดึงข้อมูลสินค้าค้างคลัง
    $stockData = getStockData($condb, $productId);
    $currentQty = $stockData['current_qty'] ?: 0;
    $currentCostPrice = $stockData['current_cost_price'] ?: 0;

    // ดึงข้อมูลสินค้าที่เพิ่มเข้ามาใหม่
    $newStockData = getNewStockData($condb, $productId);
    $newQty = $newStockData['new_qty'] ?: 0;
    $newCostPrice = $newStockData['new_cost_price'] ?: 0;

    // คำนวณค่าเฉลี่ยต้นทุน
    $averageCostPrice = calculateAverageCost($currentQty, $currentCostPrice, $newQty, $newCostPrice);

    // คำนวณกำไร
    $profit = calculateProfit($sellPrice, $averageCostPrice, $sellQty);

    $results[] = [
        'product_id' => $productId,
        'average_cost_price' => number_format($averageCostPrice, 2),
        'profit' => number_format($profit, 2)
    ];

} else {
// ไม่แสดงผลที่หน้าจอ แต่สามารถจัดการกรณีไม่มีคำสั่งซื้อได้ที่นี่
$results[] = 'ไม่มีคำสั่งซื้อสินค้าประจำเดือนและปีนี้';
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
                                        data: <?= json_encode($profitValues); ?>,
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