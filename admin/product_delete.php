<?php 
if(isset($_GET['id']) && $_GET['act']=='delete'){

    //trigger exception in a "try" block
    try {
        $id = $_GET['id'];

        // ดึงข้อมูลเพื่อใช้บันทึกในประวัติการลบ
        $stmtProductDetail = $condb->prepare("SELECT id, product_name, product_image FROM tbl_product WHERE id=?");
        $stmtProductDetail->execute([$id]);
        $row = $stmtProductDetail->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบว่าพบข้อมูลหรือไม่
        if($stmtProductDetail->rowCount() == 0){
            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "เกิดข้อผิดพลาด",
                        type: "error"
                    }, function() {
                        window.location = "product.php";
                    });
                }, 1000);
            </script>';
        } else {
           

            // ลบข้อมูลออกจาก tbl_product
            $stmtDelProduct = $condb->prepare('DELETE FROM tbl_product WHERE id=:id');
            $stmtDelProduct->bindParam(':id', $id , PDO::PARAM_INT);
            $stmtDelProduct->execute();

            $condb = null; // ปิดการเชื่อมต่อฐานข้อมูล

            // ตรวจสอบว่าลบข้อมูลสำเร็จหรือไม่
            if($stmtDelProduct->rowCount() == 1){
                // ลบไฟล์ภาพ
                unlink('../assets/product_img/'.$row['product_image']);

                echo '<script>
                    setTimeout(function() {
                        swal({
                            title: "ลบข้อมูลสำเร็จ",
                            type: "success"
                        }, function() {
                            window.location = "product.php";
                        });
                    }, 1000);
                </script>';
            }
        }
        
    } 
    // catch exception
    catch(Exception $e) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    type: "error"
                }, function() {
                    window.location = "product.php";
                });
            }, 1000);
        </script>';
    } 
}
?>
