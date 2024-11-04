<?php
// ตรวจสอบว่าเซสชันยังไม่ได้เริ่มต้น
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/condb.php';

// คิวรีประวัติการสั่งซื้อ
$query = $condb->prepare("SELECT * FROM tbl_order ORDER BY date_out DESC");
$query->execute();
$orders = $query->fetchAll(PDO::FETCH_ASSOC);


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
                                        <th width="15%">วันที่</th>
                                        <th width="30%">ชื่อสินค้า</th>
                                        <th width="10%">ราคาทุน</th>
                                        <th width="10%">ราคาขาย</th>
                                        <th width="15%">ราคารวม</th>
                                        <th width="10%">กำไร</th> 
                                        <th width="10%">จำนวน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach ($orders as $order):
                                        $totalPrice = $order['sell_price'] * $order['quantity']; // คำนวณราคารวม
                                        $profit = ($order['sell_price'] - $order['cost_price']) * $order['quantity']; // คำนวณกำไร
                                        $dateTime = new DateTime($order['date_out'], new DateTimeZone('UTC')); // กำหนดเวลา TimeZone
                                        $dateTime->setTimezone(new DateTimeZone('Asia/Bangkok')); //เป็นเวลากรุงเทพ
                                    ?>
                                    <tr>
                                        <td><?= $dateTime->format('Y-m-d H:i:s'); ?></td>
                                        <td><?= $order['product_name']; ?></td>
                                        <td><?= number_format($order['cost_price'], 2); ?> บาท</td>
                                        <td><?= number_format($order['sell_price'], 2); ?> บาท</td>
                                        <td><?= number_format($totalPrice, 2); ?> บาท</td>
                                        <td><?= number_format($profit, 2); ?> บาท</td> <!-- แสดงกำไร -->
                                        <td><?= $order['quantity']; ?></td>
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


<!-- DELIMITER //

CREATE TRIGGER after_insert_tbl_order
AFTER INSERT ON tbl_order
FOR EACH ROW
BEGIN
    -- ตรวจสอบว่าข้อมูลที่เพิ่มใหม่มี quantity มากกว่า 0 หรือไม่
    IF NEW.quantity > 0 THEN
        -- เพิ่มข้อมูลใหม่ลงใน tbl_order_eoq
        INSERT INTO tbl_order_eoq (product_id, product_name, cost_price, sell_price, quantity)
        VALUES (NEW.product_id, NEW.product_name, NEW.cost_price, NEW.sell_price, NEW.quantity);
    END IF;
END //

DELIMITER ; -->
