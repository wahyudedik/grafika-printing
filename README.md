1. copy .env.example to .env
2. composer install
3. npm install
4. php artisan key:generate --ansi
5. php artisan migrate
    - php artisan migrate --path=database/migrations/landlord --database=mysql
    - php artisan migrate --path=database/migrations/tenant --database=tenant
6. php artisan db:seed
7. npm run dev

Color Palette:

Primary Colors:
#2196F3 - Blue

Button Styles:

-   Primary: #2196F3
-   Secondary: #757575
-   Success: #4CAF50
-   Danger: #F44336
-   Warning: #FFC107
-   Info: #03A9F4

Background Colors:

-   Main: #FFFFFF
-   Light: #F5F5F5
-   Dark: #111111

Fitur APP :
1. Landing Page -> Done
2. Login -> Done
3. Register -> Done
4. Reset Password -> Done
5. Verification Email -> Done
6. Dashboard Admin -> Done
    a. Dashboard
        - Widget jumlah user -> Done
        - Widget jumlah vendor -> Done
    b. User
        - CRUD users -> Done
        - Search User -> Done
    c. Vendor
        - CRUD Vendor -> Done
        - Search Vendor ->Done
7. Dashboard User -> 
    a. Masuk ke hal vedor mau pilih vendor mana -> menuju dashboard dengan data vendor
    b. pengaturan akun
        - Edit profil -> Done
        - Ubah password -> Done
    c. dashboard 
        - swith to vendor toko lain
        - widget total produk
        - widget transaksi hari ini
        - widget transaksi bulanan
        - widget pendapatan bulanan
        - widget grafik produk populer
        - widget grafik pendapatan bulanan
    d. Pengguna
        - User list
        - Manage Role
    e. Pelanggan
        - CRUD pelanggan
        - Search pelanggan
    f. alat dan bahan
        - CRUD alat dan bahan
        - Search alat dan bahan
        - Estimasi produksi
            - CRUD estimasi produksi
            - Search estimasi produksi
    g. produk
        - CRUD produk 
        - Search produk
        - Spesifikasi produk
            - CRUD spesifikasi produk
            - Search spesifikasi produk
    h. Menu Pos
    i. POS
    j. transaksi
        - CRUD transaksi
        - Search transaksi
    k. Menu Laporan
        - Laporan panjulan per hari
        - laporan penjualan per bulan
        - laporan penjualan per tahun