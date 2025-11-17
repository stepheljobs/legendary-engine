<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Get the tenant owned by the user.
     */
    public function tenant()
    {
        return $this->hasOne(\Stancl\Tenancy\Database\Models\Tenant::class, 'user_id');
    }

    /**
     * Get the team member record for this user in the current tenant.
     */
    public function teamMember()
    {
        return $this->hasOne(TeamMember::class);
    }

    /**
     * Get all invitations sent by this user.
     */
    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Get all invitations accepted by this user.
     */
    public function acceptedInvitations()
    {
        return $this->hasMany(Invitation::class, 'accepted_by');
    }

    /**
     * Check if the user has a specific permission in the current tenant.
     */
    public function hasPermission(string $permissionName): bool
    {
        $teamMember = $this->teamMember()->with('role.permissions')->first();

        if (!$teamMember) {
            return false;
        }

        return $teamMember->hasPermission($permissionName);
    }

    /**
     * Check if the user has a specific role in the current tenant.
     */
    public function hasRole(string $roleName): bool
    {
        $teamMember = $this->teamMember()->with('role')->first();

        if (!$teamMember) {
            return false;
        }

        return $teamMember->hasRole($roleName);
    }

    /**
     * Check if the user is an owner in the current tenant.
     */
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    /**
     * Check if the user is an admin in the current tenant.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Get the user's role in the current tenant.
     */
    public function getRole(): ?Role
    {
        $teamMember = $this->teamMember()->with('role')->first();

        return $teamMember?->role;
    }

    /**
     * Get all permissions for the user in the current tenant.
     */
    public function getPermissions()
    {
        $teamMember = $this->teamMember()->with('role.permissions')->first();

        return $teamMember?->role?->permissions ?? collect();
    }
}
