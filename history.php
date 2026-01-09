<?php
require_once 'config.php';
requireLogin();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Sistem Pemantauan Kualitas Udara dalam Ruangan</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/clouds.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <!--  <script type="text/javascript" src="jquery/jquery.min.js"></script> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- <script type="text/javascript">
       $(document).ready( function(){

        setInterval( function() {
          $("#realtime").load("realtime.php");
          $("#suhu").load("suhu.php");
          $("#no2").load("no2.php");
          $("#co").load("co.php");
          $("#debu").load("debu.php");
          $("#keterangan").load("keterangan.php");
        }, 3000 );

     });
    </script> -->
  <!-- =======================================================
  * Template Name: OnePage - v4.7.0
  * Template URL: https://bootstrapmade.com/onepage-multipurpose-bootstrap-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">

      <h1 class="logo"><a href=".">Pemantau Kualitas Udara dalam Ruangan </a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link" href=".">Home</a></li>
          <li><a class="nav-link" href="history.php">History</a></li>
        <li class="dropdown">
            <a href="#"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($currentUser['nama']) ?> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center">
    <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">
      <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9 text-center">
          <div class="history">
            <h1>History Data Kualitas Udara</h1>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <div class="col text-center">

          <?php
          // koneksi DB
          $connect = mysqli_connect("localhost", "root", "", "skripsi");
          if (!$connect) {
            die('<div class="alert alert-danger">Database connection failed: ' . mysqli_connect_error() . '</div>');
          }

          // pagination config (sama seperti milik Anda)
          $batas = 20;
          $halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
          if ($halaman < 1)
            $halaman = 1;
          $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

          $previous = $halaman - 1;
          $next = $halaman + 1;
          $adjacents = 2;

          // --- TOMBOL HAPUS HALAMAN (POST handler) ---
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_page']) && $_POST['delete_page'] == '1') {
            // pastikan halaman dikirim (untuk menghapus range yang benar)
            $del_page = isset($_POST['halaman']) ? (int) $_POST['halaman'] : 1;
            if ($del_page < 1)
              $del_page = 1;
            $del_awal = ($del_page > 1) ? ($del_page * $batas) - $batas : 0;

            // ambil id_sensor pada halaman tersebut
            $q = "SELECT sensor.id_sensor FROM sensor 
          INNER JOIN suhu ON suhu.id_sensor = sensor.id_sensor 
          ORDER BY sensor.id_sensor DESC 
          LIMIT $del_awal, $batas";
            $res = mysqli_query($connect, $q);
            $ids = [];
            if ($res && mysqli_num_rows($res) > 0) {
              while ($row = mysqli_fetch_assoc($res)) {
                $ids[] = (int) $row['id_sensor'];
              }
            }

            if (count($ids) > 0) {
              // ubah menjadi string untuk IN clause
              $id_list = implode(',', $ids);

              // hapus di tabel 'suhu' dulu (jika ada)
              $del1 = mysqli_query($connect, "DELETE FROM suhu WHERE id_sensor IN ($id_list)");
              // hapus di tabel 'sensor'
              $del2 = mysqli_query($connect, "DELETE FROM sensor WHERE id_sensor IN ($id_list)");

              if ($del2) {
                // redirect kembali ke halaman yang sama (refresh) untuk mengupdate tampilan
                header("Location: history.php?halaman=$halaman");
                exit;
              } else {
                echo '<div class="alert alert-danger">Gagal menghapus data: ' . mysqli_error($connect) . '</div>';
              }
            } else {
              echo '<div class="alert alert-warning">Tidak ada data pada halaman ini untuk dihapus.</div>';
            }
          }
          // --- END DELETE HANDLER ---
          ?>

          <!-- Tombol hapus halaman, ditempatkan di atas tabel -->
          <form method="post" onsubmit="return confirmDeletePage();" style="margin-bottom:12px;">
            <input type="hidden" name="delete_page" value="1">
            <input type="hidden" name="halaman" value="<?= htmlspecialchars($halaman) ?>">
            <button type="submit" class="btn btn-danger">Hapus Halaman Ini</button>
          </form>

          <table id="history" class="table table-defaut table-bordered mt-5">
            <tr>
              <th>No. </th>
              <th>Waktu</th>
              <th>Suhu</th>
              <th>CO</th>
              <th>CO2</th>
              <th>kelembaban</th>
              <th>Debu</th>
              <th>Keterangan</th>
            </tr>

            <?php
            $data = mysqli_query($connect, "SELECT * FROM sensor");
            $jumlah_data = mysqli_num_rows($data);
            $total_halaman = ($jumlah_data > 0) ? ceil($jumlah_data / $batas) : 1;
            $second_last = ($total_halaman > 1) ? $total_halaman - 1 : 1;

            $sql = mysqli_query($connect, "SELECT * FROM sensor 
          INNER JOIN suhu ON suhu.id_sensor = sensor.id_sensor ORDER BY sensor.id_sensor desc limit $halaman_awal, $batas");

            $nomor = $halaman_awal + 1;
            while ($d = mysqli_fetch_array($sql)) {
              ?>
                <tr>
                  <td><?= $nomor++ ?></td>
                  <td><?= $d['waktu'] ?></td>
                  <td><?= $d['suhu'] ?></td>
                  <td><?= $d['co'] ?></td>
                  <td><?= $d['co2'] ?></td>
                  <td><?= $d['kelembaban'] ?></td>
                  <td><?= $d['debu'] ?></td>
                  <td><?= $d['keterangan'] ?></td>
                </tr>

              <?php
            }
            ?>

          </table>

          <nav>
            <ul class="pagination justify-content-center">

              <li class="page-item" <?php if ($halaman <= 1) {
                echo "class='disabled'";
              } ?>><a class="page-link" <?php if ($halaman > 1) {
                 echo "href='?halaman=$previous'";
               } ?>>Previous</a></li>

              <?php if ($total_halaman <= 10) {
                for ($counter = 1; $counter <= $total_halaman; $counter++) {
                  if ($counter == $halaman) {
                    echo "<li  class='page-item active'><a class='page-link'>$counter</a></li>";
                  } else {
                    echo "<li  class='page-item'><a class='page-link' href='?halaman=$counter'>$counter</a></li>";
                  }
                }
              } elseif ($total_halaman > 10) {

                if ($halaman <= 4) {
                  for ($counter = 1; $counter < 8; $counter++) {
                    if ($counter == $halaman) {
                      echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
                    } else {
                      echo "<li class='page-item'><a class='page-link' href='?halaman=$counter'>$counter</a></li>";
                    }
                  }
                  echo "<li class='page-item'><a class='page-link'>...</a></li>";
                  echo "<li class='page-item'><a class='page-link' href='?halaman=$second_last'>$second_last</a></li>";
                  echo "<li class='page-item'><a class='page-link' href='?halaman=$total_halaman'>$total_halaman</a></li>";
                } elseif ($halaman > 4 && $halaman < $total_halaman - 4) {
                  echo "<li class='page-item'><a class='page-link' href='?halaman=1'>1</a></li>";
                  echo "<li class='page-item'><a class='page-link' href='?halaman=2'>2</a></li>";
                  echo "<li class='page-item'><a class='page-link'>...</a></li>";
                  for ($counter = $halaman - $adjacents; $counter <= $halaman + $adjacents; $counter++) {
                    if ($counter == $halaman) {
                      echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
                    } else {
                      echo "<li class='page-item'><a class='page-link' href='?halaman=$counter'>$counter</a></li>";
                    }
                  }
                  echo "<li class='page-item'><a class='page-link'>...</a></li>";
                  echo "<li class='page-item'><a class='page-link' href='?halaman=$second_last'>$second_last</a></li>";
                  echo "<li class='page-item'><a class='page-link' href='?halaman=$total_halaman'>$total_halaman</a></li>";
                } else {
                  echo "<li class='page-item'><a class='page-link'php href='?halaman=1'>1</a></li>";
                  echo "<li class='page-item'><a class='page-link' href='?halaman=2'>2</a></li>";
                  echo "<li class='page-item'><a class='page-link'>...</a></li>";

                  for ($counter = $total_halaman - 6; $counter <= $total_halaman; $counter++) {
                    if ($counter == $halaman) {
                      echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
                    } else {
                      echo "<li class='page-item'><a class='page-link' href='?halaman=$counter'>$counter</a></li>";
                    }
                  }
                }
              }
              ?>



              <li class="page-item" <?php if ($halaman >= $total_halaman) {
                echo "class='disabled'";
              } ?>><a
                  class="page-link" <?php if ($halaman < $total_halaman) {
                    echo "href='?halaman=$next'";
                  } ?>>Next</a>
              </li>

              <?php if ($halaman < $total_halaman) {
                echo "<li class='page-item'><a class='page-link' href='?halaman=$total_halaman'>Last &rsaquo;&rsaquo;</a></li>";
              } ?>
            </ul>
          </nav>
        </div>

      </div>
    </div>
    </div>
    </div>

  </section><!-- End Hero -->

  <!-- ======= Footer ======= -->

  <script src="assets/vendor/purecounter/purecounter.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <script>
    function confirmDeletePage() {
      return confirm('Anda yakin ingin menghapus semua data pada halaman ini? Tindakan ini tidak dapat dibatalkan.');
    }
  </script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <!--  <script>
      $(document).ready( function ($) {
        $.noConflict();
    $('#history').DataTable();
} );
    </script> -->
</body>

</html>