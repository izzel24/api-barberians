### 1. Clone Repository
```bash
git clone https://github.com/username/nama-repo.git
cd nama-repo
```
### 2. Install Coomposser
```bash
composer install
```

### 3. Ganti path database
buat file database.sqlite di folder database copy pathnya
edit file .env: 
```bash
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/ke/project/database/database.sqlite
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. JWT Config
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

### 6. Migrate & serve
```bash
php artisan migrate
php artisan serve
```