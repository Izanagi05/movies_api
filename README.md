# 🎬 SanzFlix API
Laravel REST API + JWT Authentication

Backend REST API untuk aplikasi **Movie Watchlist** yang menangani autentikasi pengguna dan manajemen data favorite/watchlist movie.  
Data movie diambil dari **TMDB API**, sedangkan backend hanya menyimpan relasi user dengan `tmdb_movie_id`.

---

## 👥 Anggota Kelompok
- Satria Sahrul Ramadhan (2405060)
- Izanagi Faris Aslam (2405060)
- Sunan M Karim Kadilaga (2405041)

---


## 📞 Contact Information 1
- Email   : your-email@example.com
- GitHub  : https://github.com/striaaaa
- Project : Movie Watchlist 

## 📞 Contact Information 2
- Email   : izanagifarisaslam5@gmail.com
- GitHub  : https://github.com/Izanagi05
- Project : Movie Watchlist 

## 📞 Contact Information 3
- Email   : sunmkjun11@gmail.com
- GitHub  : https://github.com/sjun11
- Project : Movie Watchlist 

---

## 🔗 Repository Terkait
- Frontend (Flutter): https://github.com/striaaaa/Movie-Mobile-apps
- Backend (Laravel): https://github.com/Izanagi05/movies_api

## 🛠️ Spesifikasi Backend

- PHP >= 8.1
- Laravel 9
- MySQL
- Composer
- JWT Authentication
- Ngrok (Development)

---

## 📦 Package yang Digunakan

| Package | Kegunaan |
|------|--------|
| laravel/framework | Framework utama |
| tymon/jwt-auth *(atau package JWT yang digunakan)* | JWT Authentication | 

> Pastikan package JWT sudah terkonfigurasi dengan benar di `.env`.

---

## 🏗️ Arsitektur Backend
- REST API
- JWT Authentication (Access Token & Refresh Token)
- Middleware Auth
- Database menggunakan MySQL
- Struktur database menggunakan Laravel Migration

---

## 📂 Struktur Database

### users
- id
- name
- email
- password

### favorites
- id
- user_id (FK)
- tmdb_movie_id
- created_at
- updated_at

> Satu user hanya dapat menyimpan satu movie yang sama (unique constraint).

---

## 🚀 Setup Backend

```bash
git clone https://github.com/Izanagi05/movies_api.git
cd movies-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
