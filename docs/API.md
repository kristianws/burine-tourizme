# Tourizme API Documentation

Dokumentasi ini dibuat berdasarkan implementasi saat ini pada `routes/api.php` dan controller terkait.

## Base URL

- Local: `http://localhost:8000/api`

## Authentication

API menggunakan Laravel Sanctum Bearer Token.

- Header untuk endpoint yang butuh login:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

## Response Format

Sebagian besar endpoint menggunakan format dari trait `ApiResponse`:

```json
{
    "success": true,
    "message": "Data Ditemukan",
    "data": {}
}
```

Error default dari `errorResponse`:

```json
{
    "success": false,
    "message": "Pesan error",
    "data": []
}
```

Catatan:

- Ada beberapa endpoint yang return format berbeda (lihat bagian "Catatan Implementasi").

## Public Endpoints (Tanpa Token)

### 1. Login

- Method: `POST`
- URL: `/login`
- Auth: tidak

Request body:

```json
{
    "identity": "email@domain.com atau username",
    "password": "secret"
}
```

Success 200:

```json
{
    "success": true,
    "message": "login berhasil",
    "data": {
        "username": "johndoe",
        "role": "tourist",
        "access_token": "<token>",
        "token_type": "Bearer"
    }
}
```

Validasi gagal 422 (format khusus):

```json
{
    "status": "error",
    "message": "identity wajib diisi"
}
```

### 2. Register

- Method: `POST`
- URL: `/register`
- Auth: tidak

Request body:

```json
{
    "fullname": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "tourist"
}
```

`role` yang diterima: `tourist`, `bisnis_owner`

Success 201:

```json
{
    "success": true,
    "message": "register berhasil",
    "data": {
        "id": 1,
        "username": "johndoe",
        "role": "tourist"
    }
}
```

### 3. List Destinations

- Method: `GET`
- URL: `/destinations`
- Auth: tidak

Success 200 (dipaginate):

```json
{
    "success": true,
    "message": "Data Destinasi Wisata Ditemukan",
    "data": [
        {
            "id": 1,
            "name": "Pantai A",
            "location": "Bali",
            "price": 25000,
            "description": "...",
            "open_time": "08:00:00",
            "close_time": "17:00:00",
            "thumbnail": "https://...",
            "images": [],
            "category": {},
            "bisnis_owner": {},
            "reviews": []
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}
```

### 4. Search Destinations

- Method: `GET`
- URL (route saat ini): `/destinations/search`
- Query param (implementasi controller): `query`
- Auth: tidak

Contoh:

```http
GET /api/destinations/search?query=bali
```

## Protected Endpoints (Auth Sanctum)

### 5. Logout

- Method: `POST`
- URL: `/logout`
- Auth: `auth:sanctum`

Success 200:

```json
{
    "success": true,
    "message": "logout berhasil"
}
```

### 6. Current User Profile

- Method: `GET`
- URL: `/me`
- Auth: `auth:sanctum`

Success 200:

```json
{
    "success": true,
    "message": "User ditemukan",
    "data": {
        "id": 1,
        "fullname": "John Doe",
        "username": "johndoe",
        "email": "john@example.com",
        "profile_picture": "profile/1/file.jpg",
        "profile_picture_url": "https://...",
        "role": "tourist",
        "bisnis_owner": null
    }
}
```

### 7. Update Profile

- Method: `PATCH`
- URL: `/me`
- Auth: `auth:sanctum`
- Content type: `multipart/form-data` (karena bisa upload gambar)

Body (opsional semua):

- `name` string max 255
- `email` email unique (kecuali user sendiri)
- `password` string min 8
- `profile_picture` image (`jpeg,png,jpg,gif,svg`) max 2MB

Success 200:

```json
{
    "success": true,
    "message": "Profil berhasil diperbarui",
    "data": {
        "user": {
            "id": 1,
            "fullname": "John Doe",
            "username": "johndoe",
            "email": "john@example.com"
        },
        "profile_picture_url": "https://..."
    }
}
```

## Tourist Endpoints

Prefix: `/tourist`  
Middleware: `auth:sanctum`, `role:tourist`

### 8. Register Bisnis Owner

- Method: `POST`
- URL: `/tourist/register-bisnis-owner/`
- Content type: `multipart/form-data`

Body:

- `user_id` required, exists users.id
- `nik` required, unique
- `ktp_photo` required
- `nib` required, unique

Success 201:

```json
{
    "success": true,
    "message": "Bisnis owner registered successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "nik": "...",
        "ktp_photo": "/storage/uploads/ktp_images/...",
        "nib": "..."
    }
}
```

### 9. Tourist Update Profile

- Method: `POST`
- URL: `/tourist/profile/update/`
- Catatan: memanggil method yang sama dengan endpoint `/me` (`UserController@update`)

### 10. Get Reviews by Destination

- Method: `GET`
- URL: `/tourist/reviews/{destinationId}`

Success 200:

```json
{
    "success": true,
    "message": "Data Ditemukan",
    "data": [
        {
            "id": 1,
            "username": "user1",
            "profile_picture": "...",
            "rating": 5,
            "description": "Bagus"
        }
    ]
}
```

### 11. Create Review

- Method: `POST`
- URL: `/tourist/reviews/new`

Body:

```json
{
    "destination_id": 1,
    "rating": 5,
    "description": "Tempatnya bagus"
}
```

### 12. Update Review

- Method: `PATCH`
- URL: `/tourist/reviews/update`

Body:

```json
{
    "review_id": 1,
    "rating": 4,
    "description": "Update ulasan"
}
```

### 13. Add Wishlist

- Method: `POST`
- URL: `/tourist/wishlists/add/{id}`

Body (sesuai validasi controller):

