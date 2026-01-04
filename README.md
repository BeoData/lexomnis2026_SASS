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
```

## ⚙️ Konfiguracija

U `.env` fajlu:

```env
TENANT_APP_URL=http://localhost:8000
TENANT_APP_API_TOKEN=your-api-token-here
```

## 📋 Funkcionalnosti

- Tenant Management (CRUD, suspend, activate)
- Global User Management (search, suspend, impersonate)
- Subscription & Billing Management
- Feature Flags Management
- System Monitoring (health, metrics, activity logs)
- Audit & Security (audit logs, login history)

## 🔐 Autentifikacija

Super Admin koristi odvojeni auth guard i middleware.

## 📚 API Dokumentacija

Svi API endpoint-i su dokumentovani u Tenant App: `API_DOCUMENTATION.md`
