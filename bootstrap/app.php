<?php

use App\Console\Commands\BackupRunBackgroundDatabase;
use App\Models\ActivityLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'validation_character' => \App\Http\Middleware\ValidateSpecialCharacters::class,
            'update_last_seen' => \App\Http\Middleware\UpdateLastSeen::class,
            'currentRole' => \App\Http\Middleware\CheckCurrentRole::class,
        ]);

        // Menjalankan middleware untuk semua request POST
        // $middleware->append(\App\Http\Middleware\ValidateSpecialCharacters::class);
        $middleware->prepend(\App\Http\Middleware\LogActivityMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {

            if (!request()) {
                return;
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'request_data' => json_encode(
                    request()->except(['password', 'password_confirmation'])
                ),
                'ip_address' => request()->ip(),
                'status_code' => $e instanceof HttpExceptionInterface
                    ? $e->getStatusCode()
                    : 500,
                'error_message' => $e->getMessage(),
            ]);
        });
    })
    ->withCommands([
        BackupRunBackgroundDatabase::class
    ])
    ->create();
