<?php
// Tambahkan di bagian paling atas file
if (isset($_GET['temperature']) && isset($_GET['moisture']) && isset($_GET['pump'])) {
    require_once 'db_config.php'; // koneksi database

    $temperature = floatval($_GET['temperature']);
    $moisture = floatval($_GET['moisture']);
    $pump = $_GET['pump'];

    date_default_timezone_set('Asia/Jakarta');
    $waktu = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO data_monitoring (tanggal, suhu, kelembapan, status_mesin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ddss", $temperature, $moisture, $pump, $waktu);

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil disimpan ke database!",
            "data" => [
                "temperature" => $temperature,
                "moisture" => $moisture,
                "pump" => $pump,
                "waktu" => $waktu
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menyimpan data: " . $stmt->error
        ]);
    }

    $stmt->close();
    exit;
}

// require_once 'db_config.php';

// $channel_id = '2969861';
// $api_key = '2L7V06KD7L9AY8CL';
// $url = "https://api.thingspeak.com/channels/$channel_id/feeds/last.json?api_key=$api_key";

// $response = file_get_contents($url);
// $data = json_decode($response, true);

// if ($data && isset($data['field1'], $data['field2'], $data['field3'])) {
//     $suhu = floatval($data['field1']);
//     $kelembapan = floatval($data['field2']);
//     $status_mesin = $data['field3'];

//     // Gunakan waktu lokal server
//     date_default_timezone_set('Asia/Jakarta');
//     $waktu = date('Y-m-d H:i:s');

//     $stmt = $conn->prepare("INSERT INTO data_monitoring (suhu, kelembapan, status_mesin, tanggal) VALUES (?, ?, ?, ?)");
//     $stmt->bind_param("ddss", $suhu, $kelembapan, $status_mesin, $waktu);

//     if ($stmt->execute()) {
//         echo "Data berhasil disimpan!";
//     } else {
//         echo "Gagal menyimpan data: " . $stmt->error;
//     }

//     $stmt->close();
// } else {
//     echo "Data dari ThingSpeak tidak tersedia atau format salah.";
// }
 
// ?>
