<?php

$types = ['Shop Boy', 'Installer', 'Women Entrepreneurs'];
$users = \App\Models\User::all();
foreach($users as $user) {
    $user->user_type = $types[array_rand($types)];
    $user->save();
}
echo "Updated " . count($users) . " users with new types.\n";
