
# IoT Penyiram Tanaman Otomatis Berbasis ESP8266

Sistem penyiraman tanaman otomatis berbasis IoT menggunakan **NodeMCU ESP8266**, sensor DHT11, sensor kelembaban tanah, dan relay untuk mengontrol pompa air. Data sensor (suhu, kelembapan udara, kelembapan tanah, dan status pompa) dikirim setiap 20 detik ke server web untuk monitoring real-time.

> “Tanaman tetap subur meski kamu lupa menyiram atau sedang jauh dari rumah!”

## Fitur
- Deteksi otomatis kelembaban tanah
- Penyiraman otomatis jika tanah kering (< 50%)
- Monitoring suhu dan kelembapan udara (DHT11)
- Pengiriman data real-time ke server web via Wi-Fi
- Hemat air & listrik
- Mudah dikembangkan (Blynk, Telegram, dll)

## Hardware yang Dibutuhkan
| Komponen                  | Keterangan                          |
|---------------------------|-------------------------------------|
| NodeMCU ESP8266           | Board utama                         |
| DHT11 + resistor 10kΩ     | Sensor suhu & kelembapan udara      |
| Soil Moisture Sensor      | Sensor kelembaban tanah (analog)    |
| Single Channel Relay 5V   | Mengontrol pompa air                |
| Pompa Air Mini 5V         | Submersible atau selang            |
| Power Supply 5V (2A+)     | Bisa dari adapter atau power bank   |
| Jumper, Breadboard/PCB    | Untuk rangkaian                     |

## Skema Koneksi
DHT11     → D4 (GPIO2) + VCC 3.3V + GND
Soil Sensor → A0 (Analog)
Relay IN  → D1 (GPIO5)
Relay VCC → VIN (5V)
Relay GND → GND
Pompa     → Terhubung ke relay (NO & COM)
text## Konfigurasi Kode
1. Ganti bagian berikut sesuai jaringan dan server kamu:
```cpp
const char* ssid = "MUJADID";          // ← Ganti dengan WiFi kamu
const char* password = "adit2023";     // ← Ganti dengan password WiFi
const char* server = "overview.my.id"; // ← Ganti dengan domain/IP server kamu

Upload kode menggunakan Arduino IDE (pilih board NodeMCU 1.0 (ESP-12E Module))

Backend Server (sensor_receiver.php)
Buat file sensor_receiver.php di hosting kamu (contoh):
PHP<?php
$servername = "localhost";
$username = "username_db";
$password = "password_db";
$dbname = "nama_database";

// Ambil data dari ESP8266
$temperature = $_GET['temperature'];
$humidity = $_GET['humidity'];
$moisture = $_GET['moisture'];
$pump = $_GET['pump'];
$timestamp = date("Y-m-d H:i:s");

// Koneksi ke MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO sensor_data (temperature, humidity, moisture, pump_status, timestamp) 
        VALUES ('$temperature', '$humidity', '$moisture', '$pump', '$timestamp')";

if ($conn->query($sql) === TRUE) {
    echo "Data berhasil disimpan";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>
Tabel Database (MySQL)
SQLCREATE TABLE sensor_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature FLOAT,
    humidity FLOAT,
    moisture FLOAT,
    pump_status INT,
    timestamp DATETIME
);

#Demo

Server demo: http://overview.my.id
(Ganti dengan link kamu nanti)

Pengembangan Lanjutan (Opsional)

Tambah aplikasi Blynk / Telegram Bot untuk notifikasi
Kontrol manual pompa dari HP
Jadwal penyiraman harian
Grafik real-time dengan Chart.js
Prediksi kebutuhan air dengan Machine Learning

Lisensi
Proyek ini open-source dan bebas digunakan, dimodifikasi, serta dikomersialkan.

