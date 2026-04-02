# ECC Development Standards & Guidelines

All code in this workspace must follow the ECC (Effective Code Craftsmanship) standards documented below. These rules apply to all PHP/Laravel development.

---

## 1. PROJECT STRUCTURE

Follow this conventional Laravel layout with clear layer boundaries:

```
app/
├── Actions/            # Single-purpose use cases (one action per file)
├── Console/
├── Events/             # Domain events
├── Exceptions/         # Custom exceptions
├── Http/
│   ├── Controllers/    # THIN controllers (HTTP handling only)
│   ├── Middleware/
│   ├── Requests/       # FormRequest validation
│   └── Resources/      # API resources
├── Jobs/               # Queued jobs
├── Models/             # Eloquent models
├── Policies/           # Authorization policies
├── Providers/
├── Services/           # Coordinating domain services
└── Support/            # Helper utilities
config/
database/
├── factories/
├── migrations/
└── seeders/
resources/
├── views/
└── lang/
routes/
├── api.php
├── web.php
└── console.php
```

---

## 2. CONTROLLERS - KEEP THEM THIN

Controllers should only handle HTTP concerns. All business logic goes to Services/Actions.

**WRONG - Fat Controller:**
```php
class OrderController extends Controller {
    public function store(Request $request) {
        $validated = $request->validate([...]);
        $customer = Customer::find($validated['customer_id']);
        $order = Order::create($validated);
        $order->items()->attach($validated['items']);
        Mail::send(new OrderNotification($order));
        return response()->json($order);
    }
}
```

**CORRECT - Thin Controller with Action:**
```php
final class OrderController extends Controller {
    public function __construct(private CreateOrderAction $createOrder) {}

    public function store(StoreOrderRequest $request): JsonResponse {
        $order = $this->createOrder->handle($request->toDto());
        return response()->json([
            'success' => true,
            'data' => OrderResource::make($order),
            'error' => null,
            'meta' => null,
        ], 201);
    }
}
```

---

## 3. FORM REQUESTS - CENTRALIZE VALIDATION

All HTTP request validation must be in FormRequest classes. Controllers never validate directly.

```php
final class StoreOrderRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()?->can('create', Order::class) ?? false;
    }

    public function rules(): array {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDto(): CreateOrderData {
        return new CreateOrderData(
            customerId: (int) $this->validated('customer_id'),
            items: $this->validated('items'),
        );
    }
}
```

---

## 4. ACTIONS - SINGLE-PURPOSE USE CASES

Each Action handles ONE use case. Actions are pure, injectable, and easy to test.

```php
final class CreateOrderAction {
    public function __construct(private OrderRepository $orders) {}

    public function handle(CreateOrderData $data): Order {
        return $this->orders->create($data);
    }
}
```

---

## 5. SERVICES - COORDINATE DOMAIN LOGIC

Services orchestrate multiple Actions and Models. Keep them focused.

```php
final class OrderService {
    public function __construct(
        private CreateOrderAction $createOrder,
        private PaymentGateway $payments,
    ) {}

    public function createAndCharge(CreateOrderData $data): Order {
        $order = $this->createOrder->handle($data);
        $this->payments->charge($order);
        return $order;
    }
}
```

---

## 6. DTOs - DATA TRANSFER OBJECTS

Use DTOs for passing data between layers. Never pass raw arrays between Actions/Services.

```php
final readonly class CreateOrderData {
    public function __construct(
        public int $customerId,
        public array $items,
    ) {}
}
```

---

## 7. API RESPONSE FORMAT

All API responses must follow this consistent format:

```php
return response()->json([
    'success' => true,           // Boolean
    'data' => $resource,         // Actual data (null if error)
    'error' => null,             // Error message or null
    'meta' => [                  // Optional pagination/metadata
        'page' => 1,
        'per_page' => 25,
        'total' => 100,
    ],
], 200);
```

---

## 8. ELOQUENT MODELS

### Model Configuration
```php
final class Project extends Model {
    use HasFactory;

    protected $fillable = ['name', 'owner_id', 'status'];

    protected $casts = [
        'status' => ProjectStatus::class,
        'archived_at' => 'datetime',
    ];

    public function owner(): BelongsTo {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scopeActive(Builder $query): Builder {
        return $query->whereNull('archived_at');
    }
}
```

### Eager Loading (Prevent N+1)
```php
$projects = Project::query()
    ->with(['owner', 'tasks.assignee'])  // Eager load
    ->latest()
    ->paginate(25);
```

### Query Scopes for Reusable Filters
```php
final class Project extends Model {
    public function scopeOwnedBy(Builder $query, int $userId): Builder {
        return $query->where('owner_id', $userId);
    }

    public function scopeActive(Builder $query): Builder {
        return $query->whereNull('archived_at');
    }
}

// Usage:
$projects = Project::ownedBy($user->id)->active()->get();
```

### Transactions for Multi-Step Updates
```php
use Illuminate\Support\Facades\DB;

DB::transaction(function (): void {
    $order->update(['status' => 'paid']);
    $order->items()->update(['paid_at' => now()]);
});
```

---

## 9. ROUTING & ROUTE MODEL BINDING

Use route model binding and scoped bindings to prevent cross-tenant access.

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    
    Route::scopeBindings()->group(function () {
        Route::get('/accounts/{account}/projects/{project}', [ProjectController::class, 'show']);
    });
});
```

---

## 10. MIGRATIONS

### Naming & Structure
```php
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('status', 32)->index();
            $table->unsignedInteger('total_cents');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
