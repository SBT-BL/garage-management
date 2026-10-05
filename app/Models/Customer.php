<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'whatsapp_number', 'address'])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

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
     * Option payload for Select2.
     *
     * @return array{id: int, text: string}
     */
    public function selectOption(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->name,
        ];
    }

    /**
     * Get the customer's display initial for avatars.
     */
    public function initial(): string
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }

    /**
     * Get the vehicles for the customer.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get the job cards for the customer.
     */
    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }
}
