# Super Admin App - Development Guide

## Lokacija
**Super Admin App:** `C:\var\lexomnis2026_SASS`
**Tenant App:** `C:\var\LexOmnisC`

## Struktura Projekta

### Direktorijumi
- `app/Http/Controllers/Admin/` - Admin kontroleri
- `resources/views/admin/` - Blade template-i (za buduće)
- `resources/js/Pages/` - Vue komponente (Inertia) - ZAMENITI SA BLADE
- `routes/web.php` - Web rute
- `app/Services/TenantAppApiService.php` - API servis za komunikaciju sa Tenant App

### Trenutna Arhitektura

**PROBLEM:** Aplikacija koristi Inertia.js + Vue, što je SPORO i kompleksno za admin panel.

**REŠENJE:** Zameniti sa čistim Blade template-ima za maksimalnu brzinu i jednostavnost.

## Konverzija na Blade

### Plan

1. **Kreirati Blade layout:**
   - `resources/views/admin/layout.blade.php` - Osnovni layout sa navigacijom
   - Koristi Tailwind CSS (već postoji)
   - Jednostavna navigacija bez Vue

2. **Konvertovati kontrolere:**
   - `TenantController@show` - Zameniti `Inertia::render()` sa `view()`
   - Ukloniti Vue komponente
   - Koristiti direktno Blade view

3. **Kreirati Blade view-ove:**
   - `resources/views/admin/tenants/show.blade.php`
   - `resources/views/admin/tenants/index.blade.php`
   - `resources/views/admin/tenants/create.blade.php`
   - `resources/views/admin/tenants/edit.blade.php`

4. **API pozivi:**
   - Zadržati `TenantAppApiService` - API pozivi su OK
   - Problem je samo u Vue renderovanju

## Performance Issues

### Problem: `/tenants/1` je SPORO
**Uzrok:**
- Inertia.js + Vue overhead
- Kompilacija Vue komponenti
- Veliki JavaScript bundle

**Rešenje:**
- Čist Blade = instant render
- Bez JavaScript overhead-a
- Direktan HTML output

## Kredencijali

### Super Admin Login
- **URL:** `http://127.0.0.1:8001/login`
- **Email:** `superadmin@lexomnis.com`
- **Password:** `superadmin123`

## Komande

### Development
```bash
cd C:\var\lexomnis2026_SASS
php artisan serve --port=8001
```

### Build (samo ako je potrebno Vue)
```bash
npm run build
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## API Konfiguracija

### Settings
- API Token se postavlja u Settings UI: `http://127.0.0.1:8001/settings`
- Ili u `.env`: `TENANT_APP_API_TOKEN=...`
- Tenant App URL: `http://localhost:8000`

## Principi Razvoja

### Za Admin Panel:
1. **Brzina > Funkcionalnost** - Blade je prioritet
2. **Jednostavnost** - Nema modala, Vue, kompleksnosti
3. **Direktnost** - Direktni linkovi, standardni formi
4. **Pouzdanost** - Čist HTML/PHP, manje tačaka kvara

### Za Tenant App:
- Može ostati Inertia + Vue (glavna aplikacija)
- Ali Super Admin mora biti brz i jednostavan

## Dodatne Informacije

- Database: SQLite (`database/database.sqlite`)
- Session Driver: `database`
- Authentication: Laravel standard
- Frontend: Tailwind CSS (CSS samo, bez JS framework-a za Blade)

