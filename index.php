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

  <title>Sistemm Pemantauan Kualitas Udara dalam Ruangan</title>
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

  <script src="jquery/jquery.min.js"></script>

  <style>
    /* tambahan styling kecil untuk header suhu/kelembaban */
    .time-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .time-row .left {
      flex: 1;
    }

    .time-row .right {
      display: flex;
      gap: 1rem;
      align-items: center;
      justify-content: flex-end;
    }

    .header-sensor {
      text-align: right;
    }

    .header-sensor .suhu-large {
      font-size: 1.6rem;
      font-weight: 600;
    }

    .header-sensor .hum-small {
      font-size: 1rem;
      color: #555;
      margin-left: 0.5rem;
    }

    @media (max-width:767px) {
      .time-row {
        flex-direction: column;
        align-items: flex-start;
      }

      .time-row .right {
        justify-content: flex-start;
      }

      .header-sensor {
        text-align: left;
      }
    }
  </style>

  <script type="text/javascript">
    $(document).ready(function () {
      // update semua nilai tiap 3 detik
      function updateAll() {
        // suhu -> load ke elemen #suhu (yang sudah ada di header)
        $.get("suhu.php", function (data) {
          $("#suhu").text($.trim(data)); // header suhu
        });

        // kelembaban -> ambil sekali dan set ke dua tempat:
        $.get("kelembaban.php", function (data) {
          var v = $.trim(data);
          $("#kelembaban").text(v);            // di card (icon-box)
          $("#kelembaban_header").text(v);     // di header sebelah suhu
        });

        // lain-lain tetap load normal (co, co2, debu, kualitas, himbauan)
        $("#co").load("co.php");
        $("#co2").load("co2.php");
        $("#debu").load("debu.php");
        $("#kualitas").load("keterangan.php");
        $("#himbauan").load("himbauan.php");
      }

      // jalankan segera dan interval
      updateAll();
      setInterval(updateAll, 3000);
    });
  </script>

</head>

