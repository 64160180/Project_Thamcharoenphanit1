<?php 
// คิวรี่ข้อมูลหมวดหมู่สินค้า
$queryType = $condb->prepare("SELECT * FROM tbl_type");
$queryType->execute();
$rsType = $queryType->fetchAll();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <title>เพิ่มข้อมูลสินค้า-ธรรมเจริญพาณิช</title>
                    <h1>เพิ่มข้อมูลสินค้า</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="card-body">
                        <div class="card card-primary">
                            <!-- form start -->
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label class="col-sm-2">หมวดหมู่สินค้า</label>
                                        <div class="col-sm-3">
                                            <select name="ref_type_id" class="form-control" required>
                                                <option value="">-- เลือกข้อมูล --</option>
                                                <?php foreach($rsType as $row){ ?>
                                                <option value="<?php echo $row['type_id']; ?>">-- <?php echo $row['type_name']; ?> --</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">ชื่อสินค้า</label>
                                        <div class="col-sm-7">
                                            <input type="text" name="product_name" class="form-control" required placeholder="ชื่อสินค้า">
                                        </div>
                                    </div>

                                    <!-- <div class="form-group row">
                                        <label class="col-sm-2">จำนวนสินค้า</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="product_qty" class="form-control" value="0" min="0" max="999" >
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">ราคาทุน</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="cost_price" class="form-control" value="0" min="0" max="99999"  >
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">ราคาสินค้า</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="product_price" class="form-control" value="0" min="0" max="99999"  >
                                        </div>
                                    </div> -->

                                    <div class="form-group row">
                                        <label class="col-sm-2">ภาพสินค้า</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="product_image" class="custom-file-input" id="exampleInputFile" accept="image/*">
                                                    <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        // ดักจับเหตุการณ์เมื่อมีการเปลี่ยนแปลงของ input type="file"
                                        document.getElementById('exampleInputFile').addEventListener('change', function () {
                                            // ตรวจสอบว่ามีไฟล์ที่ถูกเลือกหรือไม่
                                            if (this.files && this.files[0]) {
                                                // แสดงชื่อไฟล์ใน label
                                                this.nextElementSibling.textContent = this.files[0].name;
                                            }
                                        });
                                    </script>

                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
                                            <a href="product.php" class="btn btn-danger">ยกเลิก</a>
                                        </div>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php 
// เช็คค่าที่ส่งมาจากฟอร์ม
if(isset($_POST['product_name']) && isset($_POST['ref_type_id'])) {

    try {
        // ประกาศตัวแปรรับค่าจากฟอร์ม
        $ref_type_id = $_POST['ref_type_id'];
        $product_name = $_POST['product_name'];
        $product_price = 0;
        $product_qty = 0;
        $cost_price = 0;

        
        // เช็คชื่อสินค้าซ้ำในฐานข้อมูล
            $stmtCheckProduct = $condb->prepare("SELECT * FROM tbl_product WHERE product_name = :product_name");
            $stmtCheckProduct->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $stmtCheckProduct->execute();

            if($stmtCheckProduct->rowCount() > 0) {
                // ถ้าชื่อสินค้าซ้ำ
                echo '<script>
                    setTimeout(function() {
                    swal({
                        title: "ชื่อสินค้าซ้ำ",
                        text: "โปรดกรอกชื่อสินค้าที่ไม่ซ้ำ",
                        type: "error"
                    }, function() {
                        window.location = "product.php";
                    });
                    }, 1000);
                </script>';
            } else {
                // สร้างตัวแปรวันที่และสุ่มตัวเลข
                $date1 = date("Ymd_His");
                $numrand = (mt_rand());
                $upload = $_FILES['product_image']['name'];

                if($upload != '') {
                    // ตรวจสอบนามสกุลของไฟล์ที่อัพโหลด
                    $typefile = strrchr($_FILES['product_image']['name'], ".");
                    if($typefile == '.jpg' || $typefile == '.jpeg' || $typefile == '.png') {
                        // โฟลเดอร์ที่เก็บไฟล์
                        $path = "../assets/product_img/";
                        // ตั้งชื่อไฟล์ใหม่
                        $newname = $numrand . $date1 . $typefile;
                        $path_copy = $path . $newname;
                        // คัดลอกไฟล์ไปยังโฟลเดอร์
                        move_uploaded_file($_FILES['product_image']['tmp_name'], $path_copy);

                        // SQL insert
                        $stmtInsertProduct = $condb->prepare("INSERT INTO tbl_product 
                        (
                            ref_type_id,
                            product_name,
                            product_qty,
                            product_price,
                            cost_price,
                            product_image                           
                        )
                        VALUES
                        (
                            :ref_type_id,
                            :product_name,
                            :product_qty,
                            :product_price,
                            :cost_price,
                            :product_image
                        )");
                  // bindParam
                        $stmtInsertProduct->bindParam(':ref_type_id', $ref_type_id, PDO::PARAM_INT);
                        $stmtInsertProduct->bindParam(':product_name', $product_name, PDO::PARAM_STR);
                        $stmtInsertProduct->bindParam(':product_qty', $product_qty, PDO::PARAM_INT);
                        $stmtInsertProduct->bindParam(':product_price', $product_price, PDO::PARAM_STR);
                        $stmtInsertProduct->bindParam(':cost_price', $cost_price, PDO::PARAM_STR);
                        $stmtInsertProduct->bindParam(':product_image', $newname, PDO::PARAM_STR);
                        $result = $stmtInsertProduct->execute();
                        $condb = null; // ปิดการเชื่อมต่อฐานข้อมูล

                        if($result) {
                            echo '<script>
                                setTimeout(function() {
                                swal({
                                    title: "เพิ่มข้อมูลสำเร็จ",
                                    type: "success"
                                }, function() {
                                    window.location = "product.php";
                                });
                                }, 1000);
                            </script>';
                        } //if
                    } else {
                        echo '<script>
                            setTimeout(function() {
                            swal({
                                title: "คุณอัพโหลดไฟล์ไม่ถูกต้อง",
                                type: "error"
                            }, function() {
                                window.location = "product.php";
                            });
                            }, 1000);
                        </script>';
                    } //else เช็ตสกุลไฟล์
                }//if upload
            } //else ตรวจสอบชื่อสินค้า
       // } //else ตรวจสอบค่าที่กรอก
    } //try
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
    } //catch
} //if isset
