# Troubleshooting Super Admin App

## Problem: Vidi se samo "@routes" umesto aplikacije

### Rešenje 1: Pokreni Vite Dev Server

**Problem:** Vite dev server nije pokrenut, pa se assets ne učitavaju.

**Rešenje:**
```bash
cd C:\var\lexomnis2026_SASS
npm run dev
```

Ovo će pokrenuti Vite dev server na portu 5173 (ili sličnom).

### Rešenje 2: Build Assets za Produkciju

Ako ne želiš da koristiš dev server, build-uj assets:

```bash
npm run build
```

### Rešenje 3: Proveri da li su svi paketi instalirani

```bash
npm install
composer install
```

### Rešenje 4: Proveri Browser Console

Otvorite Developer Tools (F12) i proverite Console za greške.

### Rešenje 5: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Problem: Route helper ne radi

### Rešenje: Proveri Ziggy konfiguraciju

```bash
php artisan ziggy:generate
```

Ili proveri da li je `@routes` direktiva u `app.blade.php` pre `@vite`.

## Problem: CSRF Token Error

### Rešenje: Proveri da li postoji CSRF token meta tag

U `app.blade.php` treba da postoji:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## Problem: Inertia ne renderuje komponente

### Rešenje: Proveri HandleInertiaRequests middleware

Proveri da li je middleware registrovan u `bootstrap/app.php`:

```php
$middleware->web(append: [
    \App\Http\Middleware\HandleInertiaRequests::class,
]);
```

