<?php
require_once 'db_config.php';
header('Content-Type: application/json');
$result = $conn->query("SELECT * FROM data_monitoring ORDER BY tanggal DESC");
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>