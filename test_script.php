<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\Admin::find(3);
$user->update(['user_types' => ['Women Entrepreneurs']]);
echo "DB raw after update: " . DB::table('admin_users')->where('id', 3)->value('user_types') . "\n";
