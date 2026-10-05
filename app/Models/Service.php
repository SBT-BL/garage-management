<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * Scope a query to search services by name.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Option payload for Select2.
     *
     * A catalog price is included only when that attribute already exists.
     *
     * @return array<string, mixed>
     */
    public function selectOption(): array
    {
        $option = [
            'id' => $this->id,
            'text' => $this->name,
        ];

        $price = $this->getAttributes()['price'] ?? null;

        if ($price !== null && $price !== '') {
            $option['data_attributes'] = [
                'data-price' => (string) $price,
            ];
        }

        return $option;
    }

    /**
     * Get the service's display initial for avatars.
     */
    public function initial(): string
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }

    /**
     * Get the job card lines that use this service.
     */
    public function jobCardServices(): HasMany
    {
        return $this->hasMany(JobCardService::class);
    }
}
