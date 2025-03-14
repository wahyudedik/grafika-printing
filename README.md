1. copy .env.example to .env
2. composer install
3. npm install
4. php artisan key:generate --ansi
5. php artisan migrate
6. php artisan db:seed
7. npm run dev or php artisan dev

Tech Stack :
1. Laravel 11
2. Bootsrap 5
3. tabler io
4. dompdf

// Get all vendor-type users
$vendorUsers = User::ofType('vendor')->get();

// Get users associated with a specific vendor
$vendorUsers = User::forVendor($vendorId)->get();

// Get all active vendors
$activeVendors = Vendor::all(); // the active scope is applied automatically

// Include inactive vendors
$allVendors = Vendor::withInactive()->get();

// Get vendors for current user
$userVendors = Vendor::forUser(auth()->id())->get();

// Search vendors by name
$searchResults = Vendor::searchByName('Some Vendor')->get();

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
    b. pengaturan akun -> Done
        - Edit profil -> Done
        - Ubah password -> Done
    c. dashboard 
        - swith to vendor toko lain
        - widget total produk -> Done
        - widget transaksi hari ini -> Done
        - widget transaksi bulanan -> Done
        - widget pendapatan bulanan -> Done
        - widget grafik produk populer -> Done
        - widget grafik pendapatan bulanan -> Done
    d. Pengguna
        - User list
        - Manage Role
    e. Pelanggan -> Done
        - CRUD pelanggan -> Done
        - Search pelanggan -> Done
    f. alat dan bahan
        - CRUD alat dan bahan -> Done
        - Search alat dan bahan -> Done
        - Estimasi produksi -> Done
            - CRUD estimasi produksi -> Done
            - Search estimasi produksi -> Done
    g. produk -> Done
        - CRUD produk -> Done
        - Search produk -> Done
        - Spesifikasi produk -> Done
            - CRUD spesifikasi produk -> Done
            - Search spesifikasi produk -> Done
    h. Menu Pos -> Done
    i. POS -> Done
    j. transaksi -> Done
        - CRUD transaksi -> Done
        - Search transaksi -> Done
    k. Menu Laporan -> Done
        - Laporan penjualan per hari -> Done
        - laporan penjualan per bulan -> Done
        - laporan penjualan per tahun -> Done
    l. Notifikasi bahan habis
    m. notifikasi email proses cetak produk yang di pesan