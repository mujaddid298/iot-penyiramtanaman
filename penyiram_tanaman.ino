#include <ESP8266WiFi.h>
#include <DHT.h>

#define DHTPIN D4          
#define DHTTYPE DHT11      
#define SOIL_PIN A0        
#define RELAY_PIN D1       

const char* ssid = "MUJADID";          
const char* password = "adit2023";     
const char* server = "overview.my.id";  // Ganti dengan domain kamu

DHT dht(DHTPIN, DHTTYPE);
WiFiClient client;

void setup() {
  Serial.begin(115200);
  dht.begin();
  pinMode(SOIL_PIN, INPUT);
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, HIGH); // Pompa OFF

  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan ke Wi-Fi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWi-Fi Terhubung!");
}

void loop() {
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();
  int soilAnalogValue = analogRead(SOIL_PIN);
  float soilMoisturePercent = (750 - soilAnalogValue) * 100.0 / (750 - 250);
  soilMoisturePercent = constrain(soilMoisturePercent, 0, 100);

  int pumpStatus = 0;

  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Gagal membaca dari sensor DHT!");
    return;
  }

  if (soilMoisturePercent < 50) {
    digitalWrite(RELAY_PIN, HIGH); // Pompa ON
    pumpStatus = 1;
  } else {
    digitalWrite(RELAY_PIN, LOW); // Pompa OFF
    pumpStatus = 0;
  }

  Serial.print("Suhu: ");
  Serial.print(temperature);
  Serial.print(" °C\tKelembapan Tanah: ");
  Serial.print(soilMoisturePercent);
  Serial.print(" %\tStatus Pompa: ");
  Serial.println(pumpStatus == 1 ? "AKTIF" : "MATI");

  if (client.connect(server, 80)) {
    String url = "/sensor_receiver.php?temperature=" + String(temperature, 2) +
                 "&humidity=" + String(humidity, 2) +
                 "&moisture=" + String(soilMoisturePercent, 2) +
                 "&pump=" + String(pumpStatus);

    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + server + "\r\n" +
                 "Connection: close\r\n\r\n");

    Serial.println("Data dikirim ke overview.my.id!");
  } else {
    Serial.println("Koneksi ke overview.my.id gagal.");
  }

  client.stop();
  delay(20000); // Delay 20 detik
}
