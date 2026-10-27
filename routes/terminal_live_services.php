<?php
// Require once at the bottom of routes/web.php, alongside the existing delivery routes.
\Illuminate\Support\Facades\Route::post('/admin/terminal-delivery/live-services',
    [\App\Http\Controllers\Admin\DeliverySettingController::class, 'discoverLiveServices'])
    ->middleware(['auth', 'permission:manage store', 'throttle:5,1'])
    ->name('admin.terminal-delivery.live-services');