```json
{
    "destination_id": 1
}
```

Success 200:

```json
{
    "success": true,
    "message": "Destinasi Masuk ke Wishlist",
    "data": "Destinasi Masuk ke Wishlist"
}
```

### 14. Remove Wishlist

- Method: `DELETE`
- URL: `/tourist/wishlists/remove/{id}`

### 15. List Wishlist

- Method: `GET`
- URL: `/tourist/wishlists/`

### 16. List Itineraries

- Method: `GET`
- URL: `/tourist/itineraries/`

### 17. Create Itinerary

- Method: `POST`
- URL: `/tourist/itineraries/new`

Body:

```json
{
    "title": "Trip Bali 3 Hari",
    "start_date": "2026-06-15"
}
```

### 18. Get Itinerary Detail

- Method: `GET`
- URL: `/tourist/itineraries/{itinerary}`

### 19. List Itinerary Items

- Method: `GET`
- URL: `/tourist/itineraries/{itinerary}/items`

### 20. Add Itinerary Item

- Method: `POST`
- URL: `/tourist/itineraries/{itinerary}/items`

Body:

```json
{
    "destination_id": 1,
    "day": 1,
    "sequence_order": 1,
    "start_time": "08:00",
    "end_time": "10:00"
}
```

### 21. Delete Itinerary Item

- Method: `DELETE`
- URL: `/tourist/itineraries/{itinerary}/items/{itineraryItem}`

## Bisnis Owner Endpoints

Prefix: `/bisnis-owner`  
Middleware: `auth:sanctum`, `role:bisnis_owner`

### 22. Dashboard

- Method: `GET`
- URL: `/bisnis-owner/dashboard/`

### 23. List Destinations (Owner)

- Method: `GET`
- URL: `/bisnis-owner/destinations/`

### 24. Create Destination

- Method: `POST`
- URL: `/bisnis-owner/destinations/`
- Content type: `multipart/form-data`

Body:

- `category_name` required integer
- `name` required string
- `gmaps` required string
- `location` required string
- `price` required numeric
- `description` required string
- `open_time` required format `H:i:s`
- `close_time` required format `H:i:s`
- `thumbnail` required file image

### 25. Update Destination

- Method: `PATCH`
- URL: `/bisnis-owner/destinations/{id}/update/`

Body:

- `category_id` required
- `name` required
- `description` required
- `location` required
- `open_time` required
- `close_time` required

### 26. Reply Review

- Method: `PATCH`
- URL: `/bisnis-owner/destinations/{id}/reply-review/`

Body:

```json
{
    "owner_reply": "Terima kasih atas ulasannya"
}
```

## Admin Endpoints

Prefix: `/admin`  
Middleware: `auth:sanctum`, `role:admin`

### 27. Approve Bisnis Owner

- Method: `PATCH`
- URL: `/admin/bisnisOwners/{bisnisOwner}/approve`

### 28. Reject Bisnis Owner

- Method: `PATCH`
- URL: `/admin/bisnisOwners/{bisnisOwner}/reject`

Body:

```json
{
    "verification_notes": "Data belum lengkap"
}
```

### 29. Approve Destination

- Method: `PATCH`
- URL: `/admin/destinations/{destination}/approve`

### 30. Reject Destination

- Method: `PATCH`
- URL: `/admin/destinations/{destination}/reject`

Body:

```json
{
    "notes": "Foto thumbnail tidak sesuai"
}
```

### 31. Delete Destination (Soft Status)

- Method: `PATCH`
- URL: `/admin/destinations/{destination}/delete`

Body:

```json
{
    "notes": "Melanggar kebijakan"
}
```

### 32. Set Destination Pending

- Method: `PATCH`
- URL: `/admin/destinations/{destination}/pending`

## Catatan Implementasi (Penting)

Berikut hal-hal yang perlu diperhatikan karena berpotensi menyebabkan endpoint tidak berjalan sesuai dokumentasi:

1. Route `GET /destinations/search` memanggil `DestinationController@searchReviews`, tetapi method yang ada adalah `search`.
2. Route `PATCH /me/profile-picture` terdaftar, namun method `updateProfilePicture` tidak ada di `UserController`.
3. `UserController@update` melakukan `$request->file('profile_picture')` tanpa guard; jika request tanpa file bisa error.
4. `UserController@update` menyimpan `password` tanpa hashing.
5. `UserController@update` memanggil `$user->update(...)` lalu variabel `$user` berubah jadi boolean, tapi dipakai lagi sebagai object.
6. `RegisBisnisOwner` rule `ktp_photo` menggunakan kombinasi `string|accepted|mimes`, tidak konsisten untuk file upload.
7. Route wishlist add memakai path param `{id}`, tetapi controller memakai body `destination_id`.
8. Route reply review `/bisnis-owner/destinations/{id}/reply-review/` tidak cocok dengan signature method `replyReview(Review $review, ...)`.
9. Route admin menggunakan `{destination}` tapi method menerima `int $id`; nama parameter tidak sinkron.
10. `DestinationController@index` memanggil `paginate(10)->get()` (kombinasi tidak valid).
11. `DestinationController@store` rule `thumbnail` typo `reqired`.
12. `DestinationController@update` cek kepemilikan pakai field `mitra_id`, kemungkinan tidak ada di model destination.
13. `WishlistController@show` memperlakukan collection wishlist seperti single model.
14. `ReviewResource` memakai `$this->user->name`, sementara data user cenderung memakai `fullname`/`username`.
15. `UserResource` memakai `Storage` tetapi import class `Storage` tidak ada.

Jika Anda mau, dokumentasi ini bisa dilanjutkan ke format OpenAPI (`openapi.yaml`) agar bisa dipakai Swagger UI/Postman generator.
