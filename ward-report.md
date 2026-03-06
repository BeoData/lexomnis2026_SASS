# Ward Security Report

**Project:** laravel/laravel  
**Laravel:** ^12.0  
**PHP:** ^8.2  
**Duration:** 3.635s  
**Scanners:** env-scanner, config-scanner, dependency-scanner, rules-scanner  

## Summary

| Total | 65 |
|-------|---|
| 🟠 High | 3 |
| 🟡 Medium | 5 |
| 🟢 Low | 57 |

## Findings

### 🟠 High (3)

#### ENV-002 — APP_DEBUG is enabled

- **File:** `.env:4`
- **Category:** Configuration
- **Scanner:** env-scanner

APP_DEBUG is set to true. In production, this exposes detailed error messages including stack traces, database queries, and environment variables to end users.

```
APP_DEBUG=true
```

**Remediation:**

Set APP_DEBUG=false in your production .env file. Use Laravel's logging system for error tracking instead.

**References:**
- https://owasp.org/Top10/A05_2021-Security_Misconfiguration/

---

#### CRYPTO-001 — md5() used for hashing

- **File:** `app\Services\TenantAppApiService.php:68`
- **Category:** Cryptography
- **Scanner:** rules-scanner

md5() is cryptographically broken — collisions can be generated in seconds. It must not be used for password hashing, integrity checks, or any security-sensitive operation.


```
return 'tenant_app:down:' . md5((string) $this->baseUrl);
```

**Remediation:**

For passwords: use Hash::make() (bcrypt/argon2)
  $hash = Hash::make($password);
For integrity: use hash('sha256', $data) or HMAC
  $hash = hash_hmac('sha256', $data, $key);


**References:**
- https://cwe.mitre.org/data/definitions/328.html

---

#### INJECT-003 — Shell command execution function

- **File:** `app\Console\Commands\TenantsDelete.php:90`
- **Category:** Injection
- **Scanner:** rules-scanner

PHP shell execution functions (exec, system, shell_exec, passthru, popen, proc_open) are being used. If any argument includes user input, this leads to OS command injection.


```
$pdo->exec("DROP DATABASE IF EXISTS `{$tenant->db_name}`");
```

**Remediation:**

Avoid shell commands when possible. If necessary:
  - Use escapeshellarg() and escapeshellcmd() for all arguments
  - Use Symfony\Component\Process\Process for safer execution
  - Validate and whitelist expected input values


**References:**
- https://owasp.org/Top10/A03_2021-Injection/
- https://cwe.mitre.org/data/definitions/78.html

---

### 🟡 Medium (5)

#### ENV-005 — APP_ENV is set to 'local'

- **File:** `.env:2`
- **Category:** Configuration
- **Scanner:** env-scanner

The application environment suggests a non-production configuration. If this is a production server, this may cause debug features to be enabled and performance optimizations to be skipped.

```
APP_ENV=local
```

**Remediation:**

Set APP_ENV=production on production servers.

---

#### XSS-001 — Unescaped Blade output {!! !!}

- **File:** `resources\views\admin\tenants\index.blade.php:113`
- **Category:** XSS
- **Scanner:** rules-scanner

The {!! !!} syntax renders raw HTML without escaping. If the value contains user-controlled data, this creates a stored or reflected XSS vulnerability. Use {{ }} which auto-escapes via htmlspecialchars().


```
{!! $link['label'] !!}
```

**Remediation:**

Use escaped output:
  {{ $variable }}        — auto-escapes HTML entities
If raw HTML is truly needed, sanitize first:
  {!! clean($variable) !!}   — use HTMLPurifier or similar


**References:**
- https://owasp.org/Top10/A07_2021-Cross-Site_Scripting/
- https://cwe.mitre.org/data/definitions/79.html

---

#### XSS-001 — Unescaped Blade output {!! !!}

