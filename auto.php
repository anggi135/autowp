<?php

// memanggil file rss_xml.php untuk mengakses fungsi WordPress
require_once( dirname( __FILE__ ) . 'rss_xml.php' );

// mengatur interval waktu untuk eksekusi kode (dalam detik)
$interval = 3600;

// menentukan waktu awal
$start_time = time();

// menjalankan kode setiap interval waktu
while (true) {
  // memanggil kode untuk memproses feed RSS dan memasukkan konten ke WordPress
  include 'process_rss.php';

  // menghitung waktu yang diperlukan untuk menjalankan kode
  $time_elapsed = time() - $start_time;

  // menunggu sisa waktu interval sebelum menjalankan kode lagi
  sleep($interval - $time_elapsed);
  $start_time = time();
}
?>
