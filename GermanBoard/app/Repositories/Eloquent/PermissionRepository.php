<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function create(string $permission_name): void
    {
        Permission::create([
            'name' => $permission_name,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getAll(): array
    {
        return Permission::select('name', 'guard_name')->all()->toArray();
    }
}
