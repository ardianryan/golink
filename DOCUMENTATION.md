# Premium URL Shortener & Bio-Link Platform — System Documentation

Selamat datang di dokumentasi teknis dan panduan operasional lengkap untuk platform **URL Shortener, Bio Pages (Link-in-Bio), dan QR Code Studio**.

Platform ini telah dimodifikasi menjadi **100% Standalone & Mandiri**:
- **Bebas dari lisensi pihak ketiga dan 100% mandiri**.
- **Tanpa panggilan telemetri eksternal (phone-home)**.
- **Seluruh fitur Pro / Extended (Vouchers, Coupons, Tax, Subscriptions, Teams, Bio Widgets) aktif permanen**.

---

## Daftar Isi
1. [Arsitektur & Struktur Direktori](#1-arsitektur--struktur-direktori)
2. [Kebutuhan Server & Instalasi Pertama Kali](#2-kebutuhan-server--instalasi-pertama-kali)
3. [Konfigurasi Web Server (Nginx & Apache)](#3-konfigurasi-web-server-nginx--apache)
4. [Automasi & Cron Jobs](#4-automasi--cron-jobs)
5. [Fitur URL Shortener, Targeting & Analitik](#5-fitur-url-shortener-targeting--analitik)
6. [Bio Pages (Link-in-Bio) & QR Code Studio](#6-bio-pages-link-in-bio--qr-code-studio)
7. [Monetisasi, Billing & Integrasi Payment Gateway](#7-monetisasi-billing--integrasi-payment-gateway)
8. [Custom Domain & Konfigurasi DNS](#8-custom-domain--konfigurasi-dns)
9. [Keamanan & Anti-Abuse (Anti-Phishing / Malware)](#9-keamanan--anti-abuse-anti-phishing--malware)
10. [REST API Developer & Webhook](#10-rest-api-developer--webhook)
11. [Pengembangan Plugin & Kustomisasi Tema](#11-pengembangan-plugin--kustomisasi-tema)
12. [Pemeliharaan & Troubleshooting](#12-pemeliharaan--troubleshooting)

---

## 1. Arsitektur & Struktur Direktori

Platform dibangun menggunakan arsitektur MVC berbasis GemFramework:

```
shorturl/
├── app/                      # Inti Aplikasi MVC
│   ├── config/               # Konfigurasi aplikasi & routing (routes.php, api.php)
│   ├── controllers/          # Controller: admin/, user/, api/, dan PageController
│   ├── helpers/              # Helper logika: payments/, qr/, biowidgets/, App.php
│   ├── middleware/           # Proteksi request (Auth, Captcha, BlockBot, Throttle)
│   └── models/               # Model data database (ORM)
├── core/                     # Kernel Framework (Routing, DB abstraction, Request, View)
├── public/                   # Web Root (Document Root yang diarahkan oleh webserver)
│   ├── content/              # Upload pengguna (avatar, QR logos, profiles, files)
│   ├── static/               # Aset statis backend & frontend (CSS, JS, fonts)
│   └── index.php             # Front Controller utama
├── storage/                  # Penyimpanan dinamis internal
│   ├── app/                  # Data bantu (cron, MaxMind GeoIP, domains.txt)
│   ├── cache/                # File cache sistem & template
│   ├── languages/            # File translasi bahasa (.php)
│   ├── logs/                 # Catatan error dan eksekusi cron
│   ├── plugins/              # Direktori modul/plugin tambahan
│   └── themes/               # Tema frontend dan template admin
│       ├── default/          # Tema default & seluruh view admin panel
│       └── the23/            # Tema alternatif modern frontend
├── config.sample.php         # Template konfigurasi installer
├── config.php                # File konfigurasi aktif (dibuat saat instalasi)
└── DOCUMENTATION.md          # Dokumentasi ini
```

---

## 2. Kebutuhan Server & Instalasi Pertama Kali

### Kebutuhan Minimum:
- **PHP**: Versi 8.2 ke atas (Direkomendasikan PHP 8.2 atau 8.3 / 8.5).
- **Ekstensi PHP Wajib**:
  - `pdo_mysql`, `curl`, `mbstring`, `gd` atau `imagick` (untuk QR Code), `zip`, `json`, `intl`, `openssl`.
- **Database**: MySQL 5.7+ atau MariaDB 10.3+.
- **Web Server**: Nginx atau Apache dengan modul `mod_rewrite`.

### Langkah Instalasi Wizard (Browser GUI):
1. Jalankan web server lokal atau arahkan domain ke folder `public/`:
   ```bash
   php -S localhost:8000 -t public
   ```
2. Buka `http://localhost:8000/` di browser. Sistem otomatis mendeteksi ketiadaan `config.php` dan membuka `/setup`.
3. Pastikan semua indikator dependensi berwarna hijau, lalu klik **Proceed**.
4. Masukkan kredensial MySQL lokal (Host, Database Name, User, Password).
5. Buat akun Administrator pertama (Email & Password).
6. Instalasi selesai! Anda akan langsung diarahkan ke Dashboard Admin (`/admin`).

---

## 3. Konfigurasi Web Server (Nginx & Apache)

### Nginx (Rekomendasi Produksi)
Pastikan `root` mengarah ke subfolder `/public`, bukan root project:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name mydomain.com *.mydomain.com;
    root /var/www/shorturl/public;
    index index.php index.html;

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Blokir akses ke file tersembunyi
    location ~ /\. {
        deny all;
    }

    # Cache file statis
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2?|svg)$ {
        expires 30d;
        access_log off;
    }
}
```

### Apache
Pastikan modul `mod_rewrite` aktif. File `public/.htaccess` sudah tersedia secara default.

---

## 4. Automasi & Cron Jobs

Untuk mengelola masa berlaku paket langganan, pengingat invoice, auto-delete link kadaluarsa, dan agregasi data statistik, pasang cron job di server:

Buka crontab:
```bash
crontab -e
```

Tambahkan baris berikut:
```bash
# Eksekusi pengecekan langganan dan pengingat (setiap jam)
0 * * * * wget -q -O - https://domainanda.com/cron >/dev/null 2>&1

# Agregasi data analitik dan pembersihan log klik usang (setiap malam jam 00:00)
0 0 * * * php /var/www/shorturl/storage/app/cron.php >/dev/null 2>&1
```

---

## 5. Fitur URL Shortener, Targeting & Analitik

### A. Dynamic Redirection & Targeting
1. **Geotargeting**: Arahkan pengunjung ke URL berbeda berdasarkan negara asal (misal pengunjung dari ID ke link lokal, pengunjung dari US ke link global).
2. **Device & OS Targeting**: Bedakan link tujuan untuk perangkat Android (Play Store), iOS (App Store), atau Desktop.
3. **Language Targeting**: Redirection otomatis berdasarkan bahasa browser pengguna.
4. **Rotator / A-B Testing**: Distribusikan trafik ke beberapa target URL secara acak atau proporsional.

### B. Proteksi & Opsi Tautan
- **Password Protection**: Pengunjung wajib memasukkan password sebelum diarahkan.
- **Link Expiration**: Menentukan batas waktu kadaluarsa berdasarkan tanggal atau batas jumlah klik.
- **Custom Alias**: Memberikan nama slug kustom (contoh: `domain.com/promo-merdeka`).

### C. Pixel Retargeting & Intermediary Pages
Pengguna dapat memasukkan pixel pelacakan:
- Meta (Facebook) Pixel, Google Analytics (GA4) / GTM, TikTok Pixel, Twitter Pixel, LinkedIn Insight Tag.
- Pilihan tipe redirect: **Direct (301/302)**, **Custom Splash Page** (dengan countdown & logo), atau **Overlay Page** (banner CTA di atas website tujuan).

---

## 6. Bio Pages (Link-in-Bio) & QR Code Studio

### Bio Pages Builder
Pengguna dapat membuat halaman mikro (*mobile landing page*) serbaguna:
- **Tema & Desain**: Pilihan warna gradient, custom background image, font Google Fonts, dan CSS kustom.
- **Widget Komponen**:
  - Tautan tombol animasi
  - Embed YouTube, Spotify playlist, TikTok video, SoundCloud
  - Formulir Newsletter capture (integrasi Mailchimp/webhook)
  - Formulir Kontak pesan langsung & tombol WhatsApp / Call
  - Tombol donasi PayPal
  - Kartu kontak vCard (.vcf)

### QR Code Generator Studio
Studio QR Code dinamis dengan kemampuan:
- **Desain Khusus**: Pilihan pola titik (Dots, Rounded, Square, Diamond, Heart).
- **Warna & Gradient**: Warna solid atau gradien multi-warna (linear / radial).
- **Logo Branding**: Sematkan logo di tengah QR code.
- **Frame & CTA**: Frame dengan teks ajakan bertindak (contoh: *"SCAN SAYA"*).
- **Format Download**: PNG resolusi tinggi, SVG vector, WEBP, dan PDF.

---

## 7. Monetisasi, Billing & Integrasi Payment Gateway

Platform mendukung model SaaS berbasis langganan bulanan/tahunan atau pembayaran satu kali:

### Pengaturan Paket (Membership Plans)
Buka menu `Admin -> Subscriptions -> Plans`:
- Tentukan kuota jumlah link, bio page, QR code, custom domain, dan limit klik per bulan.
- Atur status Free, Pro, Enterprise, dsb.

### Integrasi Payment Gateway
Konfigurasi API key di `Admin -> Settings -> Payment Gateways`:
1. **Stripe**:
   - Isi `Publishable Key`, `Secret Key`, dan `Webhook Secret`.
   - Pasang webhook di dashboard Stripe ke URL: `https://domainanda.com/webhook/stripe`.
   - Event yang perlu didengarkan: `checkout.session.completed`, `invoice.paid`, `customer.subscription.deleted`.
2. **PayPal**:
   - Isi `Client ID` dan `Client Secret` dari PayPal Developer Portal.
   - Webhook URL: `https://domainanda.com/webhook/paypal`.
3. **Transfer Bank (Offline Payment)**:
   - Pengguna mengunggah bukti bayar.
   - Admin memeriksa dan menyetujui aktivasi invoice di `Admin -> Subscriptions -> Invoices`.

### Kupon Diskon & Pajak (Tax Rates)
- **Coupons**: Buat kode promo dengan potongan persentase atau nominal tetap.
- **Tax Rates**: Atur PPN/pajak berdasarkan negara pengguna secara otomatis.

---

## 8. Custom Domain & Konfigurasi DNS

Pengguna dapat menghubungkan domain atau subdomain mereka sendiri untuk branding link:

### Konfigurasi Pengguna:
- **Root Domain (`namabrand.com`)**: Buat DNS **A Record** mengarah ke IP Server Anda.
- **Subdomain (`link.namabrand.com`)**: Buat DNS **CNAME Record** mengarah ke domain utama Anda.

### Konfigurasi Server:
- Pastikan Nginx/Apache memiliki konfigurasi *Wildcard* atau reverse proxy (seperti Cloudflare SSL) agar custom domain yang masuk dapat langsung menyajikan sertifikat SSL.

---

## 9. Keamanan & Anti-Abuse (Anti-Phishing / Malware)

1. **Blacklist Domain & Kata Kunci**:
   - Atur domain terlarang atau kata kunci judi/phishing di `Admin -> Settings -> Security`.
   - Dapat memanfaatkan file daftar di `storage/app/domains.txt` dan `storage/app/keywords.txt`.
2. **Integrasi Google Cloud Web Risk API**:
   - Masukkan API Key dari Google Cloud Console untuk memeriksa setiap URL tujuan secara otomatis dari ancaman Malware dan Social Engineering / Phishing.
3. **Proteksi Bot & Brute Force**:
   - Dilengkapi rate limiter bawaan pada route pembuatan link dan autentikasi.
   - Dukungan CAPTCHA: Cloudflare Turnstile, reCAPTCHA v2/v3, dan Altcha.

---

## 10. REST API Developer & Webhook

Dokumentasi interaktif endpoint REST API lengkap tersedia langsung di aplikasi pada URL:
`https://domainanda.com/developers` (atau klik menu **Developer API** di navigasi).

### Autentikasi:
Kirimkan header `Authorization: Bearer <API_KEY>` pada setiap request.

### Contoh Request Pembuatan Link (cURL):
```bash
curl -X POST https://domainanda.com/api/url/add \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://websiteanda.com/produk",
    "custom": "produk-unggulan",
    "pass": "",
    "domain": ""
  }'
```

### Response JSON:
```json
{
  "error": 0,
  "id": 1024,
  "shorturl": "https://domainanda.com/produk-unggulan"
}
```

---

## 11. Pengembangan Plugin & Kustomisasi Tema

### Struktur Plugin
Semua plugin ditempatkan di `storage/plugins/{nama-plugin}/`:
- `config.json`: Metadata plugin (nama, deskripsi, versi, kelas bootstrap).
- `plugin.php`: Logika eksekusi yang mengaitkan event menggunakan `\Core\Plugin::add()`.

**Event Hooks Populer**:
- `shorten.start`: Sebelum link dipendekkan (bisa modifikasi destination URL).
- `shorten.complete`: Setelah link berhasil disimpan di database.
- `redirect.start`: Sebelum redirect terjadi (untuk inspeksi klik / analitik tambahan).
- `apidocs.extend`: Menambahkan dokumentasi endpoint baru ke halaman `/developers`.

### Kustomisasi Tema
- Folder tema berada di `storage/themes/`.
- Template layout menggunakan sintaks PHP native yang bersih (`storage/themes/default/` dan `storage/themes/the23/`).

---

## 12. Pemeliharaan & Troubleshooting

### Membersihkan Cache Aplikasi
Jika perubahan pengaturan atau file translasi belum langsung tampak:
```bash
rm -rf storage/cache/*
```

### Memeriksa Log Error
Jika terjadi kendala eksekusi:
```bash
tail -f storage/logs/*.log
```

### Izin File & Direktori (Permissions)
Jika file upload atau avatar gagal disimpan:
```bash
chmod -R 775 storage/ public/content/
```

---

*Dokumentasi ini juga dapat diakses langsung dari dalam antarmuka Admin Panel di menu: **Admin -> Documentation** (`/admin/docs`).*