```

---

## 11. SECURITY - NO SENSITIVE DATA IN LOGS

NEVER log user data, passwords, tokens, or sensitive information.

**WRONG:**
```php
\Log::info('User login', ['user' => $user->toArray()]);  // NEVER!
\Log::info('Auth token', ['token' => $token]);            // NEVER!
```

**CORRECT:**
```php
\Log::info('User login', ['user_id' => $user->id, 'email' => $user->email]);
\Log::info('Auth success', ['user_id' => Auth::id()]);
```

### Environment Variables for Secrets
```php
// .env
DATABASE_PASSWORD=secret_here

// Config
'password' => env('DATABASE_PASSWORD'),

// NEVER hardcode!
```

---

## 12. TESTING - TDD WORKFLOW

### Test Layers
- **Unit**: Pure PHP logic, services
- **Feature**: HTTP endpoints, auth, validation
- **Integration**: Database + queues + external services

### Test Structure
```php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateOrderTest extends TestCase {
    use RefreshDatabase;

    public function test_owner_can_create_order(): void {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'customer_id' => 1,
            'items' => [['sku' => 'ABC123', 'quantity' => 1]],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['customer_id' => 1]);
    }
}
```

### Test Coverage Target
- Aim for 80%+ code coverage
- Unit + Feature tests only
- Coverage = (lines executed / total lines) × 100

---

## 13. CACHING PATTERNS

- Cache read-heavy endpoints
- Invalidate caches on model events
- Use tags for related data

```php
$projects = Cache::tags(['projects', 'user:' . $user->id])
    ->remember('user-projects', 3600, function () use ($user) {
        return $user->projects()->get();
    });
```

---

## 14. EVENTS, JOBS & QUEUES

- Emit domain events for side effects (emails, analytics)
- Use queued jobs for slow work
- Make handlers idempotent with retries

```php
// Dispatch event
event(new OrderCreated($order));

// Handler
class SendOrderNotification implements ShouldQueue {
    public function handle(OrderCreated $event): void {
        Mail::send(new OrderNotification($event->order));
    }
}
```

---

## 15. MULTI-TENANT PATTERNS

### Tenant Connection Management
- Use TenantResolver to set tenant connection BEFORE loading data
- Session stores tenant_slug for middleware to use
- SetTenantConnection middleware activates tenant DB connection
- LoadTenantUser middleware loads user from tenant database

```php
// Always set tenant connection first
$tenantResolver->setTenantConnection($firm);

// Then load data from tenant
$user = User::on('tenant')->find($userId);
```

### Session Management for Impersonation
```php
// Invalidate old session (clears admin session)
session()->invalidate();
session()->regenerateToken();

// Create new session with tenant info
session()->put('tenant_slug', $firm->slug);
session()->put('tenant_id', $firm->id);

// Login as tenant user
auth()->login($tenantUser);
```

---

## 16. CODE STYLE

### Naming Conventions
- Variables/methods: `camelCase`
- Classes: `PascalCase`
- Constants: `UPPER_SNAKE_CASE`
- Files: `PascalCase.php`

### Type Hints
- Always use type hints on function parameters and return types
- Use nullable types `?Type` when applicable
- Use union types `Type1|Type2` for multiple possibilities

```php
final class OrderService {
    public function create(CreateOrderData $data): Order {
        // ...
    }

    public function findById(int $id): ?Order {
        return Order::find($id);
    }
}
```

### Comments & Documentation
- Write self-documenting code (names should be clear)
- Comment WHY, not WHAT
- Use PHPDoc blocks for public APIs

```php
// WRONG
$x = $order->total / 100;  // divide by 100

// CORRECT - code speaks for itself
$totalInDollars = $order->total_cents / 100;

// GOOD - explains WHY
$taxRate = 0.08;  // CA tax rate as of 2024
```

---

## 17. GIT WORKFLOW

- Commit frequently with clear messages
- Feature branches: `feature/impersonation`
- Bug fixes: `bugfix/login-issue`
- Messages: `[FEATURE] Add user impersonation` or `[FIX] Session not persisting`

---

## 18. DEPENDENCIES

Use service container for dependency injection.

```php
// In Service Provider
$this->app->bind(OrderRepository::class, EloquentOrderRepository::class);

// In Controller - automatic injection
final class OrderController {
    public function __construct(private OrderRepository $orders) {}
}
```

---

## CHECKLIST BEFORE COMMITTING

- [ ] Code follows layer boundaries (Controllers → Services → Models)
- [ ] All validation in FormRequest classes
- [ ] DTOs used for data transfer
- [ ] API responses follow standard format
- [ ] No sensitive data in logs
- [ ] Environment variables used for secrets
- [ ] 80%+ test coverage maintained
- [ ] Migrations are reversible (`down()` implemented)
- [ ] Type hints on all public methods
- [ ] Database queries use eager loading (no N+1)
- [ ] No duplicate code (DRY principle)
- [ ] Comments explain WHY, not WHAT
- [ ] Git commit message is descriptive

---

## REFERENCES

- Laravel Patterns: `docs/skills/laravel-patterns/SKILL.md`
- Laravel TDD: `docs/skills/laravel-tdd/SKILL.md`
- Coding Standards: `docs/coding-standards/`
