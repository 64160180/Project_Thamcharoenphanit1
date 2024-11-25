<?php 
require_once '../config/condb.php';

// คิวรีเพื่อดึงข้อมูลรายรับรายเดือน
$queryMonthlyRevenue = $condb->prepare("
    SELECT 
        DATE_FORMAT(date_out, '%Y-%m') AS order_month,
        SUM(sell_price * quantity) AS revenue
    FROM tbl_order 
    GROUP BY order_month 
    ORDER BY order_month ASC
");
$queryMonthlyRevenue->execute();
$monthlyRevenues = $queryMonthlyRevenue->fetchAll(PDO::FETCH_ASSOC);

// คิวรีเพื่อดึงข้อมูลกำไรรายเดือน
$queryMonthlyProfits = $condb->prepare("
    SELECT 
        DATE_FORMAT(o.date_out, '%Y-%m') AS order_month,
        SUM((o.sell_price - o.historical_cost) * o.quantity) AS profit
    FROM tbl_order o
    JOIN (
        SELECT 
            newproduct_name,
            AVG(newcost_price) AS average_cost  -- ใช้ค่าเฉลี่ย (AVG) ของ newcost_price
        FROM tbl_newproduct 
        GROUP BY newproduct_name
    ) np ON o.product_name = np.newproduct_name
    GROUP BY order_month
    ORDER BY order_month ASC
");
$queryMonthlyProfits->execute();
$monthlyProfits = $queryMonthlyProfits->fetchAll(PDO::FETCH_ASSOC);

// คิวรีเพื่อดึงข้อมูลรายจ่ายรายเดือนจาก tbl_newproduct

$queryMonthlyExpenses = $condb->prepare("
    SELECT 
        DATE_FORMAT(dateCreate, '%Y-%m') AS order_month,
        SUM(newcost_price * newproduct_qty) AS expenses
    FROM tbl_newproduct 
    GROUP BY order_month 
    ORDER BY order_month ASC
");

$queryMonthlyExpenses->execute();
$monthlyExpenses = $queryMonthlyExpenses->fetchAll(PDO::FETCH_ASSOC);



// คิวรีเพื่อดึงข้อมูลรายรับรายปี
$queryYearlyRevenue = $condb->prepare("
    SELECT 
        DATE_FORMAT(date_out, '%Y') AS order_year,
        SUM(sell_price * quantity) AS revenue
    FROM tbl_order 
    GROUP BY order_year 
    ORDER BY order_year ASC
");
$queryYearlyRevenue->execute();
$yearlyRevenues = $queryYearlyRevenue->fetchAll(PDO::FETCH_ASSOC);

// คิวรีเพื่อดึงข้อมูลกำไรรายปี
$queryYearlyProfits = $condb->prepare("
    SELECT 
        YEAR(o.date_out) AS order_year,
        SUM((o.sell_price - o.historical_cost) * o.quantity) AS profit
    FROM tbl_order o
    JOIN (
        SELECT 
            newproduct_name,
            (SUM(newcost_price * newproduct_qty) / SUM(newproduct_qty)) AS average_cost
        FROM tbl_newproduct 
        GROUP BY newproduct_name
    ) np ON o.product_name = np.newproduct_name
    GROUP BY order_year
    ORDER BY order_year ASC
");
$queryYearlyProfits->execute();
$yearlyProfits = $queryYearlyProfits->fetchAll(PDO::FETCH_ASSOC);


// คิวรีเพื่อดึงข้อมูลรายจ่ายรายปีจาก tbl_newproduct
$queryYearlyExpenses = $condb->prepare("
    SELECT 
        DATE_FORMAT(dateCreate, '%Y') AS order_year,
        SUM(newcost_price * newproduct_qty) AS expenses
    FROM tbl_newproduct 
    GROUP BY order_year 
    ORDER BY order_year ASC
");
$queryYearlyExpenses->execute();
$yearlyExpenses = $queryYearlyExpenses->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับกราฟรายเดือน
$monthlyDates = array_column($monthlyRevenues, 'order_month');
$monthlyRevenueValues = array_column($monthlyRevenues, 'revenue');
$monthlyProfitValues = array_column($monthlyProfits, 'profit');
$monthlyExpenseValues = array_column($monthlyExpenses, 'expenses');

// เตรียมข้อมูลสำหรับกราฟรายปี
$yearlyDates = array_column($yearlyRevenues, 'order_year');
$yearlyRevenueValues = array_column($yearlyRevenues, 'revenue');
$yearlyProfitValues = array_column($yearlyProfits, 'profit');
$yearlyExpenseValues = array_column($yearlyExpenses, 'expenses');

// ดึงข้อมูลสินค้าจาก `tbl_order` ที่ขายออกในเดือนและปีที่กำหนด
function getMonthlyOrder($condb, $month, $year) {
    $queryMonthlyOrder = $condb->prepare("
        SELECT 
            product_id, -- แก้ไขให้ใช้ product_id
            sell_price,
            quantity AS sell_qty
        FROM tbl_order
        WHERE MONTH(date_out) = :month AND YEAR(date_out) = :year
        ORDER BY date_out DESC
    ");
    $queryMonthlyOrder->bindParam(':month', $month, PDO::PARAM_INT);
    $queryMonthlyOrder->bindParam(':year', $year, PDO::PARAM_INT);
    $queryMonthlyOrder->execute();
    return $queryMonthlyOrder->fetchAll(PDO::FETCH_ASSOC); // ดึงข้อมูลหลายแถว
}

// ดึงข้อมูลสินค้าที่ค้างอยู่ในคลัง
function getStockData($condb, $productId) {
    $queryStock = $condb->prepare("
        SELECT 
            product_qty AS current_qty,
            cost_price AS current_cost_price
        FROM tbl_product
        WHERE id = :product_id
    ");
    $queryStock->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $queryStock->execute();
    return $queryStock->fetch(PDO::FETCH_ASSOC);
}

// ดึงข้อมูลสินค้าที่เพิ่มเข้ามาใหม่
function getNewStockData($condb, $productId) {
    $queryNewStock = $condb->prepare("
        SELECT 
            SUM(newproduct_qty) AS new_qty,
            AVG(newcost_price) AS new_cost_price
        FROM tbl_newproduct
        WHERE id = :product_id -- ใช้ id แทน ref_product_id
    ");
    $queryNewStock->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $queryNewStock->execute();
    return $queryNewStock->fetch(PDO::FETCH_ASSOC);
}



?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard-ธรรมเจริญพาณิช</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard รายรับ-จ่ายและกำไร </h1>

                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label for="monthSelector">เลือกเดือน</label>
                        <select id="monthSelector" class="form-control">
                            <option value="">ทุกเดือน</option>
                            <option value="01">มกราคม</option>
                            <option value="02">กุมภาพันธ์</option>
                            <option value="03">มีนาคม</option>
                            <option value="04">เมษายน</option>
                            <option value="05">พฤษภาคม</option>
                            <option value="06">มิถุนายน</option>
                            <option value="07">กรกฎาคม</option>
                            <option value="08">สิงหาคม</option>
                            <option value="09">กันยายน</option>
                            <option value="10">ตุลาคม</option>
                            <option value="11">พฤศจิกายน</option>
                            <option value="12">ธันวาคม</option>
                        </select>
                    </div>

                    </select>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟรายรับ-จ่ายและกำไรรายเดือน</h3>
                                        <canvas id="monthlyCombinedChart"></canvas>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label for="yearSelector">เลือกปี</label>
                                    <select id="yearSelector" class="form-control">
                                        <option value="">ทุกปี</option>
                                        <?php 
                                    // ดึงปีปัจจุบัน
                                    $currentYear = date("Y") ;

                                    // แสดงปีที่มีอยู่ใน $yearlyDates
                                    foreach ($yearlyDates as $year) { 
                                        // แสดงปีจาก $yearlyDates หากปีนั้นไม่เกิน 5 ปีที่ผ่านมา
                                        if ($year >= $currentYear - 5 && $year <= $currentYear) {
                                    ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php 
                                            }
                                        } 

                                        // แสดงปีย้อนหลัง 5 ปีจากปีปัจจุบัน
                                        for ($i = 0; $i <= 5; $i++) {
                                            $yearOption = $currentYear - $i;
                                            if (!in_array($yearOption, $yearlyDates)) { // ตรวจสอบว่าไม่มีปีนี้ใน $yearlyDates แล้วเพิ่มเข้าไป
                                        ?>
                                        <option value="<?= $yearOption ?>"><?= $yearOption ?></option>
                                        <?php 
                                                    }
                                                } 
                                                ?>
                                    </select>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟรายรับ-จ่ายและกำไรรายปี</h3>
                                        <canvas id="yearlyCombinedChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
    document.getElementById('monthSelector').addEventListener('change', updateCharts);
    document.getElementById('yearSelector').addEventListener('change', updateCharts);

    function updateCharts() {
        const selectedMonth = document.getElementById('monthSelector').value;
        const selectedYear = document.getElementById('yearSelector').value;

        // ฟิลเตอร์ข้อมูลรายเดือน
        const filteredMonthlyDates = <?= json_encode($monthlyDates); ?>.filter(date => {
            const [year, month] = date.split('-');
            return (selectedMonth === '' || month === selectedMonth) && (selectedYear === '' || year ===
                selectedYear);
        });

        const filteredMonthlyRevenueValues = <?= json_encode($monthlyRevenueValues); ?>.filter((_, index) => {
            const [year, month] = <?= json_encode($monthlyDates); ?>[index].split('-');
            return (selectedMonth === '' || month === selectedMonth) && (selectedYear === '' || year ===
                selectedYear);
        });

        const filteredMonthlyProfitValues = <?= json_encode($monthlyProfitValues); ?>.filter((_, index) => {
            const [year, month] = <?= json_encode($monthlyDates); ?>[index].split('-');
            return (selectedMonth === '' || month === selectedMonth) && (selectedYear === '' || year ===
                selectedYear);
        });

        // ฟิลเตอร์ข้อมูลรายจ่ายให้แสดงเฉพาะเดือนที่มีรายจ่าย
        const filteredMonthlyExpenseValues = <?= json_encode($monthlyExpenseValues); ?>.filter((_, index) => {
            const [year, month] = <?= json_encode($monthlyDates); ?>[index].split('-');
            const expense = <?= json_encode($monthlyExpenseValues); ?>[index];

            // เงื่อนไขตรวจสอบว่ามีรายจ่ายในเดือนนั้นหรือไม่
            return expense > 0 && (selectedMonth === '' || month === selectedMonth) && (selectedYear === '' ||
                year === selectedYear);
        });


        // อัปเดตกราฟรายเดือน
        monthlyCombinedChart.data.labels = filteredMonthlyDates.map(date => {
            const d = new Date(date + '-01');
            return d.toLocaleDateString('th-TH', {
                month: '2-digit',
                year: 'numeric'
            });
        });
        monthlyCombinedChart.data.datasets[0].data = filteredMonthlyRevenueValues;
        monthlyCombinedChart.data.datasets[1].data = filteredMonthlyExpenseValues;
        monthlyCombinedChart.data.datasets[2].data = filteredMonthlyProfitValues;
        monthlyCombinedChart.update();

        // ฟิลเตอร์ข้อมูลรายปี
        const filteredYearlyDates = <?= json_encode($yearlyDates); ?>.filter(year => {
            return selectedYear === '' || year === selectedYear;
        });

        const filteredYearlyRevenueValues = <?= json_encode($yearlyRevenueValues); ?>.filter((_, index) => {
            return selectedYear === '' || <?= json_encode($yearlyDates); ?>[index] === selectedYear;
        });

        const filteredYearlyProfitValues = <?= json_encode($yearlyProfitValues); ?>.filter((_, index) => {
            return selectedYear === '' || <?= json_encode($yearlyDates); ?>[index] === selectedYear;
        });

        const filteredYearlyExpenseValues = <?= json_encode($yearlyExpenseValues); ?>.filter((_, index) => {
            return selectedYear === '' || <?= json_encode($yearlyDates); ?>[index] === selectedYear;
        });

        // อัปเดตกราฟรายปี
        yearlyCombinedChart.data.labels = filteredYearlyDates.map(date => {
            const d = new Date(date + '-01-01');
            return d.toLocaleDateString('th-TH', {
                month: '2-digit',
                year: 'numeric'
            });
        });
        yearlyCombinedChart.data.datasets[0].data = filteredYearlyRevenueValues;
        yearlyCombinedChart.data.datasets[1].data = filteredYearlyExpenseValues;
        yearlyCombinedChart.data.datasets[2].data = filteredYearlyProfitValues;
        yearlyCombinedChart.update();
    }
    </script>

    <script>
    // กราฟรายรับ, กำไร และรายจ่ายรายเดือน
    const monthlyCombinedCtx = document.getElementById('monthlyCombinedChart').getContext('2d');
    const monthlyCombinedChart = new Chart(monthlyCombinedCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($monthlyDates); ?>.map(date => {
                const d = new Date(date + '-01'); // Assuming the dates are year-month (e.g., '2024-01')
                return d.toLocaleDateString('th-TH', {
                    month: '2-digit',
                    year: 'numeric'
                });
            }),
            datasets: [{
                    label: 'รายรับรายเดือน (บาท)',
                    data: <?= json_encode($monthlyRevenueValues); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'รายจ่ายรายเดือน (บาท)',
                    data: <?= json_encode($monthlyExpenseValues); ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'กำไรรายเดือน (บาท)',
                    data: <?= json_encode($monthlyProfitValues); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'เดือน'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'บาท'
                    },
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // กราฟรายรับ, กำไร และรายจ่ายรายปี
    const yearlyCombinedCtx = document.getElementById('yearlyCombinedChart').getContext('2d');
    const yearlyCombinedChart = new Chart(yearlyCombinedCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($yearlyDates); ?>.map(date => {
                const d = new Date(date + '-01-01'); // Assuming the dates are just year (e.g., '2024')
                return d.toLocaleDateString('th-TH', {
                    year: 'numeric'
                });
            }),
            datasets: [{
                    label: 'รายรับรายปี (บาท)',
                    data: <?= json_encode($yearlyRevenueValues); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'รายจ่ายรายปี (บาท)',
                    data: <?= json_encode($yearlyExpenseValues); ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'กำไรรายปี (บาท)',
                    data: <?= json_encode($yearlyProfitValues); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'ปี'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'บาท'
                    },
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
    </script>

</body>

</html>