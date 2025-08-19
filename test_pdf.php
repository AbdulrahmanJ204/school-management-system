<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        api: __DIR__.'/routes/api.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Set the application instance for facades
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);

try {
    echo "Testing PDF generation...\n";
    
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.daily-log-pdf', [
        'report' => null,
        'activityLogs' => collect(),
        'errorLogs' => collect(),
        'date' => now()
    ]);
    
    echo "PDF generation successful!\n";
    
    // Test storage
    echo "Testing storage...\n";
    $content = $pdf->output();
    \Illuminate\Support\Facades\Storage::put('test.pdf', $content);
    
    if (\Illuminate\Support\Facades\Storage::exists('test.pdf')) {
        echo "Storage test successful!\n";
        \Illuminate\Support\Facades\Storage::delete('test.pdf');
    } else {
        echo "Storage test failed!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
