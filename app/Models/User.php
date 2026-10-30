<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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
        'date_of_birth',
        'gender',
        'password',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'active_mode',
        'is_branch_staff',
        'staff_owner_id',
        'cover_photo',
        'bio',
        'website',
        'location',
        'mobile_verified_at',
        'mobile_otp',
        'mobile_otp_expires_at',
        'account_status',
        'deactivated_at',
        'deletion_requested_at',
        'deletion_scheduled_at',
        'relationship_status',
        'categories',
        'languages',
        'hobbies',
        'address',
        'latitude',
        'longitude',
        'pending_email',
        'email_change_otp',
        'email_change_otp_expires_at',
        'pending_mobile', 'mobile_change_otp', 'mobile_change_otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'mobile_otp',
        'email_change_otp',
        'mobile_change_otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at'    => 'datetime',
            'date_of_birth'     => 'date',
            'password'          => 'hashed',
            'is_branch_staff'   => 'boolean',
            'mobile_verified_at'     => 'datetime',
            'mobile_otp_expires_at'  => 'datetime',
            'deactivated_at'         => 'datetime',
            'deletion_requested_at'  => 'datetime',
            'deletion_scheduled_at'  => 'datetime',
            'categories' => 'array',
            'languages'  => 'array',
            'hobbies'    => 'array',
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
            'email_change_otp_expires_at' => 'datetime',
            'mobile_change_otp_expires_at' => 'datetime',
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

    public function websites(): HasMany
    {
        return $this->hasMany(UserWebsite::class)->orderBy('sort_order');
    }

    public function works(): HasMany
    {
        return $this->hasMany(UserWork::class)->orderBy('sort_order');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(UserEducation::class)->orderBy('sort_order');
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
    | NEW: ACCOUNT STATUS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->account_status === 'active' && is_null($this->deleted_at);
    }

    public function isDeactivated(): bool
    {
        return $this->account_status === 'deactivated';
    }

    public function isPendingDeletion(): bool
    {
        return $this->account_status === 'deletion_pending';
    }

    public function isSoftDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | NEW: ACTIVE MODES (for Account Switcher)
    |--------------------------------------------------------------------------
    */

    /**
     * Return all modes this user can switch into based on their roles.
     *
     * @return array<int, array{mode:string, label:string, icon:string, route:string}>
     */
    public function getAvailableModes(): array
    {
        $modes = [];

        if ($this->hasRole('Customer')) {
            $modes[] = [
                'mode'  => 'customer',
                'label' => 'Customer',
                'icon'  => 'shopping-bag',
                'route' => 'home',
            ];
        }

        if ($this->isSellerOwner() || $this->isBranchStaff()) {
            $modes[] = [
                'mode'  => 'seller',
                'label' => 'Seller',
                'icon'  => 'store',
                'route' => 'seller.dashboard',
            ];
        }

        if ($this->hasAnyRole(['Super Admin', 'Admin'])) {
            $modes[] = [
                'mode'  => 'admin',
                'label' => 'Admin',
                'icon'  => 'shield-check',
                'route' => 'admin.dashboard',
            ];
        }

        if ($this->hasRole('Delivery Boy')) {
            $modes[] = [
                'mode'  => 'delivery',
                'label' => 'Delivery',
                'icon'  => 'truck',
                'route' => 'delivery.dashboard',
            ];
        }

        return $modes;
    }

    public function canSwitchTo(string $mode): bool
    {
        return in_array($mode, array_column($this->getAvailableModes(), 'mode'), true);
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

    public function getAvatarUrlAttribute(): string
    {
        if (
            $this->avatar &&
            \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)
        ) {
            return asset('storage/' . ltrim($this->avatar, '/'));
        }

        $initial = urlencode(substr($this->name ?? 'U', 0, 1));

        return "https://ui-avatars.com/api/?name={$initial}&size=200&background=0d6efd&color=fff";
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        if (
            $this->cover_photo &&
            \Illuminate\Support\Facades\Storage::disk('public')->exists($this->cover_photo)
        ) {
            return asset('storage/' . ltrim($this->cover_photo, '/'));
        }

        return null;
    }

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function isMobileVerified(): bool
    {
        return !is_null($this->mobile_verified_at);
    }

    public function isFullyVerified(): bool
    {
        return $this->isEmailVerified() && $this->isMobileVerified();
    }

    public function getVerificationStatusAttribute(): array
    {
        return [
            'email'  => [
                'verified' => $this->isEmailVerified(),
                'label'    => $this->isEmailVerified() ? 'Verified' : 'Not Verified',
                'value'    => $this->email,
            ],
            'mobile' => [
                'verified' => $this->isMobileVerified(),
                'label'    => $this->isMobileVerified() ? 'Verified' : 'Not Verified',
                'value'    => $this->mobile_number,
            ],
            'overall' => $this->isFullyVerified() ? 'Fully Verified' : 'Partially Verified',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | NEW: PUBLIC PROFILE URL
    |--------------------------------------------------------------------------
    */

    public function getProfileUrlAttribute(): string
    {
        return route('profile.show', $this->username ?: $this->id);
    }

    public function loginActivities(): HasMany
    {
        return $this->hasMany(LoginActivity::class)->latest();
    }

    public function wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(Wallet::class);
}
}
