<?php
// ตรวจสอบว่ามี session ที่ถูกเริ่มต้นแล้วหรือยัง หากยังให้เริ่มต้น session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/condb.php';

// แสดงข้อผิดพลาด (สำหรับการพัฒนา)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// ตรวจสอบว่ารถเข็นมีสินค้าอยู่หรือไม่
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div class='alert alert-warning text-center' role='alert'>
            <h4 class='alert-heading'>🚫 รถเข็นของคุณยังไม่มีสินค้า</h4>
            <p class='mb-0'>กรุณาไปที่หน้าสินค้าเพื่อเลือกสินค้าเพิ่มเติม!</p>
            <p><a href='product.php' class='btn btn-primary mt-3'>กลับไปยังหน้าสินค้า</a></p>
          </div>";
    exit();
}

// สร้างรายการสินค้าที่อยู่ในรถเข็น
$cartItems = $_SESSION['cart'];
$productIds = implode(',', array_keys($cartItems));

// คิวรีข้อมูลสินค้าจากฐานข้อมูลตาม ID ที่อยู่ในรถเข็น
$query = $condb->prepare("SELECT * FROM tbl_product WHERE id IN ($productIds)");
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รถเข็นสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/admin/css/cart.css"> 
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">รถเข็นสินค้า</h2>
        <table class="table table-bordered table-striped">
            <thead>
            <tr class="table-info">
                    <th>ภาพสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>ราคาทุน</th>
                    <th>ราคาขาย</th>
                    <th>ราคารวม</th>
                    <th>กำไร</th>
                    <th>จำนวน</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalCost = 0; // กำหนดยอดรวมราคาทุนเริ่มต้นเป็น 0
                $totalPriceAll = 0; // กำหนดยอดรวมราคารวมเริ่มต้นเป็น 0
                
                foreach ($products as $product): 
                    $quantity = $cartItems[$product['id']];
                    $totalPrice = $product['product_price'] * $quantity; // คำนวณราคารวม
                    $costPriceTotal = $product['cost_price'] * $quantity; // คำนวณราคาทุนรวม
                    $profit = $totalPrice - $costPriceTotal; // คำนวณกำไร
                    
                    // เพิ่มยอดรวมราคาทุนและราคารวม
                    $totalCost += $costPriceTotal;
                    $totalPriceAll += $totalPrice;
                ?>
                <tr>
                    <td><img src="../assets/product_img/<?= $product['product_image']; ?>" class="img-thumbnail" width="70px"></td>
                    <td><?= $product['product_name']; ?></td>
                    <td><?= number_format($costPriceTotal, 2); ?> บาท</td>
                    <td><?= number_format($product['product_price'], 2); ?> บาท</td>
                    <td><?= number_format($totalPrice, 2); ?> บาท</td>
                    <td><?= number_format($profit, 2); ?> บาท</td>
                    <td><?= $quantity; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h3>ยอดรวมทั้งหมด: <?= number_format($totalPriceAll, 2); ?> บาท</h3>
            <h3>ยอดรวมราคาทุน: <?= number_format($totalCost, 2); ?> บาท</h3>
            <h3>กำไรทั้งหมด: <?= number_format($totalPriceAll - $totalCost, 2); ?> บาท</h3>
        </div>
        
        <div class="text-center mt-4">
            <a href="product.php" class="btn btn-primary">กลับไปยังหน้าสินค้า</a>
            <form action="payment.php" method="post" class="d-inline">
                <input type="hidden" name="cart" value="<?= htmlspecialchars(serialize($cartItems)); ?>">
                <button type="submit" class="btn btn-success">ดำเนินการนำออก</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
