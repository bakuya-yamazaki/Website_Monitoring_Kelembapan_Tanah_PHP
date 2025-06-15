<?php
$csrf_token = bin2hex(random_bytes(32));
$humidity = rand(30, 70);
$temperature = rand(10, 25);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="csrf-token" content="<?= $csrf_token ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="sidebar">
    <img src="images/profile.jpg" alt="Profile Picture" class="profile-img">
    <h2>Susilo Adi Wibowo</h2>
    <ul class="menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="history.php">History</a></li>
    </ul>
</div>
<div class="content">
    <div class="status-bar">
        <span class="status">Kelembapan: <strong id="humidity"><?= $humidity ?>%</strong></span>
        <span class="status">Suhu: <strong id="temperature"><?= $temperature ?>°C</strong></span>
    </div>

    <canvas id="humidityChart"></canvas>
    <p class="chart-description">Grafik gunung mengukur kelembapan dan suhu secara real-time</p>

    <div class="status-container">
        <div class="status-box">
            <h3>Kondisi Basah</h3>
            <p id="moisture-status" class="status-ok"><?= $humidity >= 50 ? 'OK' : 'Kering' ?></p>
        </div>
        <div class="status-box">
            <h3>Irigasi Air</h3>
            <button id="irrigation-toggle" class="toggle-button off">OFF</button>
        </div>
    </div>

    <button id="time-skip" class="time-skip-button">Time Skip</button>
</div>

<script>
    let humidity = <?= $humidity ?>;
    let temperature = <?= $temperature ?>;
    let irrigationOn = false;

    function updateValues() {
        document.getElementById('humidity').textContent = humidity + '%';
        document.getElementById('temperature').textContent = temperature + '°C';
        document.getElementById('moisture-status').textContent = humidity >= 50 ? 'OK' : 'Kering';
    }

    const ctx = document.getElementById('humidityChart').getContext('2d');
    const humidityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['1', '2', '3', '4', '5', '6', '7'],
            datasets: [{
                label: 'Kelembapan',
                data: [humidity, humidity - 5, humidity - 10, humidity + 5, humidity + 10, humidity - 3, humidity],
                backgroundColor: 'rgba(0, 0, 255, 0.5)',
                borderColor: 'blue',
                fill: true,
            }]
        }
    });

    document.getElementById('irrigation-toggle').addEventListener('click', function () {
        if (!irrigationOn) {
            irrigationOn = true;
            this.textContent = 'ON';
            this.classList.remove('off');
            this.classList.add('on');
            let interval = setInterval(() => {
                humidity += 2;
                if (humidity >= 50) {
                    irrigationOn = false;
                    document.getElementById('irrigation-toggle').textContent = 'OFF';
                    document.getElementById('irrigation-toggle').classList.remove('on');
                    document.getElementById('irrigation-toggle').classList.add('off');
                    clearInterval(interval);
                }
                updateValues();
            }, 1000);
        } else {
            irrigationOn = false;
            this.textContent = 'OFF';
            this.classList.remove('on');
            this.classList.add('off');
        }
    });

    document.getElementById('time-skip').addEventListener('click', function () {
    // Simpan nilai lama SEBELUM diubah
    let savedHumidity = humidity;
    let savedTemperature = temperature;

    // Lalu baru generate nilai baru
    humidity = Math.floor(Math.random() * 40) + 30;
    temperature = Math.floor(Math.random() * 15) + 10;

    let currentDate = new Date().toISOString().split('T')[0];
    let now = new Date();
    let currentTime = now.toTimeString().split(' ')[0]; // "HH:MM:SS"

    fetch('save-history.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            tanggal: currentDate,
            jam: currentTime,
            kelembapan: savedHumidity,        // gunakan nilai sebelum perubahan
            suhu: savedTemperature,           // gunakan nilai sebelum perubahan
            status: savedHumidity >= 50 ? 'Basah' : 'Kering'
        })
    })
    .then(response => response.json())
    .then(data => {
        alert('Data berhasil disimpan ke history!');
    })
    .catch(error => console.error('Error:', error));

    // Update tampilan dengan nilai baru
    updateValues();
});
</script>
</body>
</html>
