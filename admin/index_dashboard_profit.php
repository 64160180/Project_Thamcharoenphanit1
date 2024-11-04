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
$queryMonthlyProfit = $condb->prepare("
    SELECT 
        DATE_FORMAT(date_out, '%Y-%m') AS order_month,
        SUM((sell_price - cost_price) * quantity) AS profit
    FROM tbl_order 
    GROUP BY order_month 
    ORDER BY order_month ASC
");
$queryMonthlyProfit->execute();
$monthlyProfits = $queryMonthlyProfit->fetchAll(PDO::FETCH_ASSOC);

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
$queryYearlyProfit = $condb->prepare("
    SELECT 
        DATE_FORMAT(date_out, '%Y') AS order_year,
        SUM((sell_price - cost_price) * quantity) AS profit
    FROM tbl_order 
    GROUP BY order_year 
    ORDER BY order_year ASC
");
$queryYearlyProfit->execute();
$yearlyProfits = $queryYearlyProfit->fetchAll(PDO::FETCH_ASSOC);


// เตรียมข้อมูลสำหรับกราฟรายเดือน
$monthlyDates = array_column($monthlyRevenues, 'order_month');
$monthlyRevenueValues = array_column($monthlyRevenues, 'revenue');
$monthlyProfitValues = array_column($monthlyProfits, 'profit');

// เตรียมข้อมูลสำหรับกราฟรายปี
$yearlyDates = array_column($yearlyRevenues, 'order_year');
$yearlyRevenueValues = array_column($yearlyRevenues, 'revenue');
$yearlyProfitValues = array_column($yearlyProfits, 'profit');
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboardรายรับและกำไร-ธรรมเจริญพาณิช</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard รายรับและกำไร</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">  
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟรายรับและกำไรรายเดือน</h3>
                                        <canvas id="monthlyChart"></canvas>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟรายรับและกำไรรายปี</h3>
                                        <canvas id="yearlyChart"></canvas>
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
        

        // กราฟรายรับและกำไรรายเดือน
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthlyDates); ?>,
                datasets: [
                    {
                        label: 'รายรับรายเดือน (บาท)',
                        data: <?= json_encode($monthlyRevenueValues); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'กำไรรายเดือน (บาท)',
                        data: <?= json_encode($monthlyProfitValues); ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
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
                }
            }
        });

        // กราฟรายรับและกำไรรายปี
        const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        const yearlyChart = new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($yearlyDates); ?>,
                datasets: [
                    {
                        label: 'รายรับรายปี (บาท)',
                        data: <?= json_encode($yearlyRevenueValues); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'กำไรรายปี (บาท)',
                        data: <?= json_encode($yearlyProfitValues); ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
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
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
