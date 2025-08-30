<?php

use App\Enums\UserRole;
use App\Models\User;

it('allows admin to view sales list', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $response = $this->actingAs($admin)->get(route('admin.sales'));
    $response->assertOk();
});