- **File:** `resources\views\admin\tenants\index.blade.php:119`
- **Category:** XSS
- **Scanner:** rules-scanner

The {!! !!} syntax renders raw HTML without escaping. If the value contains user-controlled data, this creates a stored or reflected XSS vulnerability. Use {{ }} which auto-escapes via htmlspecialchars().


```
{!! $link['label'] !!}
```

**Remediation:**

Use escaped output:
  {{ $variable }}        — auto-escapes HTML entities
If raw HTML is truly needed, sanitize first:
  {!! clean($variable) !!}   — use HTMLPurifier or similar


**References:**
- https://owasp.org/Top10/A07_2021-Cross-Site_Scripting/
- https://cwe.mitre.org/data/definitions/79.html

---

#### XSS-001 — Unescaped Blade output {!! !!}

- **File:** `resources\views\admin\users\index.blade.php:101`
- **Category:** XSS
- **Scanner:** rules-scanner

The {!! !!} syntax renders raw HTML without escaping. If the value contains user-controlled data, this creates a stored or reflected XSS vulnerability. Use {{ }} which auto-escapes via htmlspecialchars().


```
{!! $link['label'] !!}
```

**Remediation:**

Use escaped output:
  {{ $variable }}        — auto-escapes HTML entities
If raw HTML is truly needed, sanitize first:
  {!! clean($variable) !!}   — use HTMLPurifier or similar


**References:**
- https://owasp.org/Top10/A07_2021-Cross-Site_Scripting/
- https://cwe.mitre.org/data/definitions/79.html

---

#### XSS-001 — Unescaped Blade output {!! !!}

- **File:** `resources\views\admin\users\index.blade.php:107`
- **Category:** XSS
- **Scanner:** rules-scanner

The {!! !!} syntax renders raw HTML without escaping. If the value contains user-controlled data, this creates a stored or reflected XSS vulnerability. Use {{ }} which auto-escapes via htmlspecialchars().


```
{!! $link['label'] !!}
```

**Remediation:**

Use escaped output:
  {{ $variable }}        — auto-escapes HTML entities
If raw HTML is truly needed, sanitize first:
  {!! clean($variable) !!}   — use HTMLPurifier or similar


**References:**
- https://owasp.org/Top10/A07_2021-Cross-Site_Scripting/
- https://cwe.mitre.org/data/definitions/79.html

---

### 🟢 Low (57)

#### AUTH-001 — Route without middleware

