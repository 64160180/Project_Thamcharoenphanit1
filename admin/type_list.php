<?php
// คิวรี่ข้อมูลสมาชิก
$querytype = $condb->prepare("SELECT * FROM tbl_type");
$querytype->execute();
$rstype = $querytype->fetchAll();
?>  

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <title>จัดการข้อมูลหมวดหมู่สินค้า-ธรรมเจริญพาณิช</title>
                    <h1>จัดการข้อมูลหมวดหมู่สินค้า
                    <a href="type.php?act=add" class="btn btn-primary">+เพิ่มข้อมูล</a>
                    </h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr class="table-info">
                                        <th width="5%" class="text-center">No.</th>
                                        <th width="75%">หมวดหมู่สินค้า</th>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                                            <th width="10%" class="text-center">แก้ไขขั้นต่ำสินค้า</th>
                                            <th width="5%" class="text-center">แก้ไข</th> 
                                            <th width="5%" class="text-center">ลบ</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1; // เริ่มต้นหมายเลข
                                    foreach ($rstype as $row) { ?>
                                        <tr>
                                            <td align="center"><?php echo $i++; ?></td>
                                            <td><?= $row['type_name']; ?></td>
                                            
                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                                            <td align="center">
                                            <a href="type.php?id=<?= $row['type_id']; ?>&act=editmini" class="btn btn-primary btn-sm">แก้ไขจำนวนขั้นต่ำ</a>
                                            </td>
                                            <?php } ?>

                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                                                <td align="center"> 
                                                    
                                                    <a href="type.php?id=<?= $row['type_id']; ?>&act=edit" class="btn btn-warning btn-sm">แก้ไข</a>
                                                </td>
                                            <?php } ?>

                                            
                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                                                <td align="center">
                                                    <a href="type.php?id=<?= $row['type_id']; ?>&act=delete" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบข้อมูล??');">ลบ</a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
