# LANGKAH - LANGKAH INSTALASI
### Prasyarat
- XAMPP v8.2.12
- Composer
- Node.js
- Git
- VS Code

### 1. Clone Repositori Menggunakan Git Bash
- Buka folder xampp/htdocs
- Klik kanan > git bash here
- Clone repositori
```
git clone https://github.com/FaridFitriansahAlfarizi/interview-laravel.git
```
### 2. Setup Project Dengan Visual Studio Code
- Buka folder interview-laravel menggunakan Visual Studio Code
- Buka terminal pada Visual Studio Code
- Install composer
```
composer install
```
- Install NPM
```
npm install
```
- Copy Paste file .env.example melalui file explorer
- Rename menjadi .env
- Aktifkan Apache dan MySQL pada XAMPP
- Kembali ke terminal Visual Studio Code
- Generate Key Laravel
```
php artisan key:generate
```
- Buka file .env menggunakan Visual Studio Code
- Pastikan APP_KEY sudah berisi
- Kembali ke terminal Visual Studio Code
- Migrasi database
```
php artisan migrate
```
- Mengaktifkan storage
```
php artisan storage:link
```
- Buka terminal baru dan jalankan serve php
```
php artisan serve
```
### 3. Masuk Ke Dalam Website
- Buka browser
- Ketikkan pada URL
```
127.0.0.1:8000
```
- Maka akan langsung di arahkan ke halaman Daftar Produk