- **File:** `routes\api.php:18`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('queues', [SystemController::class, 'queues']);
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\api.php:19`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('crons', [SystemController::class, 'crons']);
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\api.php:20`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('metrics', [SystemController::class, 'metrics']);
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\api.php:21`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('activity-logs', [SystemController::class, 'activityLogs']);
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:8`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:14`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/pricing', [App\Http\Controllers\PricingController::class, 'index'])->name('pricing');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:20`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/resend-verification', [App\Http\Controllers\TenantRegistrationController::class, 'resendVerification'])->name('tenant.resend-verification');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:27`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/checkout/{plan}', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:28`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:29`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/checkout/success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:30`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/checkout/cancel', [App\Http\Controllers\CheckoutController::class, 'cancel'])->name('checkout.cancel');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:33`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/subscriptions/manage', [App\Http\Controllers\SubscriptionController::class, 'manage'])->name('subscriptions.manage');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:34`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/subscriptions/upgrade', [App\Http\Controllers\SubscriptionController::class, 'upgrade'])->name('subscriptions.upgrade');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:35`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/subscriptions/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:40`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:42`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:46`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\TenantController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:47`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/create', [App\Http\Controllers\Admin\TenantController::class, 'create'])->name('create');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:48`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/', [App\Http\Controllers\Admin\TenantController::class, 'store'])->name('store');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:49`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}', [App\Http\Controllers\Admin\TenantController::class, 'show'])->name('show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:50`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}/edit', [App\Http\Controllers\Admin\TenantController::class, 'edit'])->name('edit');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:51`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::put('/{id}', [App\Http\Controllers\Admin\TenantController::class, 'update'])->name('update');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:52`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::delete('/{id}', [App\Http\Controllers\Admin\TenantController::class, 'destroy'])->name('destroy');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:53`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/{id}/suspend', [App\Http\Controllers\Admin\TenantController::class, 'suspend'])->name('suspend');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:54`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/{id}/activate', [App\Http\Controllers\Admin\TenantController::class, 'activate'])->name('activate');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:55`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/{id}/impersonate', [App\Http\Controllers\Admin\TenantController::class, 'impersonate'])->name('impersonate');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:60`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:61`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:62`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/{id}/suspend', [App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('suspend');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:64`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/{id}/impersonate', [App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('impersonate');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:69`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\SystemController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:71`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/metrics', [App\Http\Controllers\Admin\SystemController::class, 'metrics'])->name('metrics');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:72`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/test-connection', [App\Http\Controllers\Admin\SystemController::class, 'testConnection'])->name('test-connection');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:77`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:78`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::put('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:79`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/test-connection', [App\Http\Controllers\Admin\SettingsController::class, 'testConnection'])->name('test-connection');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:82`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/system/roles-permissions', [App\Http\Controllers\Admin\RolesPermissionsController::class, 'index'])->name('system.roles-permissions');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:87`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\PlanController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:88`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/create', [App\Http\Controllers\Admin\PlanController::class, 'create'])->name('create');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:89`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/', [App\Http\Controllers\Admin\PlanController::class, 'store'])->name('store');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:90`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}', [App\Http\Controllers\Admin\PlanController::class, 'show'])->name('show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:91`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}/edit', [App\Http\Controllers\Admin\PlanController::class, 'edit'])->name('edit');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:92`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::put('/{id}', [App\Http\Controllers\Admin\PlanController::class, 'update'])->name('update');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:97`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:98`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}', [App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:103`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/manual', [App\Http\Controllers\Admin\ManualPaymentController::class, 'index'])->name('manual.index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:104`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/manual/{id}', [App\Http\Controllers\Admin\ManualPaymentController::class, 'show'])->name('manual.show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:105`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/manual/{id}/approve', [App\Http\Controllers\Admin\ManualPaymentController::class, 'approve'])->name('manual.approve');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:106`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/manual/{id}/reject', [App\Http\Controllers\Admin\ManualPaymentController::class, 'reject'])->name('manual.reject');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:111`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\FeatureFlagController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:112`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/create', [App\Http\Controllers\Admin\FeatureFlagController::class, 'create'])->name('create');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:113`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::post('/', [App\Http\Controllers\Admin\FeatureFlagController::class, 'store'])->name('store');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:114`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}', [App\Http\Controllers\Admin\FeatureFlagController::class, 'show'])->name('show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:115`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}/edit', [App\Http\Controllers\Admin\FeatureFlagController::class, 'edit'])->name('edit');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:116`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::put('/{id}', [App\Http\Controllers\Admin\FeatureFlagController::class, 'update'])->name('update');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:121`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('index');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

#### AUTH-001 — Route without middleware

- **File:** `routes\web.php:122`
- **Category:** Authentication
- **Scanner:** rules-scanner

A route is defined without any middleware. Depending on the route, this may expose endpoints without authentication or rate limiting. Sensitive routes should always have auth or throttle middleware.


```
Route::get('/{id}', [App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('show');
```

**Remediation:**

Apply middleware to routes:
  Route::get('/dashboard', [DashboardController::class, 'index'])
      ->middleware(['auth', 'verified']);
Or group routes:
  Route::middleware(['auth'])->group(function () { ... });


**References:**
- https://cwe.mitre.org/data/definitions/306.html

---

*Generated by [Ward](https://github.com/Eljakani/ward) v0.4.0*
