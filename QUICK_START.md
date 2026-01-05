# Super Admin App - Quick Start

## Projekt Lokacija
```
C:\var\lexomnis2026_SASS
```

## Osnovne Informacije

### Trenutni Problem
- `/tenants/1` je SPORO (par sekundi kašnjenja)
- Uzrok: Inertia.js + Vue overhead
- Rešenje: Preći na čist Blade

### Struktura

**Admin Kontroleri:**
- `app/Http/Controllers/Admin/TenantController.php`
- `app/Http/Controllers/Admin/UserController.php`
- `app/Http/Controllers/Admin/SettingsController.php`
- itd.

**Rute:**
- `routes/web.php`

**API Servis:**
- `app/Services/TenantAppApiService.php` - Komunikacija sa Tenant App

### Login
```
URL: http://127.0.0.1:8001/login
Email: superadmin@lexomnis.com
Password: superadmin123
```

### Važne Komande
```bash
# Server
php artisan serve --port=8001

# Clear cache
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

## Pravila Razvoja

1. **Super Admin = Blade samo** (bez Vue/Inertia)
2. **Brzina > Kompleksnost**
3. **Direktni linkovi** (nema modala)
4. **Čist HTML/PHP**

## Konverzija Plan

**START: TenantController@show**
1. Kreirati `resources/views/admin/layout.blade.php`
2. Kreirati `resources/views/admin/tenants/show.blade.php`
3. Promeniti `TenantController@show` da koristi `view()` umesto `Inertia::render()`

**NEXT: Ostale stranice**
- Index, Create, Edit - sve u Blade

