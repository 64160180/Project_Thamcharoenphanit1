<?php
// ตรวจสอบว่าเซสชันยังไม่ได้เริ่มต้น
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/condb.php';

// ตัวแปรสำหรับระบุเดือนและปี (ตัวอย่าง: เดือนพฤศจิกายน ปี 2024)
$month = date('m'); // เดือนปัจจุบัน
$year = date('Y');  // ปีปัจจุบัน

// คิวรีเพื่อคำนวณรายได้และกำไร
$queryRevenueAndProfit = $condb->prepare("
  SELECT 
    o.date_out, -- แสดงวันที่และเวลา
    o.product_name, -- เลือกชื่อสินค้า
    o.sell_price, -- เพิ่มราคาขาย
    SUM(o.sell_price * o.quantity) AS revenue,
    SUM(o.quantity) AS total_quantity,
    SUM((o.sell_price - o.historical_cost) * o.quantity) AS profit, -- ใช้ historical_cost
    o.historical_cost -- ใช้ historical_cost จาก tbl_order
FROM tbl_order o
WHERE YEAR(o.date_out) = :year AND MONTH(o.date_out) = :month
GROUP BY o.date_out, o.product_name, o.historical_cost, o.sell_price
ORDER BY o.date_out ASC, o.product_name ASC

");

// Binding parameters
$queryRevenueAndProfit->bindParam(':month', $month, PDO::PARAM_STR);
$queryRevenueAndProfit->bindParam(':year', $year, PDO::PARAM_STR);

$queryRevenueAndProfit->execute();
$results = $queryRevenueAndProfit->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการนำออกสินค้า-ธรรมเจริญพาณิช</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>ประวัติการนำออกสินค้า</h1>
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
                                <table id="example1" class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr class="table-info">
                                            <th width="">วันที่</th>
                                            <th width="">ชื่อสินค้า</th>
                                            <th width="">ราคาขาย</th>
                                            <th width="">ค่าเฉลี่ยต้นทุน</th> <!-- ใช้ historical_cost -->
                                            <th width="">กำไรรวม</th>
                                            <th width="">รายได้รวม</th>
                                            <th width="">จำนวนสินค้ารวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($results as $result):
                                            $dateTime = new DateTime($result['date_out'], new DateTimeZone('UTC')); // กำหนดเวลา TimeZone
                                            $dateTime->setTimezone(new DateTimeZone('Asia/Bangkok')); // เป็นเวลากรุงเทพ
                                        ?>
                                        <tr>
                                            <td><?= $dateTime->format('Y-m-d H:i:s'); ?></td> <!-- แสดงทั้งวันที่และเวลา -->
                                            <td><?= $result['product_name']; ?></td>
                                            <td><?= number_format($result['sell_price'] ?? 0, 2); ?> บาท</td>
                                            <td><?= number_format($result['historical_cost'] ?? 0, 2); ?> บาท</td>
                                            <td><?= number_format($result['profit'] ?? 0, 2); ?> บาท</td>
                                            <td><?= number_format($result['revenue'] ?? 0, 2); ?> บาท</td>
                                            <td><?= number_format($result['total_quantity'] ?? 0, 0); ?></td>

                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
