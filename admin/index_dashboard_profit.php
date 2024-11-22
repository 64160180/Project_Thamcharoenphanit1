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


// คิวรีเพื่อดึงข้อมูลรายจ่ายรายเดือน
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

// คิวรีเพื่อดึงข้อมูลรายจ่ายรายปี
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
$monthlyProfitValues = !empty($monthlyProfits) ? array_column($monthlyProfits, 'profit') : [];
$monthlyExpenseValues = array_column($monthlyExpenses, 'expenses');

// เตรียมข้อมูลสำหรับกราฟรายปี
$yearlyDates = array_column($yearlyRevenues, 'order_year');
$yearlyRevenueValues = array_column($yearlyRevenues, 'revenue');
$yearlyProfitValues = !empty($yearlyProfits) ? array_column($yearlyProfits, 'profit') : [];
$yearlyExpenseValues = array_column($yearlyExpenses, 'expenses');


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
                        <h1>Dashboard รายรับ-จ่ายและกำไร</h1>
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
                    <div class="col-sm-3">
                        <label for="yearSelector">เลือกปี</label>
                        <select id="yearSelector" class="form-control">
                            <option value="">ทุกปี</option>
                            <?php 
                            foreach ($yearlyDates as $year) { 
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h3>กราฟรายรับ-จ่ายและกำไรรายเดือน</h3>
                                <canvas id="monthlyCombinedChart"></canvas>
                                <h3>กราฟรายรับ-จ่ายและกำไรรายปี</h3>
                                <canvas id="yearlyCombinedChart"></canvas>
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

    // กรองข้อมูลสำหรับรายเดือน
    const filteredMonthlyLabels = <?php echo json_encode($monthlyDates); ?>.filter((label) => {
        if (selectedYear && !label.startsWith(selectedYear)) return false;
        if (selectedMonth && !label.endsWith(`-${selectedMonth}`)) return false;
        return true;
    });

    const filteredMonthlyRevenue = <?php echo json_encode($monthlyRevenueValues); ?>.filter((_, index) =>
        filteredMonthlyLabels.includes(<?php echo json_encode($monthlyDates); ?>[index])
    );
    const filteredMonthlyProfit = <?php echo json_encode($monthlyProfitValues); ?>.filter((_, index) =>
        filteredMonthlyLabels.includes(<?php echo json_encode($monthlyDates); ?>[index])
    );
    const filteredMonthlyExpense = <?php echo json_encode($monthlyExpenseValues); ?>.filter((_, index) =>
        filteredMonthlyLabels.includes(<?php echo json_encode($monthlyDates); ?>[index])
    );

    // อัปเดตกราฟรายเดือน
    monthlyChart.data.labels = filteredMonthlyLabels;
    monthlyChart.data.datasets[0].data = filteredMonthlyRevenue;
    monthlyChart.data.datasets[1].data = filteredMonthlyExpense;
    monthlyChart.data.datasets[2].data = filteredMonthlyProfit;
    monthlyChart.update();

    // กรองข้อมูลสำหรับรายปี
    const filteredYearlyLabels = <?php echo json_encode($yearlyDates); ?>.filter((label) => {
        if (selectedYear && label !== selectedYear) return false;
        return true;
    });

    const filteredYearlyRevenue = <?php echo json_encode($yearlyRevenueValues); ?>.filter((_, index) =>
        filteredYearlyLabels.includes(<?php echo json_encode($yearlyDates); ?>[index])
    );
    const filteredYearlyProfit = <?php echo json_encode($yearlyProfitValues); ?>.filter((_, index) =>
        filteredYearlyLabels.includes(<?php echo json_encode($yearlyDates); ?>[index])
    );
    const filteredYearlyExpense = <?php echo json_encode($yearlyExpenseValues); ?>.filter((_, index) =>
        filteredYearlyLabels.includes(<?php echo json_encode($yearlyDates); ?>[index])
    );

    // อัปเดตกราฟรายปี
    yearlyChart.data.labels = filteredYearlyLabels;
    yearlyChart.data.datasets[0].data = filteredYearlyRevenue;
    yearlyChart.data.datasets[1].data = filteredYearlyExpense;
    yearlyChart.data.datasets[2].data = filteredYearlyProfit;
    yearlyChart.update();
}


    const chartOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'top', // ตำแหน่ง legend (top, bottom, left, right)
                labels: {
                    font: {
                        size: 14 // ขนาดตัวอักษรใน legend
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        // ปรับการแสดงค่า tooltip
                        return `${tooltipItem.dataset.label}: ${tooltipItem.raw.toLocaleString()} บาท`;
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'เดือน/ปี',
                    font: {
                        size: 16
                    }
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'มูลค่า (บาท)',
                    font: {
                        size: 16
                    }
                },
                ticks: {
                    callback: function(value) {
                        // รูปแบบตัวเลขแกน Y
                        return value.toLocaleString() + ' บาท';
                    }
                }
            }
        }
    };

    const monthlyData = {
        labels: <?php echo json_encode($monthlyDates); ?>,
        datasets: [{
                label: 'รายรับ',
                data: <?php echo json_encode($monthlyRevenueValues); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },

            {
                label: 'รายจ่าย',
                data: <?php echo json_encode($monthlyExpenseValues); ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64,  1)',
                borderWidth: 1
            },
            {
                label: 'กำไร',
                data: <?php echo json_encode($monthlyProfitValues); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ],
    };

    const yearlyData = {
        labels: <?php echo json_encode($yearlyDates); ?>,
        datasets: [{
                label: 'รายรับ',
                data: <?php echo json_encode($yearlyRevenueValues); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },

            {
                label: 'รายจ่าย',
                data: <?php echo json_encode($yearlyExpenseValues); ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.6)',
                borderColor: 'rgba(255, 159, 64,  1)',
                borderWidth: 1
            },
            {
                label: 'กำไร',
                data: <?php echo json_encode($yearlyProfitValues); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ],
    };

    // กราฟรายเดือน
    const monthlyChart = new Chart(document.getElementById('monthlyCombinedChart').getContext('2d'), {
    type: 'bar',
    data: monthlyData,
    options: chartOptions
});

const yearlyChart = new Chart(document.getElementById('yearlyCombinedChart').getContext('2d'), {
    type: 'bar',
    data: yearlyData,
    options: chartOptions
});

    </script>

</body>

</html>