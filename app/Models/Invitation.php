<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant;

class Invitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'email',
        'token',
        'role_name',
        'invited_by',
        'accepted_at',
        'accepted_by',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the tenant that this invitation belongs to.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who sent the invitation.
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the user who accepted the invitation.
     */
    public function acceptedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * Generate a unique invitation token.
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Create a new invitation.
     */
    public static function createInvitation(
        string $tenantId,
        string $email,
        string $roleName = 'member',
        ?int $invitedBy = null,
        int $expirationDays = 7
    ): self {
        return self::create([
            'tenant_id' => $tenantId,
            'email' => strtolower($email),
            'token' => self::generateToken(),
            'role_name' => $roleName,
            'invited_by' => $invitedBy,
            'expires_at' => now()->addDays($expirationDays),
        ]);
    }

    /**
     * Check if the invitation has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the invitation has been accepted.
     */
    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    /**
     * Check if the invitation is still valid.
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isAccepted();
    }

    /**
     * Mark the invitation as accepted.
     */
    public function markAsAccepted(int $userId): self
    {
        $this->update([
            'accepted_at' => now(),
            'accepted_by' => $userId,
        ]);

        return $this;
    }

    /**
     * Scope to get only pending invitations.
     */
    public function scopePending($query)
    {
        return $query->whereNull('accepted_at')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to get invitations for a specific tenant.
     */
    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope to get invitations for a specific email.
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', strtolower($email));
    }
}
