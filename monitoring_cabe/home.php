<?php
session_start();

// 🔐 Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 🔌 Include koneksi database
require_once 'db_config.php'; 
// 🛡️ Cek apakah koneksi berhasil
if (!$conn || $conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// 🔽 Ambil data terbaru
$latest_data = null;
$sql = "SELECT * FROM data_monitoring ORDER BY tanggal DESC LIMIT 1";
if ($result = $conn->query($sql)) {
    $latest_data = $result->fetch_assoc();
    $result->free();
}

// 📊 Inisialisasi array statistik
$stats = [
    'avg_suhu' => 0,
    'avg_kelembapan' => 0,
    'total_data' => 0,
    'status_aktif' => 0,
    'status_nonaktif' => 0,
];

// 🌡️ Suhu rata-rata
$sql_suhu_avg = "SELECT AVG(suhu) as avg_suhu FROM data_monitoring";
if ($result_suhu = $conn->query($sql_suhu_avg)) {
    $row = $result_suhu->fetch_assoc();
    $stats['avg_suhu'] = round($row['avg_suhu'], 1);
    $result_suhu->free();
}

// 💧 Kelembapan rata-rata
$sql_kelembapan_avg = "SELECT AVG(kelembapan) as avg_kelembapan FROM data_monitoring";
if ($result_kelembapan = $conn->query($sql_kelembapan_avg)) {
    $row = $result_kelembapan->fetch_assoc();
    $stats['avg_kelembapan'] = round($row['avg_kelembapan'], 1);
    $result_kelembapan->free();
}

// 📈 Total data
$sql_count = "SELECT COUNT(*) as total FROM data_monitoring";
if ($result_count = $conn->query($sql_count)) {
    $row = $result_count->fetch_assoc();
    $stats['total_data'] = $row['total'];
    $result_count->free();
}

// 🚰 Status mesin
$sql_status = "SELECT 
    SUM(CASE WHEN status_mesin = 'Aktif' THEN 1 ELSE 0 END) as aktif,
    SUM(CASE WHEN status_mesin = 'Tidak Aktif' THEN 1 ELSE 0 END) as nonaktif
    FROM data_monitoring";
if ($result_status = $conn->query($sql_status)) {
    $row = $result_status->fetch_assoc();
    $stats['status_aktif'] = $row['aktif'];
    $stats['status_nonaktif'] = $row['nonaktif'];
    $result_status->free();
}

// 📊 Data untuk grafik suhu & kelembapan (hari ini)
$chart_data = [];
$today = date('Y-m-d');
$sql_chart = "SELECT 
    DATE_FORMAT(tanggal, '%H:%i') as jam,
    suhu,
    kelembapan 
    FROM data_monitoring 
    WHERE DATE(tanggal) = '$today'
    ORDER BY tanggal ASC";
if ($result_chart = $conn->query($sql_chart)) {
    while ($row = $result_chart->fetch_assoc()) {
        $chart_data[] = $row;
    }
    $result_chart->free();
}
$chart_data_json = json_encode($chart_data);

// ❌ Tutup koneksi setelah semua selesai
$conn->close();
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monitoring Tanaman Cabai</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <style>
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            align-items: center;
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: white;
        }
        
        .card-suhu .card-icon {
            background-color: #e74c3c;
        }
        
        .card-kelembapan .card-icon {
            background-color: #3498db;
        }
        
        .card-data .card-icon {
            background-color: #9b59b6;
        }
        
        .card-status .card-icon {
            background-color: #2ecc71;
        }
        
        .card-info h3 {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .card-info p {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
        
        .chart-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .latest-data {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .latest-data h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .latest-data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .data-item {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
        }
        
        .data-item h3 {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }
        
        .data-item p {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
        
        .data-item p.status-aktif {
            color: #2ecc71;
        }
        
        .data-item p.status-nonaktif {
            color: #e74c3c;
        }
        
        .welcome-message {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar" id="sidebar">
            <div class="toggle-sidebar" id="toggleSidebar">
                ◀
            </div>
            
            <div class="dashboard-label" id="dashboardButton">
                <span class="dashboard-icon">≡</span>
                <span class="dashboard-text">Dashboard</span>
            </div>
            
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="home.php" class="active">Home</a>
                <a href="data.php">Data</a>
            </div>

            <a href="logout.php">
                <button class="logout-button">
                    <span class="logout-icon">×</span>
                    <span class="logout-text">Logout</span>
                </button>
            </a>
        </div>
        
        <div class="content">
            <div class="title">DASHBOARD MONITORING TANAMAN CABAI</div>
            
            <div class="welcome-message">
                <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Ini adalah ringkasan data monitoring tanaman cabai.</p>
            </div>
            
            <div class="dashboard-cards">
                <div class="card card-suhu">
                    <div class="card-icon">
                        <i class="fas fa-thermometer-half"></i>
                    </div>
                    <div class="card-info">
                        <h3>Suhu Rata-rata</h3>
                        <p><?php echo $stats['avg_suhu']; ?> °C</p>
                    </div>
                </div>
                
                <div class="card card-kelembapan">
                    <div class="card-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="card-info">
                        <h3>Kelembapan Rata-rata</h3>
                        <p><?php echo $stats['avg_kelembapan']; ?> %</p>
                    </div>
                </div>
                
                
                <div class="card card-status">
                    <div class="card-icon">
                        <i class="fas fa-power-off"></i>
                    </div>
                    <div class="card-info">
                        <h3>Status Mesin Air</h3>
                        <p>Aktif: <?php echo $stats['status_aktif']; ?> Nonaktif: <?php echo $stats['status_nonaktif']; ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($latest_data): ?>
            <div class="latest-data">
                <h2>Data Terbaru (<?php echo date('d/m/Y H:i', strtotime($latest_data['tanggal'])); ?>)</h2>
                <div class="latest-data-grid">
                    <div class="data-item">
                        <h3>Suhu</h3>
                        <p><?php echo $latest_data['suhu']; ?> °C</p>
                    </div>
                    
                    <div class="data-item">
                        <h3>Kelembapan</h3>
                        <p><?php echo $latest_data['kelembapan']; ?> %</p>
                    </div>
                    
                    <div class="data-item">
                        <h3>Status Mesin Air</h3>
                        <?php
                            $status_label = ($latest_data['status_mesin'] == '1') ? 'Aktif' : 'Tidak Aktif';
                            $status_class = ($latest_data['status_mesin'] == '1') ? 'status-aktif' : 'status-nonaktif';
                        ?>
                        <p class="<?php echo $status_class; ?>"><?php echo $status_label; ?></p>

                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="chart-container">
                <h2 class="section-title">Grafik Monitoring Hari Ini</h2>
                <canvas id="monitoringChart"></canvas>
            </div>
            
            <div class="action-buttons">
                <a href="data.php" class="btn btn-primary">
                    <i class="fas fa-table"></i> Lihat Semua Data
                </a>
            </div>
        </div>
    </div>

    <script src="script1.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data dari PHP
            const chartData = <?php echo $chart_data_json; ?>;
            
            if (chartData.length > 0) {
                const labels = chartData.map(item => item.jam);
                const suhuData = chartData.map(item => item.suhu);
                const kelembapanData = chartData.map(item => item.kelembapan);
                
                // Inisialisasi grafik
                const ctx = document.getElementById('monitoringChart').getContext('2d');
                const monitoringChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Suhu (°C)',
                                data: suhuData,
                                borderColor: '#e74c3c',
                                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Kelembapan (%)',
                                data: kelembapanData,
                                borderColor: '#3498db',
                                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false
                            }
                        }
                    }
                });
            } else {
                document.getElementById('monitoringChart').innerHTML = 'Tidak ada data hari ini';
            }
        });
    </script>
    
</body>
</html>