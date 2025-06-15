<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = ''; // Ganti jika password MySQL Anda tidak kosong
$database = 'monitoring_db'; // Sesuai dengan gambar

$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]);
    exit;
}

// Ambil data dari POST JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak valid']);
    exit;
}

$tanggal = $conn->real_escape_string($data['tanggal']);
$jam = $conn->real_escape_string($data['jam']);
$kelembapan = (int)$data['kelembapan'];
$suhu = (int)$data['suhu'];
$status = $conn->real_escape_string($data['status']);

// Simpan ke database
$sql = "INSERT INTO history (tanggal, jam, kelembapan, suhu, status)
        VALUES ('$tanggal', '$jam', $kelembapan, $suhu, '$status')";
if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan data: ' . $conn->error]);
}

$conn->close();
?>


