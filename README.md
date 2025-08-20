# sobhansgh/mellat-api

پکیج درگاه **بانک ملت** مخصوص **API-first** (لاراول) — خروجی فقط JSON (بدون Blade / فرم).
نویسنده: **Sobhan Ghasemi** — برند: **HiveWeb**

## نصب
```bash
composer require sobhansgh/mellat-api
php artisan vendor:publish --tag=hiveweb-mellat-api-config
php artisan vendor:publish --tag=hiveweb-mellat-api-migrations
php artisan migrate
```

### .env
```env
MELLAT_API_TERMINAL_ID=xxxx
MELLAT_API_USERNAME=xxxx
MELLAT_API_PASSWORD=xxxx
MELLAT_API_WSDL=https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl
MELLAT_API_CALLBACK_URL=/api/payments/mellat-api/callback
MELLAT_API_CONVERT_TO_RIAL=true
```

## اندپوینت‌ها (پیش‌فرض)
- `POST /api/payments/mellat-api/pay` → `{ amount: 120000, order_id?: "...", additional?: "..." }` → خروجی: `{ ok, ref_id, redirect_url, order_id }`
- بانک به `/api/payments/mellat-api/callback` برمی‌گردد (GET/POST)
- سپس کلاینت شما `POST /api/payments/mellat-api/verify` را با `order_id`, `sale_order_id`, `sale_reference_id` می‌زند.

> مسیرها، پیشوند و میدلور قابل تغییر از طریق فایل کانفیگ `config/hiveweb-mellat-api.php` هستند.

## نکات هم‌زیستی با نسخه‌ی Web
- کلیدهای ENV با پیشوند `MELLAT_API_*` هستند.
- فایل کانفیگ: `config/hiveweb-mellat-api.php` (کلید کانفیگ: `hiveweb-mellat-api`).
- جدول دیتابیس: `mellat_api_logs` (عدم تداخل با هر جدول دیگری مثل `mellat_logs`).

## License
MIT
