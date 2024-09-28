<?php 
require_once '../config/condb.php';

// คิวรีเพื่อดึงข้อมูลกำไรรายวัน
$queryProfit = $condb->prepare("
    SELECT 
        DATE(date_out) AS order_date,
        SUM((sell_price - cost_price) * quantity) AS profit
    FROM tbl_order 
    GROUP BY DATE(date_out) 
    ORDER BY order_date ASC
");
$queryProfit->execute();
$profits = $queryProfit->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับกราฟ
$dates = [];
$profitValues = [];
foreach ($profits as $profit) {
    $dates[] = $profit['order_date'];
    $profitValues[] = $profit['profit'];
}

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

// เตรียมข้อมูลสำหรับกราฟกำไรรายเดือน
$monthlyDates = [];
$monthlyProfitValues = [];
foreach ($monthlyProfits as $profit) {
    $monthlyDates[] = $profit['order_month'];
    $monthlyProfitValues[] = $profit['profit'];
}

// เตรียมข้อมูลสำหรับกราฟกำไรรายปี
$yearlyDates = [];
$yearlyProfitValues = [];
foreach ($yearlyProfits as $profit) {
    $yearlyDates[] = $profit['order_year'];
    $yearlyProfitValues[] = $profit['profit'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard กำไร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
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
                                    
                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟกำไรรายวัน</h3>
                                        <canvas id="profitChart"></canvas>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟกำไรรายเดือน</h3>
                                        <canvas id="monthlyProfitChart"></canvas>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h3>กราฟกำไรรายปี</h3>
                                        <canvas id="yearlyProfitChart"></canvas>
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
        // กราฟกำไรรายวัน
    const ctx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates); ?>,
            datasets: [{
                label: 'กำไรรายวัน (บาท)',
                data: <?= json_encode($profitValues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'วันที่'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'กำไร (บาท)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <script>
    // กราฟกำไรรายเดือน
    const monthlyCtx = document.getElementById('monthlyProfitChart').getContext('2d');
    const monthlyProfitChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlyDates); ?>,
            datasets: [{
                label: 'กำไรรายเดือน (บาท)',
                data: <?= json_encode($monthlyProfitValues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.1
            }]
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
                        text: 'กำไร (บาท)'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // กราฟกำไรรายปี
    const yearlyCtx = document.getElementById('yearlyProfitChart').getContext('2d');
    const yearlyProfitChart = new Chart(yearlyCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($yearlyDates); ?>,
            datasets: [{
                label: 'กำไรรายปี (บาท)',
                data: <?= json_encode($yearlyProfitValues); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.1
            }]
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
                        text: 'กำไร (บาท)'
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
