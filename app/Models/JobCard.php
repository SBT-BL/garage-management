<?php

namespace App\Models;

use App\Enums\JobCardStatus;
use Database\Factories\JobCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

#[Fillable(['job_card_number', 'date', 'customer_id', 'vehicle_id', 'grand_total', 'remark', 'status'])]
class JobCard extends Model
{
    /** @use HasFactory<JobCardFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'Pending',
    ];

    /**
     * Get the customer that owns the job card.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the vehicle for the job card.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the service lines for the job card.
     */
    public function jobCardServices(): HasMany
    {
        return $this->hasMany(JobCardService::class);
    }

    /**
     * Scope a query to search job cards by number, status, customer, or vehicle.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('job_card_number', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhereHas('customer', function (Builder $query) use ($term) {
                    $query->where('name', 'like', "%{$term}%");
                })
                ->orWhereHas('vehicle', function (Builder $query) use ($term) {
                    $query->where('vehicle_number', 'like', "%{$term}%")
                        ->orWhere('vehicle_model', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Default listing filters. Missing dates use the last 7 days.
     * Missing status keeps Pending and In Progress only.
     *
     * @return array{start_date: string, end_date: string, status: string, customer_id: string, service_id: string}
     */
    public static function listingFilters(Request $request): array
    {
        $hasDates = $request->exists('start_date') || $request->exists('end_date');

        return [
            'start_date' => $hasDates ? $request->string('start_date')->toString() : now()->subDays(6)->toDateString(),
            'end_date' => $hasDates ? $request->string('end_date')->toString() : now()->toDateString(),
            'status' => $request->exists('status') ? $request->string('status')->toString() : 'open',
            'customer_id' => $request->string('customer_id')->toString(),
            'service_id' => $request->string('service_id')->toString(),
        ];
    }

    /**
     * Limit a job card list by the listing filters.
     *
     * @param  array{start_date: string, end_date: string, status: string, customer_id: string, service_id: string}  $filters
     */
    public function scopeListing(Builder $query, array $filters): Builder
    {
        $startDate = self::listingDate($filters['start_date']);
        $endDate = self::listingDate($filters['end_date']);

        if ($startDate !== null) {
            $query->whereDate('job_cards.date', '>=', $startDate);
        }

        if ($endDate !== null) {
            $query->whereDate('job_cards.date', '<=', $endDate);
        }

        $status = $filters['status'];

        if ($status === '' || $status === 'open') {
            $query->whereIn('job_cards.status', [
                JobCardStatus::Pending->value,
                JobCardStatus::InProgress->value,
            ]);
        } elseif ($status !== 'all' && in_array($status, JobCardStatus::values(), true)) {
            $query->where('job_cards.status', $status);
        }

        if ($filters['customer_id'] !== '' && ctype_digit($filters['customer_id'])) {
            $query->where('job_cards.customer_id', $filters['customer_id']);
        }

        if ($filters['service_id'] !== '' && ctype_digit($filters['service_id'])) {
            $query->whereHas('jobCardServices', function (Builder $query) use ($filters): void {
                $query->where('service_id', $filters['service_id']);
            });
        }

        return $query;
    }

    /**
     * Accept only a real Y-m-d value. An empty value means that bound is unused.
     */
    private static function listingDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if ($parsed === false || $parsed->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }

    /**
     * Generate the next unique job card number for today.
     *
     * Call this inside a transaction so lockForUpdate covers the latest row.
     */
    public static function nextNumber(): string
    {
        $prefix = 'JC-'.now()->format('Ymd').'-';

        $latest = static::query()
            ->where('job_card_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('job_card_number')
            ->value('job_card_number');

        $sequence = 1;

        if (is_string($latest) && preg_match('/(\d+)$/', $latest, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'grand_total' => 'decimal:2',
            'status' => JobCardStatus::class,
        ];
    }
}
