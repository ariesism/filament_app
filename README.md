# Filament App

專案說明
---
這是一個基於 Laravel（專案使用 PHP ^8.2 與 Laravel 12）與 Filament 建置的管理面板範例應用程式，整合了 Filament、Livewire、Spatie 權限（`spatie/laravel-permission`）、Filament Shield，以及常見的資源模型（`Post`, `Product`, `Category`, `Tag`, `User`）。此專案示範如何快速建立管理介面、權限控制與常見的開發流程。

主要技術與相依套件
---
- PHP ^8.2
- Laravel 12
- Filament 5
- Livewire
- spatie/laravel-permission
- bezhansalleh/filament-shield
- Node.js + npm（Vite）
- Pest（測試）

快速安裝指南
---
以下是在本機開發環境的快速安裝步驟：

1. 取得原始碼
```bash
git clone <repository-url>
cd filament-app
```

2. 安裝 PHP 相依套件
```bash
composer install
```

3. 複製環境範本並產生應用金鑰
```bash
cp .env.example .env
php artisan key:generate
```

4. 設定資料庫
- 編輯 `.env` 設定 `DB_CONNECTION`、`DB_HOST`、`DB_DATABASE`、`DB_USERNAME`、`DB_PASSWORD`。
- 開發時可使用 `database/database.sqlite`：
```bash
touch database/database.sqlite
# .env 中設定 DB_CONNECTION=sqlite 與 DB_DATABASE=database/database.sqlite
```

5. 執行遷移與種子（建立資料表與測試資料）
```bash
php artisan migrate --seed
```

6. 安裝前端套件並啟動開發伺服器
```bash
npm install
npm run dev   # 開發模式 (Vite watch)
npm run build # 建置資源
```

7. 使用專案提供的 dev 指令（同時啟動 server、隊列與前端）
```bash
composer run-script dev
```

專案結構（重點）
---
- `app/Models/`：Eloquent 模型（User、Post、Product、Category、Tag）
- `app/Filament/Resources/`：Filament 資源定義（管理介面表單與表格）
- `app/Observers/`：模型觀察者
- `app/Policies/`：權限政策（Policies）
- `database/migrations/`：資料表遷移
- `database/seeders/`：資料種子
- `tests/`：Pest / PHPUnit 測試

Filament 管理介面
---
預設 Filament 管理介面路徑通常為 `/admin`（可在 `config/filament.php` 中確認 `path` 設定）。要登入管理介面請建立管理帳號，範例使用 tinker 建立：

```bash
php artisan tinker
>>> use App\\Models\\User;
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
```

記得為該帳號指派 `admin` 角色或授予必要權限（可透過 `spatie/laravel-permission` 的方法或專案中的 seeder 完成）。

環境變數（重要）
---
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_...`（如需 email 功能）
- `FILESYSTEM_DRIVER`（若使用外部儲存）

測試
---
專案使用 Pest，可透過下面指令執行整套測試：

```bash
./vendor/bin/pest
# 或
php artisan test
# 或
composer test
```

程式碼格式化與檢查
---
專案已包含 `laravel/pint`，在本機可執行：

```bash
./vendor/bin/pint
```

部署建議
---
- 在部署前執行 `composer install --no-dev --optimize-autoloader` 與 `npm ci && npm run build`。
- 執行資料庫遷移：
```bash
php artisan migrate --force
```
- 快取並優化：
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```