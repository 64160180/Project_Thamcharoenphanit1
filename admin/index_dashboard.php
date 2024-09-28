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

// คิวรีเพื่อดึงข้อมูลกำไร
$queryProfit = $condb->prepare("
    SELECT 
        DATE(date_out) AS order_date,
        SUM((sell_price - cost_price) * quantity) AS profit
    FROM tbl_order 
    GROUP BY DATE(date_out) 
    ORDER BY order_date ASC
");
$queryProfit->execute();
$profits = $queryProfit->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับกราฟ
$dates = [];
$profitValues = [];
foreach ($profits as $profit) {
    $dates[] = $profit['order_date'];
    $profitValues[] = $profit['profit'];
}

// ดึงข้อมูลยอดขายของสินค้าทั้งหมด
$query = $condb->prepare("
    SELECT 
        product_id,
        product_name,
        SUM(quantity) AS total_sold,
        AVG(cost_price) AS avg_cost_price,
        AVG(sell_price) AS avg_sell_price
    FROM tbl_order
    GROUP BY product_id
");
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// กำหนดค่าคงที่สำหรับการคำนวณ EOQ
$S = 50; // ต้นทุนการสั่งซื้อ (บาท)
$H = 5;  // ต้นทุนการเก็บสินค้า (บาทต่อปี)

// คำนวณ EOQ สำหรับแต่ละสินค้า
$eoqResults = [];
foreach ($products as $product) {
    $D = $product['total_sold'] ?: 0; // ความต้องการสินค้า
    if ($D > 0) {
        $EOQ = sqrt((2 * $D * $S) / $H);
        $eoqResults[] = [
            'product_id' => $product['product_id'],
            'product_name' => $product['product_name'],
            'EOQ' => ceil($EOQ), // ปัดขึ้นเป็นจำนวนเต็ม
            'total_sold' => $D,
            'avg_cost_price' => $product['avg_cost_price'],
            'avg_sell_price' => $product['avg_sell_price'],
        ];
    }
}

// แบ่งหน้า
$itemsPerPage = 10;
$totalItems = count($eoqResults);
$totalPages = ceil($totalItems / $itemsPerPage);

// ตรวจสอบหมายเลขหน้าปัจจุบัน
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); // ตรวจสอบให้หมายเลขหน้าไม่เกินขอบเขต

// คำนวณตำแหน่งเริ่มต้นในการดึงข้อมูล
$offset = ($currentPage - 1) * $itemsPerPage;

// จำกัดให้แสดงผลแค่ 10 อันดับแรกในหน้าปัจจุบัน
$topEoqResults = array_slice($eoqResults, $offset, $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard กำไร</title>
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
                                            <div class="icon">
                                                <i class="ion ion-ios-pricetags-outline"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3> <?=$rowM['totalMember'];?> </h3>
                                                <p>สมาชิก</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-person-stalker"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3><?=$rowP['totalProduct'];?></h3>
                                                <p>สินค้า</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-bag"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-6">
                                        <div class="small-box bg-danger">
                                            <div class="inner">
                                                <h3> <?= $rowTodayOut['totalTodayOut'] ?: 0; ?> </h3>
                                                <p>นำสินค้าออกทั้งหมดของวันนี้</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-bag"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟกำไรรายวัน</h3>
                                        <canvas id="profitChart"></canvas>
                                    </div>
                                </div>

                                <!-- แสดงผล EOQ -->
                                 <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>ปริมาณคำสั่งซื้อที่เหมาะสม (EOQ)</h3>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อสินค้า</th>
                                                    <th>EOQ</th>
                                                    <th>ยอดขายรวม</th>
                                                    <th>ราคาเฉลี่ยต้นทุน</th>
                                                    <th>ราคาเฉลี่ยขาย</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($topEoqResults as $result): ?>
                                                <tr>
                                                    <td><?= $result['product_name']; ?></td>
                                                    <td><?= $result['EOQ']; ?></td>
                                                    <td><?= $result['total_sold']; ?></td>
                                                    <td><?= number_format($result['avg_cost_price'], 2); ?></td>
                                                    <td><?= number_format($result['avg_sell_price'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                        <!-- ปุ่มนำทางสำหรับหน้า EOQ -->
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination">
                                                <?php if ($currentPage > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $currentPage - 1; ?>">ก่อนหน้า</a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                    <li class="page-item <?= ($i === $currentPage) ? 'active' : ''; ?>">
                                                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                <?php if ($currentPage < $totalPages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $currentPage + 1; ?>">ถัดไป</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // กราฟกำไรรายวัน
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates); ?>,
            datasets: [{
                label: 'กำไรรายวัน (บาท)',
                data: <?= json_encode($profitValues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'วันที่'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'กำไร (บาท)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
