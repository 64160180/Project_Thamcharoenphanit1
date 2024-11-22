
<!DOCTYPE html>
<html lang="th">
<head>
    <title>รถเข็นสินค้า-ธรรมเจริญพาณิช</title>
</head>

<?php
// ตรวจสอบว่ามี session ที่ถูกเริ่มต้นแล้วหรือยัง หากยังให้เริ่มต้น session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/condb.php';


// ตรวจสอบการเพิ่ม/ลดจำนวนหรือการลบสินค้า
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        // อัปเดตจำนวนสินค้า
        $productId = $_POST['product_id'];
        $newQuantity = $_POST['quantity'];

        // คิวรีจำนวนสินค้าที่มีในระบบ
        $stmt = $condb->prepare("SELECT product_qty FROM tbl_product WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $availableQty = $product['product_qty']; // จำนวนสินค้าที่มีในระบบ

            if ($newQuantity > $availableQty) {
                echo "<div class='alert alert-danger text-center' role='alert'>
                        <strong>❌ คุณเลือกจำนวนสินค้ามากกว่าจำนวนที่มีอยู่ในสต็อก!!!</strong>
                      </div>";
            } elseif ($newQuantity > 0) {
                $_SESSION['cart'][$productId] = $newQuantity;
            } else {
                unset($_SESSION['cart'][$productId]); // ลบสินค้าออกหากจำนวนเป็น 0 หรือติดลบ
            }
        }
    } elseif (isset($_POST['remove_product'])) {
        // ลบสินค้าจากรถเข็น
        $productId = $_POST['product_id'];
        unset($_SESSION['cart'][$productId]); // ลบสินค้าที่มี id ตรงกับ $productId จาก session 'cart'
    }
} //เพิ่มลด/อัพเดท/ลบ


// ตรวจสอบว่ารถเข็นมีสินค้าอยู่หรือไม่

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<div class='alert alert-warning text-center p-5' role='alert'>
            <div class='d-flex justify-content-center'>
                <div class='alert-icon'>
                    <i class='bi bi-cart-x' style='font-size: 4rem;'></i>
                </div>
            </div>
            <h4 class='alert-heading mb-3'>🚫 รถเข็นของคุณยังไม่มีสินค้า</h4>
            <p class='lead mb-4'>กรุณาไปที่หน้าสินค้าเพื่อเลือกสินค้าเพิ่มเติม!</p>
            <p>
                <a href='product.php' class='btn btn-primary btn-lg mt-3 px-4 py-2' role='button'>
                    ไปยังหน้าสินค้า
                </a>
            </p>
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
    <title>รถเข็นสินค้า-ธรรมเจริญพาณิช</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <th>ราคาขาย</th>
                    <th>ราคารวม</th>
                    <th>จำนวน</th>
                    <th>การจัดการ</th>
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
                    
                    // เพิ่มยอดรวมราคาทุนและราคารวม
                    $totalCost += $costPriceTotal;
                    $totalPriceAll += $totalPrice;
                ?>
                <tr>
                    <td><img src="../assets/product_img/<?= $product['product_image']; ?>" class="img-thumbnail" width="70px"></td>
                    <td><?= $product['product_name']; ?></td>
                    <td><?= number_format($product['product_price'], 2); ?> บาท</td>
                    <td><?= number_format($totalPrice, 2); ?> บาท</td>
                    <td>
                        <form action="" method="post" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <input type="number" name="quantity" value="<?= $quantity; ?>" min="1" class="form-control d-inline w-50">
                            <button type="submit" name="update_quantity" class="btn btn-warning btn-sm">อัปเดต</button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="post" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                            <button type="submit" name="remove_product" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-3"></div>
        
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
