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

// คิวรีเพื่อดึงข้อมูลรายรับและกำไร
$queryRevenueAndProfit = $condb->prepare("
    SELECT 
        DATE(date_out) AS order_date,
        SUM(sell_price * quantity) AS revenue,
        SUM((sell_price - cost_price) * quantity) AS profit
    FROM tbl_order 
    GROUP BY DATE(date_out) 
    ORDER BY order_date ASC
");
$queryRevenueAndProfit->execute();
$results = $queryRevenueAndProfit->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับกราฟ
$dates = [];
$revenueValues = [];
$profitValues = [];
foreach ($results as $result) {
    $dates[] = $result['order_date'];
    $revenueValues[] = $result['revenue'];
    $profitValues[] = $result['profit'];
}

// ดึงข้อมูลยอดขายของสินค้าทั้งหมด
$query = $condb->prepare("
    SELECT 
        product_id,
        product_name,
        SUM(quantity) AS total_sold,
        AVG(cost_price) AS avg_cost_price,
        AVG(sell_price) AS avg_sell_price
    FROM tbl_order_eoq
    GROUP BY product_id, product_name
");
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// กำหนดค่าคงที่สำหรับการคำนวณ EOQ
$eoqResults = [];
foreach ($products as $product) {
    $D = $product['total_sold'] ?: 0; // ความต้องการสินค้า
    $S = $product['avg_cost_price'];  // ใช้ราคาทุนเป็นต้นทุนการสั่งซื้อ
    $H = $product['avg_sell_price'] - $product['avg_cost_price']; // ใช้กำไรเป็นต้นทุนการเก็บสินค้า
    
    if ($D > 0 && $H > 0) {  // ตรวจสอบให้แน่ใจว่า H ไม่เป็น 0
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
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                        <h3>กราฟรายรับและกำไรรายวัน</h3>
                                        <canvas id="profitChart"></canvas>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- แสดงผล EOQ -->
                                <section class="content">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h3>ปริมาณคำสั่งซื้อที่เหมาะสม (EOQ)</h3>

                                                                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                                                                <div class="alert alert-success" role="alert">
                                                                    ลบข้อมูล EOQ สำเร็จ!
                                                                </div>
                                                                <?php endif; ?>

                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>ชื่อสินค้า</th>
                                                                            <th>จำนวนที่ต้องสั่งซื้อ</th>
                                                                            <th>ยอดขายรวม</th>
                                                                            <th>ราคาต้นทุน</th>
                                                                            <th>ราคาขาย</th>
                                                                            <th>ดำเนินการ</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($topEoqResults as $result): ?>
                                                                        <tr>
                                                                            <td><?= $result['product_name']; ?></td>
                                                                            <td><?= $result['EOQ']; ?></td>
                                                                            <td><?= $result['total_sold']; ?></td>
                                                                            <td><?= number_format($result['avg_cost_price'], 2); ?>
                                                                            </td>
                                                                            <td><?= number_format($result['avg_sell_price'], 2); ?>
                                                                            </td>
                                                                            <td>
                                                                                <form action="delete_eoq.php"
                                                                                    method="POST"
                                                                                    style="display:inline;">
                                                                                    <input type="hidden"
                                                                                        name="product_id"
                                                                                        value="<?= $result['product_id']; ?>">
                                                                                    <button type="submit"
                                                                                        class="btn btn-danger btn-sm"
                                                                                        onclick="return confirm('คุณต้องการลบข้อมูลหรือไม่');">ลบ</button>
                                                                                </form>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>

                                                                <!-- แสดงตัวแบ่งหน้า -->
                                                                <nav aria-label="Page navigation">
                                                                    <ul class="pagination">
                                                                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                                                        <li
                                                                            class="page-item <?= $page === $currentPage ? 'active' : ''; ?>">
                                                                            <a class="page-link"
                                                                                href="?page=<?= $page; ?>"><?= $page; ?></a>
                                                                        </li>
                                                                        <?php endfor; ?>
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
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates); ?>,
            datasets: [{
                label: 'รายรับ',
                data: <?= json_encode($revenueValues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
            }, {
                label: 'กำไร',
                data: <?= json_encode($profitValues); ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: false,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php endif; ?>
</html>