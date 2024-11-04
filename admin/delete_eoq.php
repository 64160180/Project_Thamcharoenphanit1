<?php 
require_once '../config/condb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];

    // ตรวจสอบว่ามี product_id หรือไม่
    if (!empty($productId)) {
        try {
            // ลบข้อมูลจาก tbl_order_eoq
            $stmt = $condb->prepare("DELETE FROM tbl_order_eoq WHERE product_id = :product_id");
            $stmt->bindParam(':product_id', $productId);

            if ($stmt->execute()) {
                // ลบสำเร็จ
                header("Location: main.php"); // เปลี่ยนเป็น URL ของหน้าแดชบอร์ดของคุณ
                exit;
            } else {
                echo "ลบไม่สำเร็จ";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "ไม่พบ product_id ที่ต้องการลบ";
    }
}
?>
