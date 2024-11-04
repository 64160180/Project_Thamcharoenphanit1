<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <title>แก้ไขจำนวนขั้นต่ำ-ธรรมเจริญพาณิช</title>
                    <h1>ฟอร์มแก้ไขจำนวนขั้นต่ำ</h1>
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
                                <?php
                    
                                if (isset($_GET['id']) && !empty($_GET['id']) && $_GET['act'] == 'editmini') {
                                    $stmtMemberDetailsss = $condb->prepare("SELECT * FROM tbl_type WHERE type_id =?");
                                    $stmtMemberDetailsss->execute([$_GET['id']]);
                                    $row = $stmtMemberDetailsss->fetch(PDO::FETCH_ASSOC);
                                
                                    if (!$row) {
                                        echo "ไม่พบข้อมูลสมาชิกที่คุณกำลังค้นหา";
                                        exit;
                                    }
                                } else {
                                    echo "ไม่พบ ID";
                                    exit;
                                }
                                

                                ?>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-sm-2">แก้ไขจำนวนขั้นต่ำ</label>
                                        <div class="col-sm-4">
                                            <input type="number" name="type_minimum" class="form-control" min="0" max="999" value="<?php echo $row['type_minimum']; ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-4">
                                            <input type="hidden" name="type_id" value="<?php echo $_GET['id']; ?>">
                                            <button type="submit" class="btn btn-primary">ปรับปรุงข้อมูล</button>
                                            <a href="type.php" class="btn btn-danger">ยกเลิก</a>
                                        </div>
                                    </div>
                                </div> <!-- /.card-body -->
                            </form>
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
if (isset($_POST['type_minimum']) && isset($_POST['type_id'])) {
    try {
        // ประกาศตัวแปรรับค่าจากฟอร์ม
        $type_minimum = $_POST['type_minimum'];
        $type_id = $_POST['type_id'];

        // sql update
        $stmtUpdate = $condb->prepare("UPDATE tbl_type SET 
            type_minimum = :type_minimum
            WHERE type_id = :type_id
        ");

        // bindParam
        $stmtUpdate->bindParam(':type_id', $type_id, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':type_minimum', $type_minimum, PDO::PARAM_INT);

        $result = $stmtUpdate->execute();

        $condb = null; // close connect db

        if ($result) {
            echo '<script>
                setTimeout(function() {
                    swal({
                        title: "แก้ไขข้อมูลสำเร็จ",
                        type: "success"
                    }, function() {
                        window.location = "type.php";
                    });
                }, 1000);
            </script>';
        }
    } // try
    catch (Exception $e) {
        echo '<script>
            setTimeout(function() {
                swal({
                    title: "เกิดข้อผิดพลาด: ' . $e->getMessage() . '",
                    type: "error"
                }, function() {
                    window.location = "type.php"; //หน้าที่ต้องการให้กระโดดไป
                });
            }, 1000);
        </script>';
    } // catch
} // isset
?>