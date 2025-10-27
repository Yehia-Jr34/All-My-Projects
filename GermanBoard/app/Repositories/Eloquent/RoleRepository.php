<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function create(string $name): void
    {
        Role::create([
            'name' => $name,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function storeOrFind(string $role): string
    {
        $role = Role::where('name', $role)->first();
        if ($role) {
            $role = Role::create([
                'name' => $role,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $role->name;
    }

    public function getAll(): array
    {
        return Role::select('name', 'guard_name')->all()->toArray();
    }
}
