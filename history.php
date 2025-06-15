<?php
// Koneksi DB dan ambil data (contoh sederhana)
$conn = new mysqli("localhost", "root", "", "monitoring_db");
$result = $conn->query("SELECT * FROM history ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Kelembapan</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="sidebar">
        <img src="images/profile.jpg" alt="Profile" class="profile-img">
        <h2>Susilo Adi Wibowo</h2>
        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="history.php">History</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>History Monitoring Kelembapan Tanah</h1>
        <div class="history-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal / Bulan</th>
                        <th>Waktu</th>
                        <th>Kelembapan (%)</th>
                        <th>Suhu (°C)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= date('H:i', strtotime($row['jam'])) ?> WITA</td>
                            <td><?= $row['kelembapan'] ?>%</td>
                            <td><?= $row['suhu'] ? $row['suhu'] . '°C' : '-' ?></td>
                            <td><?= $row['status'] ?></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
