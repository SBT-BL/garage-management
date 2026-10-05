<?php

namespace App\Models;

use Database\Factories\JobCardServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_card_id', 'service_id', 'price', 'remark'])]
class JobCardService extends Model
{
    /** @use HasFactory<JobCardServiceFactory> */
    use HasFactory;

    /**
     * Get the job card that owns the line.
     */
    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    /**
     * Get the catalog service for the line.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }
}