<body>
  <?php date_default_timezone_set("Asia/Jakarta"); ?>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">

      <h1 class="logo"><a href=".">Pemantau Kualitas Udara dalam Ruangan</a></h1>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link" href=".">Home</a></li>
          <li><a class="nav-link" href="history.php">History</a></li>
          <li class="dropdown">
            <a href="#"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($currentUser['nama']) ?> <i
                class="bi bi-chevron-down"></i></a>
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

      <!-- time + suhu+kelembaban row -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="time-row">
            <div class="left">
              <h3><span id="waktu"><?php echo date('H:i:s'); ?></span></h3>
            </div>
            <div class="right header-sensor">
              <!-- suhu utama di header (ID #suhu dipakai oleh ajax) -->
              <div style="display:inline-block;">
                <span class="suhu-large"><span id="suhu">0</span>°C</span>
              </div>
              <!-- kelembaban header (ID baru #kelembaban_header) -->
              <div style="display:inline-block;">
                <span class="hum-small">| Kelembaban: <span id="kelembaban_header">0</span>%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9 text-center">
          <div class="card text-center">
            <div class="card-body">
              <span id="kualitas"></span>
            </div>
          </div>
          <h2><span id="himbauan">dapat beraktivitas dengan baik</span></h2>
        </div>
      </div>

      <div class="row icon-boxes mt-4">
        <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="200">
          <div class="counts icon-box">
            <div class="count-box">
              <span id="co">0</span>
              <h3>ppm</h3>
              <p>Karbon Monoksida (CO)</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="300">
          <div class="icon-box">
            <div class="counts">
              <div class="count-box">
                <span id="co2">0</span>
                <h3>ppm</h3>
                <p>Karbon Dioksida (CO2)</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Debu -->
        <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="500">
          <div class="icon-box">
            <div class="counts">
              <div class="count-box">
                <span id="debu">0</span>
                <h3>mg/m3</h3>
                <p>Debu (PM10)</p>
              </div>
            </div>
          </div>
        </div>
        <!-- START Control panel -->
        <div class="col-12 mt-4" data-aos="fade-up" data-aos-delay="400">
          <div class="control-card-modern">
            <div class="control-header">
              <div class="control-icon">
                <i class="bi bi-sliders"></i>
              </div>
              <div class="control-title">
                <h5>Kontrol Perangkat</h5>
                <span class="control-subtitle">Atur mode dan perangkat</span>
              </div>
              <div class="control-status-badge" id="statusBadge">
                <i class="bi bi-circle-fill"></i>
                <span id="controlStatus">loading...</span>
              </div>
            </div>

            <div class="control-body">
              <!-- Mode Control -->
              <div class="control-item">
                <div class="control-item-header">
                  <i class="bi bi-gear-wide-connected"></i>
                  <span>Mode Operasi</span>
                </div>
                <div class="control-buttons">
                  <button id="modeAuto" class="ctrl-btn ctrl-btn-auto">
                    <i class="bi bi-robot"></i>
                    <span>Auto</span>
                  </button>
                  <button id="modeManual" class="ctrl-btn ctrl-btn-manual">
                    <i class="bi bi-hand-index"></i>
                    <span>Manual</span>
                  </button>
                </div>
              </div>

              <!-- Purifier Control -->
              <div class="control-item">
                <div class="control-item-header">
                  <i class="bi bi-wind"></i>
                  <span>Air Purifier</span>
                </div>
                <div class="control-buttons">
                  <button id="purOn" class="ctrl-btn ctrl-btn-on">
                    <i class="bi bi-power"></i>
                    <span>ON</span>
                  </button>
                  <button id="purOff" class="ctrl-btn ctrl-btn-off">
                    <i class="bi bi-stop-circle"></i>
                    <span>OFF</span>
                  </button>
                </div>
              </div>

              <!-- Humidifier Control -->
              <div class="control-item">
                <div class="control-item-header">
                  <i class="bi bi-droplet-half"></i>
                  <span>Humidifier</span>
                </div>
                <div class="control-buttons">
                  <button id="humOn" class="ctrl-btn ctrl-btn-on">
                    <i class="bi bi-power"></i>
                    <span>ON</span>
                  </button>
                  <button id="humOff" class="ctrl-btn ctrl-btn-off">
                    <i class="bi bi-stop-circle"></i>
                    <span>OFF</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          // Audio context for notifications (mobile-friendly)
          var audioCtx = null;
          var audioUnlocked = false;
          
          // Unlock audio on first user interaction (required for mobile)
          function unlockAudio() {
            if (audioUnlocked) return;
            try {
              audioCtx = new (window.AudioContext || window.webkitAudioContext)();
              // Create silent buffer to unlock
              var buffer = audioCtx.createBuffer(1, 1, 22050);
              var source = audioCtx.createBufferSource();
              source.buffer = buffer;
              source.connect(audioCtx.destination);
              source.start(0);
              audioUnlocked = true;
              console.log('Audio unlocked!');
            } catch(e) {
              console.log('Audio unlock failed');
            }
          }
          
          // Listen for first interaction to unlock audio
          document.addEventListener('click', unlockAudio, { once: true });
          document.addEventListener('touchstart', unlockAudio, { once: true });
          
          // Play notification sound
          function playNotifSound() {
            try {
              if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
              }
              
              // Resume if suspended (mobile requirement)
              if (audioCtx.state === 'suspended') {
                audioCtx.resume();
              }
              
              var oscillator = audioCtx.createOscillator();
              var gainNode = audioCtx.createGain();
              
              oscillator.connect(gainNode);
              gainNode.connect(audioCtx.destination);
              
              oscillator.frequency.value = 800; // Hz
              oscillator.type = 'sine';
              gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
              gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
              
              oscillator.start(audioCtx.currentTime);
              oscillator.stop(audioCtx.currentTime + 0.3);
            } catch(e) {
              console.log('Audio not supported');
            }
          }
          
          // Toast notification function
          function showToast(message, type) {
            // Remove existing toast
            $('.toast-notification').remove();
            
            // Play sound
            playNotifSound();
            
            // Create toast element with different colors based on type
            var iconClass, bgColor;
            switch(type) {
              case 'success': // ON actions - Green
                iconClass = 'bi-check-circle-fill';
                bgColor = 'linear-gradient(135deg, #00c853, #00a844)';
                break;
              case 'warning': // OFF actions - Red/Orange
                iconClass = 'bi-x-circle-fill';
                bgColor = 'linear-gradient(135deg, #ff5252, #d32f2f)';
                break;
              case 'info': // Mode changes - Blue
                iconClass = 'bi-gear-fill';
                bgColor = 'linear-gradient(135deg, #2487ce, #1e6ca6)';
                break;
              default:
                iconClass = 'bi-info-circle-fill';
                bgColor = 'linear-gradient(135deg, #6c757d, #495057)';
            }
            
            var toast = $('<div class="toast-notification">' +
              '<i class="bi ' + iconClass + '"></i>' +
              '<span>' + message + '</span>' +
            '</div>');
            
            toast.css({
              'position': 'fixed',
              'bottom': '30px',
              'right': '30px',
              'background': bgColor,
              'color': 'white',
              'padding': '16px 24px',
              'border-radius': '12px',
              'box-shadow': '0 10px 40px rgba(0,0,0,0.3)',
              'display': 'flex',
              'align-items': 'center',
              'gap': '12px',
              'font-family': 'Poppins, sans-serif',
              'font-size': '14px',
              'font-weight': '500',
              'z-index': '9999',
              'animation': 'slideInRight 0.4s ease'
            });
            
            $('body').append(toast);
            
            // Auto hide after 3 seconds
            setTimeout(function() {
              toast.css('animation', 'slideOutRight 0.4s ease');
              setTimeout(function() {
                toast.remove();
              }, 400);
            }, 3000);
          }
          
          function ajaxSet(params, message, type, cb) {
            $.get("control_set.php", params)
              .done(function (resp) { 
                showToast(message, type);
                if (cb) cb(null, resp); 
              })
              .fail(function (xhr) { 
                showToast('Gagal mengubah pengaturan!', 'error');
                if (cb) cb(xhr); 
              });
          }

          function refreshControlStatus() {
            $.get("get_control.php").done(function (resp) {
              $("#controlStatus").text(resp);
              
              // Parse response: mode=manual&purifier=0&humidifier=0
              var params = {};
              resp.split('&').forEach(function(pair) {
                var kv = pair.split('=');
                params[kv[0]] = kv[1];
              });
              
              // Update Mode buttons
              if (params.mode === 'auto') {
                $("#modeAuto").addClass('active');
                $("#modeManual").removeClass('active');
                $("#statusBadge").removeClass('manual').addClass('auto');
              } else {
                $("#modeAuto").removeClass('active');
                $("#modeManual").addClass('active');
                $("#statusBadge").removeClass('auto').addClass('manual');
              }
              
              // Update Purifier buttons
              if (params.purifier === '1') {
                $("#purOn").addClass('active');
                $("#purOff").removeClass('active');
              } else {
                $("#purOn").removeClass('active');
                $("#purOff").addClass('active');
              }
              
              // Update Humidifier buttons
              if (params.humidifier === '1') {
                $("#humOn").addClass('active');
                $("#humOff").removeClass('active');
              } else {
                $("#humOn").removeClass('active');
                $("#humOff").addClass('active');
              }
              
            }).fail(function () { $("#controlStatus").text("Error"); });
          }

          $(function () {
            $("#modeAuto").click(() => ajaxSet({ mode: 'auto' }, '⚙ Mode Auto diaktifkan', 'info', refreshControlStatus));
            $("#modeManual").click(() => ajaxSet({ mode: 'manual' }, '⚙ Mode Manual diaktifkan', 'default', refreshControlStatus));

            $("#purOn").click(() => ajaxSet({ purifier: 1, mode: 'manual' }, '✓ Air Purifier dinyalakan', 'success', refreshControlStatus));
            $("#purOff").click(() => ajaxSet({ purifier: 0, mode: 'manual' }, '✕ Air Purifier dimatikan', 'warning', refreshControlStatus));
            $("#humOn").click(() => ajaxSet({ humidifier: 1, mode: 'manual' }, '✓ Humidifier dinyalakan', 'success', refreshControlStatus));
            $("#humOff").click(() => ajaxSet({ humidifier: 0, mode: 'manual' }, '✕ Humidifier dimatikan', 'warning', refreshControlStatus));

            refreshControlStatus();
            setInterval(refreshControlStatus, 5000);
          });
        </script>
        
        <style>
          @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
          }
          @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
          }
        </style>
        <!-- END Control panel -->


      </div> <!-- akhir row icon-boxes -->

    </div>
  </section>


  <!-- ======= Footer ======= -->
  <script src="assets/vendor/purecounter/purecounter.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <script type="text/javascript">
    // jam live
    window.onload = function () { waktu(); }
    function waktu() {
      var e = document.getElementById('waktu'),
        d = new Date(), h, m, s;
      h = d.getHours();
      m = setZero(d.getMinutes());
      s = setZero(d.getSeconds());

      e.innerHTML = h + ':' + m + ':' + s;

      setTimeout(waktu, 1000);
    }
    function setZero(e) {
      e = e < 10 ? '0' + e : e;
      return e;
    }
  </script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>