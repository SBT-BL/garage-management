<?php

namespace App\Models;

use App\Enums\VehicleType;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_id', 'vehicle_model', 'vehicle_number', 'vehicle_type', 'notes'])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    /**
     * Get the customer that owns the vehicle.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the job cards for the vehicle.
     */
    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }

    /**
     * Label used in job card vehicle dropdowns.
     */
    public function optionLabel(): string
    {
        return $this->vehicle_number.' - '.$this->vehicle_model;
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
            'text' => $this->optionLabel(),
        ];
    }

    /**
     * Scope a query to search vehicles by number or model.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query->where('vehicle_number', 'like', "%{$term}%")
                ->orWhere('vehicle_model', 'like', "%{$term}%");
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vehicle_type' => VehicleType::class,
        ];
    }
}
