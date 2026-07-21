# Shop Project — Agent Guide

## Architecture

Two-server PHP app with no framework:

- **`backend/`** — JSON API (port 8000). Entry: `public/index.php`.
- **`frontend/`** — Server-rendered PHP views (port 8001). Entry: `frontend/index.php`.

Frontend calls backend via `api_get()`/`api_post()` helpers (`frontend/helpers.php`). `API_BASE` is hardcoded to `http://localhost:8000`.

## Run locally

```Shell
# Terminal 1 — Backend API
php -S localhost:8000 -t backend/public

# Terminal 2 — Frontend
php -S localhost:8001 -t frontend
```

## Key directories

| Path                       | Purpose                                           |
| -------------------------- | ------------------------------------------------- |
| `backend/src/Controllers/` | Request handlers, thin, return `Response::json()` |
| `backend/src/Models/`      | DB queries (PDO)                                  |
| `backend/src/Core/`        | Router, Database, Auth, Response, JWT             |
| `backend/src/Services/`    | Business logic (e.g., `BakongService`)            |
| `backend/src/Database/`    | Migration SQL scripts                             |
| `backend/public/index.php` | All API routes registered here                    |
| `frontend/views/`          | PHP templates, rendered via `render()`            |
| `frontend/index.php`       | All frontend routes + form handlers               |

## Dependencies (backend)

- `vlucas/phpdotenv` — loads `.env` (gitignored)
- `firebase/php-jwt` — auth tokens
- `khqr-gateway/bakong-khqr-php` — Bakong KHQR generation
- `chillerlan/php-qrcode` — QR image rendering (local, no external API)

## Bakong Payment Gotchas

- **`$response->data`** **is an array**, not object: use `$response->data['qr']`, NOT `$response->data->qr`
- **Expiration timestamp is required** in Tag 99 sub-tag `01`. The library only creates creation timestamp (sub-tag `00`). The `BakongService::addExpirationTimestamp()` method post-processes the QR to inject it + recalculate CRC via `KHQR\Helpers\Utils::crc16()`.
- QR image is generated locally as a base64 PNG data URI — no external service dependency.
- `Order::create()` 5th param `$paymentMethod` defaults to `'cod'`. Must pass it from `OrderController::store()`.
- `BAKONG_ACCOUNT_TYPE` in `.env` controls merchant (`30`) vs individual (`29`) tag.

## Common mistakes

- `Order::create()` — `$paymentMethod` was undefined (always `'cod'` before fix)
- Bakong response data accessed as object (`->qr`) instead of array (`['qr']`)
- QR missing `expirationTimestamp` causes "expired" error on scan

## Style

- No framework, no ORM — raw PDO in models.
- Controllers return JSON via `App\Core\Response::json()`.
- Frontend uses session flash (`$_SESSION['_flash']`, `$_SESSION['_errors']`).
- Migrations are raw SQL files in `backend/src/Database/`.
