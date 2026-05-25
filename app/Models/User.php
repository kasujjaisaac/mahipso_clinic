<?php

namespace App\Models;

use App\Models\Branch;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'branch_id', 'employee_number', 'job_title', 'department', 'line_supervisor_id', 'failed_login_count', 'locked_until', 'last_password_changed_at', 'must_change_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'last_password_changed_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function lineSupervisor()
    {
        return $this->belongsTo(User::class, 'line_supervisor_id');
    }

    public function supervisedUsers()
    {
        return $this->hasMany(User::class, 'line_supervisor_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles
            ->contains(fn ($role) => in_array($module, method_exists($role, 'allowedModules') ? $role->allowedModules() : $this->modulesFromRole($role), true));
    }

    public function canAccessAnyModule(array $modules): bool
    {
        return collect($modules)->contains(fn ($module) => $this->canAccessModule($module));
    }

    private function modulesFromRole($role): array
    {
        $access = $role->module_access ?? null;

        if (is_string($access)) {
            $decoded = json_decode($access, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($access) ? $access : Role::defaultModulesFor($role->name);
    }
}
