<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'whatsapp_number', 'address'])]
class Customer extends Model
{
    /**
     * Scope a query to search customers by name, WhatsApp number, or address.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('whatsapp_number', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%");
        });
    }

    /**
     * Get the customer's display initial for avatars.
     */
    public function initial(): string
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }

    /*
     * Future relationship:
     * public function vehicles(): HasMany
     * {
     *     return $this->hasMany(Vehicle::class);
     * }
     */
}
