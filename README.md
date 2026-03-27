# MaplePHP - Your code. Your libraries. Your framework.

MaplePHP is a high-performance PHP 8.2+ framework built on PSR standards and modern best practices. It provides the core infrastructure needed for real applications — MVC architecture, dependency injection, routing, middleware, caching, logging, error handling, and full support for both web and CLI environments — while keeping every component swappable.

The goal is not to lock you into a fixed ecosystem. Each `maplephp/*` library is independently installable and PSR-compliant. You shape the framework around your own stack and workflow, while still benefiting from updates to the core.

**Requires PHP 8.2+**

> This framework is currently in beta. Use `--stability=beta` when installing.

---

## Table of Contents

- [Quick Start](#quick-start)
- [Project Structure](#project-structure)
- [Core Architecture](#core-architecture)
- [Routing](#routing)
- [Controllers](#controllers)
- [Services](#services)
- [Service Providers](#service-providers)
- [Middleware](#middleware)
- [Database](#database)
- [Validation](#validation)
- [Error Handling](#error-handling)
- [Caching](#caching)
- [Logging](#logging)
- [CLI Commands](#cli-commands)
- [Testing](#testing)
- [Library Ecosystem](#library-ecosystem)
- [Configuration Reference](#configuration-reference)
- [CLI Command Reference](#cli-command-reference)

---

## Quick Start

### Installation

```bash
composer create-project maplephp/maplephp my-app --stability=beta
cd my-app
./maple serve
```

Visit `http://localhost:8000` to see the default welcome page.

### Your First Route

Add a route in `routers/web.php`:

```php
// routers/web.php
$router->get("/hello/{name}", [App\Controllers\HelloController::class, "greet"]);
```

Create the controller:

```php
// app/Controllers/HelloController.php
namespace App\Controllers;

use MaplePHP\Core\Routing\DefaultController;
use MaplePHP\Http\Interfaces\PathInterface;
use Psr\Http\Message\ResponseInterface;

class HelloController extends DefaultController
{
    public function greet(ResponseInterface $response, PathInterface $path): ResponseInterface
    {
        $name = $path->select("name")->last();
        $response->getBody()->write("Hello, {$name}!");
        return $response;
    }
}
```

Visit `http://localhost:8000/hello/World`.

---

## Project Structure

```
my-app/
├── app/
│   ├── Controllers/     # HTTP controllers — extend DefaultController
│   ├── Services/        # Business logic — plain classes, autowired by DI
│   ├── Commands/        # CLI commands — extend DefaultCommand
│   └── Providers/       # Custom service providers — extend ServiceProvider
├── configs/
│   ├── configs.php      # App settings: title, environment, timezone, locale
│   ├── database.php     # Database connections (MySQL, SQLite, in-memory test)
│   ├── http.php         # Middleware pipeline configuration
│   └── providers.php    # Service providers to register and boot
├── database/
│   └── migrations/      # Schema migration classes (timestamped filenames)
├── public/
│   └── index.php        # Web entry point — boots HttpKernel
├── routers/
│   ├── web.php          # HTTP routes (FastRoute-based)
│   └── console.php      # CLI command routes
├── storage/             # Cache files, logs, and temp data (must be writable)
├── tests/               # Test files — run with vendor/bin/unitary
└── maple                # CLI entry point — boots CliKernel
```

---

## App singleton

After boot, `MaplePHP\Core\App` is available as a singleton. Use it to resolve well-known directory paths instead of hardcoding strings:

### Directory Paths

```php
use MaplePHP\Core\App;

$app = App::get();

$app->dir()->root();            // /path/to/my-app
$app->dir()->public();          // /path/to/my-app/public
$app->dir()->configs();         // /path/to/my-app/configs
$app->dir()->app();             // /path/to/my-app/app
$app->dir()->logs();            // /path/to/my-app/logs
$app->dir()->cache();           // /path/to/my-app/storage/cache
$app->dir()->migrations();      // /path/to/my-app/database/migrations
```

### Environment

`App` also exposes the current environment:

```php
$app->env();       // e.g. 'PROD', 'DEV', 'STAGE', 'TEST'
$app->isProd();    // bool
$app->isDev();     // bool
```

---

## Routing

### HTTP Routes

Define HTTP routes in `routers/web.php`. The `$router` variable is injected automatically.

```php
// routers/web.php
use App\Controllers\UserController;

$router->get("/users", [UserController::class, "index"]);
$router->get("/users/{id:\d+}", [UserController::class, "show"]);
$router->post("/users", [UserController::class, "store"]);
$router->put("/users/{id:\d+}", [UserController::class, "update"]);
$router->delete("/users/{id:\d+}", [UserController::class, "destroy"]);
```

### FastRoute Pattern Reference

MaplePHP uses [FastRoute](https://github.com/nikic/FastRoute) for URL matching.

| Pattern | Example Route | Matches |
|---|---|---|
| `/slug` | `/about` | Static segment only |
| `{name}` | `/users/{name}` | Any segment except `/` |
| `{id:\d+}` | `/posts/{id:\d+}` | Digits only |
| `{name:[^/]+}` | `/profile/{name:[^/]+}` | Explicit single segment |
| `{slug:.+}` | `/docs/{slug:.+}` | Everything including `/` |
| `{lang:(en\|sv\|de)}` | `/{lang}/about` | Enumerated values only |
| `{name:your-slug}` | `/your-slug` | Bind a static slug to a named parameter |

### CLI Routes

Define CLI commands in `routers/console.php`:

```php
// routers/console.php
use App\Commands\ImportCommand;

$router->cli("import", [ImportCommand::class, "index"]);
```

Run with: `./maple import --file=data.csv`

### Route Groups

Use `$router->group()` to apply a shared middleware stack — and optionally a URL prefix — to a set of routes.

**Group with middleware only** (no prefix):

```php
// routers/web.php
use MaplePHP\Emitron\Middlewares\GzipMiddleware;

$router->group(function (RouterDispatcher $router) {
    $router->get("/dashboard", [DashboardController::class, "index"]);
    $router->get("/dashboard/stats", [DashboardController::class, "stats"]);
}, [
    GzipMiddleware::class,
]);
```

**Group with a URL prefix and middleware**:

```php
// routers/web.php
$router->group("/api", function (RouterDispatcher $router) {
    $router->get("/{page:show}", [HelloWorldController::class, "show"]);
}, [
    GzipMiddleware::class,
]);
// Matches: GET /api/show
```

Middleware listed in the group array runs only for routes defined inside that group, in addition to any global middleware from `configs/http.php`.

### Reading Route Parameters

Use `PathInterface` to access named route parameters:

```php
// Reads the {id} segment from the matched route
$id = $path->select("id")->last();

// Build a URI relative to the current request
$url = $path->uri()->withPath("/users");
```

---

## Controllers

HTTP controllers extend `MaplePHP\Core\Routing\DefaultController`. Action methods declare their dependencies as type-hinted parameters — the framework resolves them automatically from the DI container.

```php
// app/Controllers/UserController.php
namespace App\Controllers;

use App\Services\UserService;
use MaplePHP\Core\Routing\DefaultController;
use MaplePHP\Http\Interfaces\PathInterface;
use Psr\Http\Message\ResponseInterface;

class UserController extends DefaultController
{
    public function show(
        ResponseInterface $response,
        PathInterface     $path,
        UserService       $users
    ): ResponseInterface {
        $id = (int) $path->select("id")->last();
        $user = $users->find($id);

        $response->getBody()->write(json_encode($user));
        return $response->withHeader("Content-Type", "application/json");
    }
}
```

**Key points**:

- Write output to `$response->getBody()->write(...)` — the body stream is mutable in place
- Use `$response->withHeader(...)` to set headers — this is immutable and returns a new instance; always capture the return value
- The action method must return a `ResponseInterface`
- Any class registered in the container (or resolvable via autowiring) can be injected as a parameter

**Available via `$this` in every controller** (inherited from `DefaultController`):

| Property | Type | Description |
|---|---|---|
| `$this->container` | `ContainerInterface` | The PSR-11 service container |
| `$this->request` | `ServerRequestInterface` | The current PSR-7 request |
| `$this->config` | `array` | Merged configuration from `configs/` |

---

## Services

Services are plain PHP classes. No base class is required. Type-hint any service in a controller method and the container resolves it automatically via constructor autowiring.

```php
// app/Services/UserService.php
namespace App\Services;

use Psr\Container\ContainerInterface;

class UserService
{
    public function __construct(private readonly ContainerInterface $container) {}

    public function find(int $id): array
    {
        // Retrieve user from database, cache, etc.
        return [];
    }
}
```

Services can themselves declare constructor dependencies — the container resolves the full tree. If a class has non-resolvable constructor parameters (scalars, configuration values), register it explicitly in a [service provider](#service-providers).

---

## Service Providers

Service providers are the primary extension point for wiring third-party libraries and custom services into the container. They follow a two-phase boot: first all `register()` calls run, then all `boot()` calls run. This guarantees that every service is available before any service tries to use another.

### Creating a Provider

```php
// app/Providers/MailServiceProvider.php
namespace App\Providers;

use App\Services\Mailer;
use MaplePHP\Core\Support\ServiceProvider;
use Psr\Container\ContainerInterface;

class MailServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        $container->set('mailer', new Mailer(
            host: env('MAIL_HOST'),
            port: (int) env('MAIL_PORT', 587),
        ));
    }

    public function boot(): void
    {
        // Runs after all providers have registered.
        // Safe to resolve other services here.
    }
}
```

### Registering a Provider

Add the fully-qualified class name to `configs/providers.php`:

```php
// configs/providers.php
return [
    \MaplePHP\Core\Providers\DatabaseProvider::class,
    \App\Providers\MailServiceProvider::class,
];
```

### Built-in Providers

| Provider | What it registers |
|---|---|
| `MaplePHP\Core\Providers\DatabaseProvider` | Doctrine DBAL connection, `DB` singleton, `QueryBuilder` |

`DatabaseProvider` must be present for `DB::table()` to work. Remove it if you are not using the database layer.

---

## Middleware

MaplePHP uses a PSR-15 middleware pipeline powered by `maplephp/emitron`. Middleware is configured in `configs/http.php` and runs for every request.

### Configuration

```php
// configs/http.php
use MaplePHP\Core\Middlewares\HttpStatusError;
use MaplePHP\Emitron\Middlewares\ContentLengthMiddleware;
use MaplePHP\Emitron\Middlewares\GzipMiddleware;

return [
    "middleware" => [
        "global" => [
            HttpStatusError::class,
            ContentLengthMiddleware::class,
            // GzipMiddleware::class,
        ]
    ]
];
```

### Built-in Middleware

| Class | Purpose |
|---|---|
| `HttpStatusError` | Catches HTTP exceptions and renders error responses |
| `ContentLengthMiddleware` | Sets the `Content-Length` header automatically |
| `GzipMiddleware` | Compresses the response body with gzip |
| `HeadRequestMiddleware` | Strips the body from HEAD responses (RFC 7231) |
| `CacheControlMiddleware` | Sets cache control headers |
| `EmitterMiddleware` | Final middleware — sends headers and body to the client |

### Writing Custom Middleware

```php
// app/Http/Middleware/AuthMiddleware.php
namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Inspect the request; return early or continue the pipeline
        return $handler->handle($request);
    }
}
```

Register it in `configs/http.php` alongside the built-in middleware.

---

## Database

The database layer is built on [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html) and exposed through a fluent query builder. Enable it by adding `DatabaseProvider` to `configs/providers.php`.

### Configuration

Set credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_app
DB_USERNAME=root
DB_PASSWORD=
```

The `configs/database.php` file reads these values:

```php
// configs/database.php
return [
    'default' => env('DB_CONNECTION'),
    'connections' => [
        'mysql' => [
            'driver'   => 'pdo_mysql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', 3306),
            'dbname'   => env('DB_DATABASE', ''),
            'user'     => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8mb4',
        ],
        'sqlite' => [
            'driver' => 'pdo_sqlite',
            'file'   => env('DB_DATABASE', 'database.sqlite'),
        ],
        'test' => [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ],
    ],
];
```

### Query Builder

`DB::table()` returns a `QueryBuilder` instance. Chain methods to build and execute queries.

```php
use MaplePHP\Core\Support\Database\DB;

// Fetch all rows
$users = DB::table('users')->where('status', '=', 1)->get();

// Fetch a single row
$user = DB::table('users')->where('id', '=', $id)->first();

// Fetch a single column value from the first row
$email = DB::table('users')->where('id', '=', $id)->value('email');

// Fetch a flat array of one column across all rows
$ids = DB::table('users')->pluck('id');

// Check existence
$exists = DB::table('users')->where('email', '=', $email)->exists();

// Count rows
$total = DB::table('users')->count();

// Paginate — returns data + pagination meta
$result = DB::table('users')
    ->orderByDesc('created_at')
    ->paginate(page: 1, perPage: 20);
// $result['data'], $result['total'], $result['last_page'], ...

// Conditional clause (useful for optional filters)
$users = DB::table('users')
    ->when($search !== null, fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
    ->orderByAsc('name')
    ->get();

// Insert
DB::insert('users', ['email' => 'user@example.com', 'status' => 1]);

// Update
DB::update('users', ['status' => 0], ['id' => $id]);

// Delete
DB::delete('users', ['id' => $id]);

// Transaction
DB::transaction(function ($conn) use ($data) {
    DB::insert('orders', $data['order']);
    DB::insert('order_items', $data['items'][0]);
});

// Raw query
$rows = DB::select('SELECT * FROM users WHERE status = ?', [1]);
```

### Migrations

Generate a migration scaffold:

```bash
./maple make --type=migration --name=CreateUsers
```

Edit the generated file in `database/migrations/`:

```php
// database/migrations/2026-01-01-000000_CreateUsersMigration.php
namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use MaplePHP\Core\Support\Database\Migrations;

class CreateUsersMigration extends Migrations
{
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('users');
        $table->addColumn('id',     'integer', ['autoincrement' => true]);
        $table->addColumn('email',  'string',  ['length' => 255]);
        $table->addColumn('status', 'integer', ['default' => 0]);

        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setUnquotedColumnNames('id')
                ->create()
        );

        $table->addIndex(['status']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
```

Run migrations: `./maple migrate`

See the [CLI Command Reference](#cli-command-reference) for all migration commands.

---

## Validation

MaplePHP ships with `maplephp/validate`, which provides two approaches: `Validator` for single-value checks and `ValidationChain` for collecting multiple validation errors (useful for forms).

### Single Value — `Validator`

```php
use MaplePHP\Validate\Validator;

// Option 1: Create an instance
$v = new Validator($email);
if ($v->isEmail() && $v->length(5, 255)) {
    // value is valid
}

// Option 2: Use the static method for cleaner syntax
if (Validator::value($email)->isEmail(1, 200)) {
    // value is valid
}
```

### Available Validators

Visit the [maplephp/validate](https://github.com/maplephp/validate) repository for a complete list of validators.

---

## Caching

`maplephp/cache` implements both PSR-6 (`CacheItemPoolInterface`) and PSR-16 (`CacheInterface`). The `Cache` wrapper provides the simple PSR-16 API around any PSR-6 handler.

```php
use MaplePHP\Cache\Cache;
use MaplePHP\Cache\Handlers\FileSystemHandler;
use MaplePHP\Core\App;

$cache = new Cache(new FileSystemHandler(App::get()->dir()->cache()));

// Read (returns $default on miss)
$value = $cache->get('my-key', null);

// Write with TTL in seconds
$cache->set('my-key', $computedResult, 3600);

// Check existence
if ($cache->has('my-key')) { ... }

// Delete
$cache->delete('my-key');

// Clear all
$cache->clear();
```

Swap the handler to use Memcached without changing any other code:

```php
use MaplePHP\Cache\Handlers\MemcachedHandler;

$memcached = new \Memcached();
$memcached->addServer('127.0.0.1', 11211);

$cache = new Cache(new MemcachedHandler($memcached));
```

---

## Logging

`maplephp/log` implements PSR-3. Attach one or more handlers to a `Logger` instance.

```php
use MaplePHP\Core\App;
use MaplePHP\Log\Logger;
use MaplePHP\Log\Handlers\StreamHandler;

// Log to a rotating file: rotate at 5 MB, keep 10 files
$logger = new Logger(
    new StreamHandler(
        file:  App::get()->dir()->logs() . '/app.log',
        size:  5000,   // KB — rotate when the file exceeds this size
        count: 10      // keep at most this many rotated files
    )
);

$logger->info('User registered', ['user_id' => $id]);
$logger->warning('Slow query detected', ['duration_ms' => 1200]);
$logger->error('Payment gateway timeout', ['order_id' => $orderId]);
```

### Handlers

| Handler | Description |
|---|---|
| `StreamHandler` | Writes to a file with optional size-based rotation |
| `ErrorLogHandler` | Delegates to PHP's `error_log()` |
| `DBHandler` | Writes structured entries to a database table |

Log levels follow PSR-3: `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`.

---

## CLI Commands

CLI commands extend `MaplePHP\Core\Routing\DefaultCommand`. Generate a scaffold with:

```bash
./maple make --type=command --name=Import
```

### Anatomy of a Command

```php
// app/Commands/ImportCommand.php
namespace App\Commands;

use MaplePHP\Core\Routing\DefaultCommand;
use MaplePHP\Core\Console\ArgDefinition;

class ImportCommand extends DefaultCommand
{
    public static function name(): string
    {
        return 'import';
    }

    public static function description(): string
    {
        return 'Import records from a CSV file';
    }

    protected function args(): array
    {
        return [
            new ArgDefinition('file', 'Path to the CSV file', required: true),
            new ArgDefinition('limit', 'Maximum rows to import', required: false),
        ];
    }

    public function index(): void
    {
        $file  = $this->args['file']  ?? '';
        $limit = $this->args['limit'] ?? null;

        $this->command->message("Importing from: {$file}");

        // ... import logic ...

        $this->command->message("Done.");
    }
}
```

Register the command in `routers/console.php`:

```php
// routers/console.php
$router->cli("import", [App\Commands\ImportCommand::class, "index"]);
```

Run it:

```bash
./maple import --file=data.csv --limit=500
```

### Interactive Prompts

For interactive CLI input, use `maplephp/prompts` directly. It provides `text`, `password`, `toggle`, `select`, `list`, `confirm`, and progress bar prompt types. See the [maplephp/prompts](https://github.com/maplephp/prompts) repository for usage.

---

## Testing

MaplePHP includes `maplephp/unitary`, a standalone testing framework built for speed. It runs 100,000+ tests per second with no external dependencies, and includes built-in mocking and assertion support.

For more information, visit the [Unitary](https://maplephp.github.io/unitary/) documentation.


### Writing Tests

```php
// tests/UserServiceTest.php
use MaplePHP\Unitary\TestCase;

group("Your grouped test subject", function (TestCase $case) {

    $case->expect(1 + 2)
        ->isEqualTo(2)
        ->validate("Addition must be correct"); // Will fail

});
```

### Running Tests

```bash
vendor/bin/unitary
```

Or add a Composer script:

```json
{
    "scripts": {
        "test": "vendor/bin/unitary"
    }
}
```

```bash
composer test
```

Unitary auto-discovers test files in the `tests/` directory. No configuration file is needed.

---

## Library Ecosystem

Each `maplephp/*` package is a standalone, independently installable Composer library. The framework skeleton wires them together, but nothing prevents using any one of them in a different project.

| Package | PSR | Description |
|---|---|---|
| [maplephp/core](https://github.com/maplephp/core) | PSR-11, PSR-15 | HttpKernel, CliKernel, App singleton, router dispatcher, DB query builder, migration runner |
| [maplephp/http](https://github.com/maplephp/http) | PSR-7 | ServerRequest, Response, Stream, Uri, UploadedFile, HTTP client (cURL), safe Input helper |
| [maplephp/container](https://github.com/maplephp/container) | PSR-11 | DI container with reflection-based autowiring and factory support |
| [maplephp/emitron](https://github.com/maplephp/emitron) | PSR-15 | Middleware dispatcher; built-in: OutputBuffer, Gzip, ContentLength, HeadRequest, CacheControl, Emitter |
| [maplephp/dto](https://github.com/maplephp/dto) | — | Safe data traversal, dot-notation access, type coercion, string / number / date / HTML formatting |
| [maplephp/validate](https://github.com/maplephp/validate) | — | 50+ validators: email, phone, URL, credit card, dates, passwords, identity numbers. Fluent chaining. |
| [maplephp/log](https://github.com/maplephp/log) | PSR-3 | Logger with StreamHandler (auto-rotation), ErrorLogHandler, DBHandler |
| [maplephp/cache](https://github.com/maplephp/cache) | PSR-6, PSR-16 | FileSystem and Memcached handlers behind a unified SimpleCache interface |
| [maplephp/blunder](https://github.com/maplephp/blunder) | PSR-7 | Error/exception handling: HTML, JSON, XML, CLI, PlainText, Silent output handlers |
| [maplephp/prompts](https://github.com/maplephp/prompts) | — | Interactive CLI: text, password, toggle, select, list, confirm, progress bar |
| [maplephp/unitary](https://github.com/maplephp/unitary) | — | Testing framework: 100k+ tests/sec, built-in mocking, zero external dependencies |

---

## Configuration Reference

### Environment Variables

Copy `.env.example` to `.env` and fill in your values. The `env()` helper reads from `$_ENV` and `$_SERVER`.

| Key | Default | Purpose |
|---|---|---|
| `APP_TITLE` | — | Application name |
| `APP_ENV` | — | Environment name (`local`, `production`, etc.) |
| `DB_CONNECTION` | — | Active connection key (`mysql`, `sqlite`, `test`) |
| `DB_HOST` | `127.0.0.1` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_DATABASE` | — | Database name (or SQLite file path) |
| `DB_USERNAME` | — | Database username |
| `DB_PASSWORD` | — | Database password |

### `configs/configs.php`

```php
return [
    'app_title' => env('APP_TITLE'),
    'env'       => env('APP_ENV'),
    'timezone'  => 'UTC',
    'locale'    => 'en_US',
];
```

### `configs/http.php`

```php
return [
    "middleware" => [
        "global" => [
            // Add MiddlewareInterface implementations here.
            // Applied to every HTTP request in the order listed.
        ]
    ]
];
```

### `configs/providers.php`

```php
return [
    // Add ServiceProvider subclasses here.
    // Providers are registered in order, then booted in order.
    \MaplePHP\Core\Providers\DatabaseProvider::class,
];
```

---

## CLI Command Reference

| Command | Description |
|---|---|
| `./maple serve` | Start the development server on `localhost:8000` |
| `./maple serve --host=0.0.0.0 --port=8080` | Custom host and port |
| `./maple make --type=controller --name=User` | Generate a controller class |
| `./maple make --type=service --name=User` | Generate a service class |
| `./maple make --type=migration --name=CreateUsers` | Generate a migration class |
| `./maple make --type=command --name=Import` | Generate a CLI command class |
| `./maple migrate` | Run all pending migrations |
| `./maple migrate:up` | Step one migration up |
| `./maple migrate:down` | Step one migration down |
| `./maple migrate:fresh` | Roll all migrations down then back up |
| `./maple migrate:clear` | Roll all migrations down |
| `./maple migrate --name=CreateUsers` | Run one specific migration by name (always re-runs) |
