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

## Lisensi
Proyek ini open-source dan bebas digunakan, dimodifikasi, serta dikomersialkan.





