<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | ROLE IDs
    |--------------------------------------------------------------------------
    | 1 = Super Admin
    | 2 = Admin
    | 3 = Customer
    | 4 = Seller
    | 5 = Delivery Boy
    | 6 = Branch Manager
    | 7 = Branch Staff
    */

    public const ROLE_SUPER_ADMIN    = 1;
    public const ROLE_ADMIN          = 2;
    public const ROLE_CUSTOMER       = 3;
    public const ROLE_SELLER         = 4;
    public const ROLE_DELIVERY_BOY   = 5;
    public const ROLE_BRANCH_MANAGER = 6;
    public const ROLE_BRANCH_STAFF   = 7;

    protected $fillable = [
        'name',
        'username',
        'email',
        'google_id',
        'avatar',
        'country',
        'mobile_number',
        'phone',
        'date_of_birth',
        'gender',
        'password',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'active_mode',
        'is_branch_staff',
        'staff_owner_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at'    => 'datetime',
            'date_of_birth'     => 'date',
            'password'          => 'hashed',
            'is_branch_staff'   => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function sellerProfile()
    {
        return $this->hasOne(Seller::class, 'user_id');
    }

    public function branchStaff()
    {
        return $this->hasOne(BranchStaff::class, 'user_id');
    }

    public function managedBranches()
    {
        return $this->hasMany(SellerBranch::class, 'manager_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE CHECKS
    |--------------------------------------------------------------------------
    */

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has ANY of the given role names.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has ALL of the given role names.
     */
    public function hasAllRoles(array $roleNames): bool
    {
        $userRoles = $this->roles()->whereIn('name', $roleNames)->pluck('name')->toArray();
        return count(array_intersect($roleNames, $userRoles)) === count($roleNames);
    }

    public function hasRoleId(int $roleId): bool
    {
        return $this->roles()->where('roles.id', $roleId)->exists();
    }

    /**
     * Assign a role by name (idempotent — safe to call multiple times).
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return;
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove a role by name.
     */
    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function isMode(string $mode): bool
    {
        return $this->active_mode === $mode;
    }

    /*
    |--------------------------------------------------------------------------
    | BRANCH STAFF HELPERS (roles 6 & 7)
    |--------------------------------------------------------------------------
    */

    public function isBranchStaff(): bool
    {
        return (bool) $this->is_branch_staff;
    }

    public function isBranchManager(): bool
    {
        if (!$this->isBranchStaff()) {
            return false;
        }

        if ($this->branchStaff?->role === 'manager') {
            return true;
        }

        return $this->hasRoleId(self::ROLE_BRANCH_MANAGER);
    }

    public function isBranchStaffOnly(): bool
    {
        if (!$this->isBranchStaff()) {
            return false;
        }

        if ($this->branchStaff?->role === 'staff') {
            return true;
        }

        return $this->hasRoleId(self::ROLE_BRANCH_STAFF);
    }

    public function isSellerOwner(): bool
    {
        return $this->sellerProfile !== null && !$this->isBranchStaff();
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION HELPERS
    |--------------------------------------------------------------------------
    */

    public function canManageProducts(): bool
    {
        return $this->isSellerOwner() || $this->isBranchStaff();
    }

    public function canManageBranches(): bool
    {
        return $this->isSellerOwner();
    }

    public function canManageStaff(): bool
    {
        if ($this->isSellerOwner()) return true;
        if ($this->isBranchManager()) return true;
        return false;
    }

    public function canManageServices(): bool
    {
        return $this->isSellerOwner() || $this->isBranchStaff();
    }

    /*
    |--------------------------------------------------------------------------
    | BRANCH ACCESS
    |--------------------------------------------------------------------------
    */

    public function getAccessibleBranches()
    {
        if ($this->isSellerOwner()) {
            return $this->sellerProfile->branches()->where('status', 1)->get();
        }

        if ($this->isBranchStaff() && $this->branchStaff?->branch) {
            return collect([$this->branchStaff->branch])->filter();
        }

        return collect();
    }

    /*
    |--------------------------------------------------------------------------
    | DISPLAY HELPERS
    |--------------------------------------------------------------------------
    */

    public function getDisplayRoleAttribute(): string
    {
        if ($this->isSellerOwner()) return 'Owner';

        if ($this->isBranchStaff()) {
            $role = $this->branchStaff?->role;
            if ($role === 'manager') return 'Manager';
            if ($role === 'staff')   return 'Staff';
        }

        $priority = [
            'Super Admin'  => 1,
            'Admin'        => 2,
            'Delivery Boy' => 3,
            'Seller'       => 4,
            'Customer'     => 5,
        ];

        $primary = $this->roles->sortBy(fn($r) => $priority[$r->name] ?? 99)->first();
        return $primary?->name ?? 'User';
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->display_role) {
            'Owner'       => 'primary',
            'Manager'     => 'info',
            'Staff'       => 'success',
            'Admin'       => 'danger',
            'Super Admin' => 'dark',
            'Delivery Boy' => 'warning',
            default       => 'secondary',
        };
    }

    public function getBranchRoleLabelAttribute(): ?string
    {
        if (!$this->isBranchStaff()) return null;
        return $this->isBranchManager() ? 'Manager' : 'Staff';
    }
}