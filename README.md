# 🌬️ Sistem Pemantauan Kualitas Udara dalam Ruangan

Aplikasi web untuk memantau kualitas udara dalam ruangan secara real-time menggunakan sensor IoT.

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat&logo=bootstrap&logoColor=white)

## ✨ Fitur

- 📊 **Monitoring Real-time** - Memantau CO, CO2, Debu (PM10), Suhu, dan Kelembaban
- 🎛️ **Kontrol Perangkat** - Mengontrol Air Purifier dan Humidifier
- 📱 **Responsive Design** - Tampilan optimal di desktop dan mobile
- 🔐 **Authentication** - Sistem login/logout untuk keamanan
- 📈 **History Data** - Melihat riwayat data sensor dengan pagination

## 🚀 Instalasi

### Prasyarat
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web Server (Apache/Nginx) atau Laragon/XAMPP

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/username/sistem_pemantauan_kualitas_udara.git
   ```

2. **Pindahkan ke folder web server**
   - Laragon: `C:\laragon\www\`
   - XAMPP: `C:\xampp\htdocs\`

3. **Buat database**
   - Buka phpMyAdmin atau HeidiSQL
   - Buat database baru dengan nama: `skripsi`

4. **Import database**
   ```sql
   -- Import struktur database
   SOURCE skripsi (1).sql;
   
   -- Import tabel users
   SOURCE migration_users.sql;
   ```

5. **Konfigurasi database** (jika diperlukan)
   
   Edit file `config.php` jika konfigurasi database berbeda:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'skripsi');
   ```

6. **Akses aplikasi**
   ```
   http://localhost/sistem_pemantauan_kualitas_udara/
   ```

## 🔑 Login Default

| Username | Password |
|----------|----------|
| admin    | admin123 |

## 📁 Struktur File

```
sistem_pemantauan_kualitas_udara/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── img/
│   ├── js/
│   └── vendor/
├── jquery/
├── config.php          # Konfigurasi database & session
├── index.php           # Dashboard utama
├── login.php           # Halaman login
├── logout.php          # Handler logout
├── history.php         # Riwayat data sensor
├── kirimdata.php       # API endpoint untuk sensor IoT
├── suhu.php            # API suhu
├── co.php              # API karbon monoksida
├── co2.php             # API karbon dioksida
├── debu.php            # API partikel debu
├── kelembaban.php      # API kelembaban
├── control_set.php     # API kontrol perangkat
├── get_control.php     # API status kontrol
├── migration_users.sql # SQL untuk tabel users
└── skripsi (1).sql     # SQL struktur database utama
```

## 🛠️ Teknologi

- **Backend**: PHP Native
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5
- **Library**: jQuery, AOS (Animate On Scroll)

## 📸 Screenshot

### Dashboard
Menampilkan data sensor real-time dengan visualisasi yang menarik.

### Kontrol Perangkat
Panel kontrol modern untuk mengatur Air Purifier dan Humidifier.

## 📝 Lisensi

Project ini dibuat untuk keperluan skripsi/tugas akhir.

## 👤 Author

Dibuat dengan ❤️ untuk monitoring kualitas udara yang lebih baik.
