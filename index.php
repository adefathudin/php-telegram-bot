<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set("Asia/Jakarta");

//database config
$servername = "localhost";
$username = "adefathudin";
$password = "assu";
$dbname = "absensi";

$key_bot = 'YOUR_SECRET_KEY_TELEGRAM_BOT';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//ambil id chat telegram dalam bentuk json dan menyimpannya ke database
$get_update = 'https://api.telegram.org/bot' . $key_bot . '/getUpdates';

$json = file_get_contents($get_update);
$jo = json_decode($json);
$a = $jo->result;

foreach ($a as $b) {
    $id = $b->message->from->id;
    $nama = $b->message->from->first_name;

    $select_id = "SELECT * FROM telegram WHERE id_telegram='$id'";
    $hasil = $conn->query($select_id);

    if ($hasil->num_rows <= 0) {
        $conn->query("INSERT INTO telegram (id_telegram, nama) VALUES ($id, '$nama')");
    }
}

$sql = "SELECT * FROM telegram";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nama = $row['nama'];
        $msg = "[" . date('D, j M Y H:i:s') . "] Halo " . $nama . ", Ini adalah pesan telegram otomatis";
        try {
            $url = "https://api.telegram.org/bot" . $key_bot . "/sendMessage?chat_id=" . $row['id_telegram'];
            $url = $url . "&text=" . urlencode($msg);
            $ch = curl_init();
            $optArray = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
            );
            curl_setopt_array($ch, $optArray);
            curl_exec($ch);

        } catch (Exception $ex) {
            echo "error";
        }
    }
} else {
    echo "0 results";
}
