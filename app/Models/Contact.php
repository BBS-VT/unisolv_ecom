<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'company_id',
        'contactable_type',
        'contactable_id',
        'name',
        'email',
        'phone',
        'mobile',
        'position',
        'department',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // When setting a contact as primary, unset other primary contacts
        static::saving(function ($contact) {
            if ($contact->is_primary && $contact->isDirty('is_primary')) {
                static::where('contactable_type', $contact->contactable_type)
                    ->where('contactable_id', $contact->contactable_id)
                    ->where('id', '!=', $contact->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    // ==================== Relationships ====================

    /**
     * Get the parent contactable model (Customer, Supplier, etc.)
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Contact belongs to a company (multi-tenancy)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ==================== Scopes ====================

    /**
     * Scope: Active contacts only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Primary contacts only
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope: Filter by company
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? (auth()->user()->company_id ?? null);

        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Contacts for a specific contactable type
     */
    public function scopeForType($query, $type)
    {
        return $query->where('contactable_type', $type);
    }

    /**
     * Scope: Customer contacts only
     */
    public function scopeCustomers($query)
    {
        return $query->where('contactable_type', 'App\\Models\\Customer');
    }

    /**
     * Scope: Supplier contacts only
     */
    public function scopeSuppliers($query)
    {
        return $query->where('contactable_type', 'App\\Models\\Supplier');
    }

    /**
     * Scope: Search by name, email, or phone
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%");
        });
    }

    // ==================== Accessors ====================

    /**
     * Get formatted phone number (prefer mobile)
     */
    public function getBestPhoneAttribute(): ?string
    {
        return $this->mobile ?? $this->phone;
    }

    /**
     * Get display name with position
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->position) {
            return "{$this->name} ({$this->position})";
        }

        return $this->name;
    }

    /**
     * Get full contact info string
     */
    public function getFullContactInfoAttribute(): string
    {
        $info = [$this->name];

        if ($this->position) {
            $info[] = $this->position;
        }

        if ($this->email) {
            $info[] = $this->email;
        }

        if ($this->bestPhone) {
            $info[] = $this->bestPhone;
        }

        return implode(' | ', $info);
    }

    // ==================== Helper Methods ====================

    /**
     * Check if this is a customer contact
     */
    public function isCustomerContact(): bool
    {
        return $this->contactable_type === 'App\\Models\\Customer';
    }

    /**
     * Check if this is a supplier contact
     */
    public function isSupplierContact(): bool
    {
        return $this->contactable_type === 'App\\Models\\Supplier';
    }

    /**
     * Make this contact the primary contact
     */
    public function makePrimary(): bool
    {
        $this->is_primary = true;
        return $this->save();
    }

    /**
     * Activate this contact
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate this contact
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

}
