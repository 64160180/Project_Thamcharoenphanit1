
<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// เช็คการเข้าสู่ระบบ
if(empty($_SESSION['id']) && empty($_SESSION['name']) && empty($_SESSION['surname'])) {
    echo '<script>
        alert("คุณไม่มีสิทธิ์ใช้งานหน้านี้");
        window.location = "../index.php";
    </script>';
    exit();
}

// รวมไฟล์ที่จำเป็น
include 'header.php'; // รวมส่วนหัว
include 'navbar.php'; // รวมแถบนำทาง
include 'sidebar_menu.php'; // รวมเมนูด้านข้าง
 // include 'calendar_form_add.php'; // รวมส่วนท้าย
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>เพิ่มรายงานปฏิทิน</h1>
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
                            <form action="" method="post">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label class="col-sm-2">ชื่อผู้สั่งซื้อ</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="title" class="form-control" required
                                                placeholder="ชื่อผู้สั่งซื้อ">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label class="col-sm-2">เลือกสินค้า</label>
                                    <div class="col-sm-3">
                                        <select name="product_id" class="form-control" required>
                                            <option value="">-- เลือกสินค้า --</option>
                                            <?php
                                            // ดึงข้อมูลสินค้าจากฐานข้อมูล
                                            $stmtProducts = $condb->prepare("SELECT id, product_name FROM tbl_product");
                                            $stmtProducts->execute();
                                            $rsProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

                                            foreach($rsProducts as $row) { ?>
                                                <option value="<?php echo $row['id']; ?>">
                                                    <?php echo $row['product_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                        <label class="col-sm-2">วันที่สั่ง</label>
                                        <div class="col-sm-4">
                                            <input type="date" name="start" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2">วันกำหนดส่ง</label>
                                        <div class="col-sm-4">
                                            <input type="date" name="end" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">เวลากำหนดส่ง</label>
                                        <div class="col-sm-4">
                                            <input type="time" name="end" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary">เพิ่มรายงานปฏิทิน</button>
                                            <a href="calendar.php" class="btn btn-danger">ยกเลิก</a>
                                        </div>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<!-- /.col-->
</div>

<!-- ./row -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php 
if(isset($_POST['title']) && isset($_POST['start']) && isset($_POST['end'])) {
    try {
        //ประกาศตัวแปรรับค่าจากฟอร์ม
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];

        //sql insert
        $stmtInsertEvent = $condb->prepare("INSERT INTO tbl_event (title, start, end) VALUES (:title, :start, :end)");

        //bindParam
        $stmtInsertEvent->bindParam(':title', $title, PDO::PARAM_STR);
        $stmtInsertEvent->bindParam(':start', $start, PDO::PARAM_STR);
        $stmtInsertEvent->bindParam(':end', $end, PDO::PARAM_STR);
        $result = $stmtInsertEvent->execute();

        $condb = null; //close connect db

        if($result){
            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "เพิ่มกิจกรรมสำเร็จ",
                        type: "success"
                    }, function() {
                        window.location = "calendar.php"; //หน้าที่ต้องการให้กระโดดไป
                    });
                }, 1000);
            </script>';
        }
    } //เช็คข้อมูลซ้ำ
    catch(Exception $e) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด",
                    type: "error"
                }, function() {
                    window.location = "calendar.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
        </script>';
    }
}
?>
