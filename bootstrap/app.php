<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Helper Global (settings, company_name, company_logo_url)
|--------------------------------------------------------------------------
| Di-require langsung agar tetap tersedia walau autoloader Composer di
| server produksi belum di-regenerate (composer dump-autoload). Semua
| fungsi di file tsb memakai guard function_exists sehingga aman jika
| ter-load dua kali (oleh composer + require ini).
*/
if (is_file(__DIR__.'/../app/Support/helpers.php')) {
    require_once __DIR__.'/../app/Support/helpers.php';
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
