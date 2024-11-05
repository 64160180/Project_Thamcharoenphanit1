<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <title>อัพเดทสินค้าใหม่เข้าระบบ-ธรรมเจริญพาณิช</title>
          <h1>อัพเดทสินค้าใหม่เข้าระบบ</h1>
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
            <div class="card-body">
              <h1>ของเข้า</h1>
              
              <div class="row">
                <div class="col-md-3">
                  <form action="" method="post">
                    <?php
                    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
                    include('../config/condb.php'); // ปรับให้ตรงกับไฟล์การเชื่อมต่อของคุณ

                    // เพิ่มข้อมูลเมื่อกดปุ่ม submit
                    if (isset($_POST['receive'])) {
                        // รับค่าจากฟอร์ม
                        $ref_type_id = $_POST['ref_type_id'];
                        $cost_price = $_POST['cost_price'];
                        $selling_price = $_POST['product_price'];
                        $quantity = $_POST['product_qty'];
                    
                        // แยก id และชื่อสินค้าออกจาก $ref_type_id
                        list($product_id, $product_name) = explode(":", $ref_type_id);
                    
                        // ตรวจสอบให้แน่ใจว่าทุกค่ามีการรับ
                        if (empty($cost_price) || empty($selling_price) || empty($quantity)) {
                            echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน');</script>";
                        } else {
                            try {
                                // เตรียมคำสั่ง SQL เพื่อเพิ่มข้อมูลลงใน tbl_newproduct
                                $queryInsert = $condb->prepare("INSERT INTO tbl_newproduct (newproduct_name, newcost_price, newproduct_price, newproduct_qty) VALUES (:product_name, :cost_price, :product_price, :product_qty)");

                                // ผูกค่าตัวแปรที่ได้รับจากฟอร์มกับคำสั่ง SQL
                                $queryInsert->bindParam(':product_name', $product_name);
                                $queryInsert->bindParam(':cost_price', $cost_price);
                                $queryInsert->bindParam(':product_price', $selling_price);
                                $queryInsert->bindParam(':product_qty', $quantity);

                                // เรียกใช้คำสั่ง SQL
                                if ($queryInsert->execute()) {
                                    // อัปเดตข้อมูลใน tbl_product
                                    $queryUpdate = $condb->prepare("UPDATE tbl_product SET product_qty = product_qty + :product_qty, cost_price = :cost_price, product_price = :product_price WHERE id = :id");
                                    $queryUpdate->bindParam(':id', $product_id);
                                    $queryUpdate->bindParam(':cost_price', $cost_price);
                                    $queryUpdate->bindParam(':product_price', $selling_price);
                                    $queryUpdate->bindParam(':product_qty', $quantity);

                                    if ($queryUpdate->execute()) {
                                        echo '<script>
                                            setTimeout(function() {
                                                swal({
                                                    title: "เพิ่มข้อมูลสำเร็จ",
                                                    type: "success"
                                                }, function() {
                                                    window.location = "product_addnew.php"; //หน้าที่ต้องการให้กระโดดไป
                                                });
                                            }, 1000);
                                        </script>';
                                    } else {
                                        throw new Exception("เกิดข้อผิดพลาดในการอัปเดตข้อมูลใน tbl_product");
                                    }
                                } else {
                                    throw new Exception("เกิดข้อผิดพลาดในการเพิ่มข้อมูลใน tbl_newproduct");
                                }
                            } catch (Exception $e) {
                                echo '<script>
                                    setTimeout(function() {
                                        swal({
                                            title: "เกิดข้อผิดพลาด",
                                            type: "error"
                                        }, function() {
                                            window.location = "product_addnew.php"; //หน้าที่ต้องการให้กระโดดไป
                                        });
                                    }, 1000);
                                </script>';
                            }
                        }
                    }

                    // คิวรี่ข้อมูลสินค้าจาก tbl_product
                    $queryProduct = $condb->prepare("SELECT id, product_name FROM tbl_product");
                    $queryProduct->execute();
                    $rsProduct = $queryProduct->fetchAll();
                    ?>
                    
                    <select name="ref_type_id" class="form-control" required>
                      <option value="">-- เลือกข้อมูล --</option>
                      <?php foreach ($rsProduct as $row) { ?>
                        <option value="<?php echo $row['id'] . ":" . $row['product_name']; ?>">-- <?php echo htmlspecialchars($row['product_name']); ?> --</option>
                      <?php } ?>
                    </select>
                    <input type="text" name="cost_price" class="form-control" placeholder="ต้นทุน" required>
                    <input type="text" name="product_price" class="form-control" placeholder="ขาย" required>
                    <input type="text" name="product_qty" class="form-control" placeholder="จำนวน" required>
                    <button type="submit" name="receive" value="receive" class="btn btn-primary">เพิ่มข้อมูล</button>
                  </form>
                </div>
              </div>

              <table id="example1" class="table table-bordered table-striped table-sm">
                <thead>
                  <tr class="table-info">
                    <th width="5%" class="text-center">No.</th>
                    <th width="20%">ชื่อสินค้า</th>
                    <th width="10%" class="text-center">เวลานำเข้าสินค้า</th>
                    <th width="10%" class="text-center">ราคาทุน</th>
                    <th width="10%" class="text-center">ราคาสินค้า</th>
                    <th width="10%" class="text-center">จำนวน</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                // คิวรี่ข้อมูลสินค้า
                $queryproduct = $condb->prepare("SELECT dateCreate, newproduct_name, newcost_price, newproduct_price, newproduct_qty FROM tbl_newproduct");
                $queryproduct->execute();
                $rsproduct = $queryproduct->fetchAll();
                $i = 1;
                
                foreach ($rsproduct as $row) { 
                    $dateTime = new DateTime($row['dateCreate'], new DateTimeZone('UTC'));
                    $dateTime->setTimezone(new DateTimeZone('Asia/Bangkok'));
                ?>
                    <tr>
                    <td align="center"> <?php echo $i++ ?> </td>
                    <td><?php echo htmlspecialchars($row['newproduct_name']); ?></td>
                    <td><?php echo $dateTime->format('Y-m-d H:i:s'); ?></td>
                    <td align="right"><?php echo number_format($row['newcost_price'], 2); ?></td>
                    <td align="right"><?php echo number_format($row['newproduct_price'], 2); ?></td>
                    <td align="right"><?php echo number_format($row['newproduct_qty'], 2); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
