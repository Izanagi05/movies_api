# 🎬 Movie Watchlist API
Laravel REST API + JWT Authentication

Backend REST API untuk aplikasi **Movie Watchlist** yang menangani autentikasi pengguna dan manajemen data favorite/watchlist movie.  
Data movie diambil dari **TMDB API**, sedangkan backend hanya menyimpan relasi user dengan `tmdb_movie_id`.

---

## 👥 Anggota Kelompok
- Nama 1 – NIM
- Nama 2 – NIM
- Nama 3 – NIM
- Nama 4 – NIM

---

## 🛠️ Spesifikasi Backend

- PHP >= 8.1
- Laravel >= 10
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
| laravel/sanctum *(jika ada)* | API authentication (opsional) |

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
git clone <repository-backend>
cd movie-watchlist-backend
composer install
cp .env.example .env
php artisan key:generate
