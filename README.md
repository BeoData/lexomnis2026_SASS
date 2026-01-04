# LexOmnis Super Admin Panel

Potpuno odvojena Super Admin aplikacija za upravljanje multi-tenant SaaS platformom.

## 🏗️ Arhitektura

- **Potpuno odvojena aplikacija** - nema shared koda sa Tenant App
- **API komunikacija** - sve operacije preko REST API-ja sa Tenant App
- **Laravel + Inertia.js + Vue 3** - moderni stack
- **Separate database** - Super Admin ima svoju bazu za admin podatke

## 🚀 Instalacija

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=SuperAdminSeeder
```

## ⚙️ Konfiguracija

U `.env` fajlu:

```env
TENANT_APP_URL=http://localhost:8000
TENANT_APP_API_TOKEN=your-api-token-here
```

**Kako da dobiješ API token:**

1. Idi u Tenant App (`C:\var\LexOmnisC`)
2. Pokreni seeder:
```bash
php artisan db:seed --class=ApiTokenSeeder
```
3. Kopiraj token koji se prikaže
4. Dodaj ga u Super Admin App `.env` fajl

## 🔐 Login

**URL:** `http://localhost:8001/login`

**Kredencijali (default):**
- Email: `superadmin@lexomnis.com`
- Password: `superadmin123`

⚠️ **Promenite lozinku u produkciji!**

## 🚀 Pokretanje

```bash
# Terminal 1: Laravel server
php artisan serve --port=8001

# Terminal 2: Vite dev server (za development)
npm run dev

# Ili build assets (za produkciju)
npm run build
```

Zatim idite na: `http://localhost:8001/login`

## 📋 Funkcionalnosti

- ✅ Super Admin autentifikacija (login/logout)
- ✅ Dashboard (osnovni)
- ✅ Tenant Management (CRUD, suspend, activate)
- ✅ User Management (search, suspend, impersonate)
- ✅ System Monitoring (health, metrics, activity logs)
- ⏳ Subscription & Billing Management
- ⏳ Feature Flags Management
- ⏳ Audit & Security (audit logs, login history)

## 📚 API Dokumentacija

Svi API endpoint-i su dokumentovani u Tenant App: `API_DOCUMENTATION.md`

## 🔗 Povezivanje sa Tenant App

Super Admin App komunicira sa Tenant App preko REST API-ja. 
API token se generiše u Tenant App-u i koristi se u Super Admin App-u.

## 🐛 Troubleshooting

Ako vidiš samo `@routes` umesto aplikacije:
1. Proveri da li je `npm run dev` pokrenut
2. Ili pokreni `npm run build` za produkciju

Ako dobiješ grešku o API token-u:
1. Proveri da li je `TENANT_APP_API_TOKEN` postavljen u `.env` fajlu
2. Pokreni `php artisan config:clear` nakon izmene `.env` fajla
3. Proveri da li je token kreiran u Tenant App bazi
