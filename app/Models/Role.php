<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'module_access',
    ];

    protected $casts = [
        'module_access' => 'array',
    ];

    public function allowedModules(): array
    {
        if (is_array($this->module_access)) {
            return $this->module_access;
        }

        return self::defaultModulesFor($this->name);
    }

    public static function defaultModulesFor(string $roleName): array
    {
        return config("clinic_modules.default_access.{$roleName}", []);
    }

    public static function moduleOptions(): array
    {
        return config('clinic_modules.modules', []);
    }
}
