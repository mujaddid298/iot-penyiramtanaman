<?php
session_start();
// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_config.php';

// Proses penghapusan data
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM data_monitoring WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Data berhasil dihapus";
    } else {
        $error_message = "Gagal menghapus data: " . $conn->error;
    }
    $stmt->close();
}

// Ambil data dari database
$sql = "SELECT * FROM data_monitoring ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Monitoring Tanaman Cabai</title>
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="home.php">Home</a>
                <a href="data.php" class="active">Data</a>
            </div>

            <a href="logout.php">
                <button class="logout-button">
                    <span class="logout-icon">×</span>
                    <span class="logout-text">Logout</span>
                </button>
            </a>
        </div>
        
        <div class="content">
            <div class="title">DATA MONITORING TANAMAN CABAI</div>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <button id="printPdfBtn" class="btn btn-success">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </button>
            </div>
            
            <div class="table-container">
                <div class="table-controls">
                    <div class="entries-control">
                        <span>Tampilkan</span>
                        <select id="entriesSelect">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                        <span>data</span>
                    </div>
                    
                    <div class="table-search">
                        <input type="text" id="searchInput" placeholder="Cari...">
                    </div>
                </div>
                
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th data-sort="date">Tanggal & Jam</th>
                            <th data-sort="suhu">Suhu (°C)</th>
                            <th data-sort="kelembapan">Kelembapan (%)</th>
                            <th data-sort="mesin">Status Mesin Air</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr data-id='".$row['id']."'>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row['tanggal'])) . "</td>";
                                echo "<td>" . $row['suhu'] . "</td>";
                                echo "<td>" . $row['kelembapan'] . "</td>";
                                echo "<td>" . $row['status_mesin'] . "</td>";
                                echo "<td class='action-column'>
                                        <a href='data.php?delete=".$row['id']."' class='btn-delete' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>
                                            <i class='fas fa-trash'></i>
                                        </a>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Tidak ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                
                <div class="pagination" id="pagination">
                    <!-- Pagination akan diisi oleh JavaScript -->
                </div>
                
                <div class="table-info" id="tableInfo">
                    <!-- Info akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="script1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="pdf_export.js"></script>

    <script>
        // Fungsi pencarian pada tabel
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const table = document.getElementById('dataTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            // Loop melalui semua baris tabel dan sembunyikan yang tidak cocok
            for (let i = 0; i < rows.length; i++) {
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                
                // Periksa semua sel dalam baris
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    
                    if (cellText.toLowerCase().indexOf(searchText) > -1) {
                        found = true;
                        break;
                    }
                }
                
                if (found) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    </script>
    
</body>
</html>