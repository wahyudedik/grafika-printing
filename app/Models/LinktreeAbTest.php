<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\HasMany;
use App\Models\Vendor\Linktree;

class LinktreeAbTest extends Model
{
    protected $fillable = [
        'linktree_id',
        'name',
        'variant_a',
        'variant_b',
        'variant_a_config',
        'variant_b_config',
        'status',
        'traffic_split',
        'min_samples',
        'started_at',
        'ended_at',
        'winner',
        'notes',
    ];

    protected $casts = [
        'variant_a_config' => 'array',
        'variant_b_config' => 'array',
        'traffic_split' => 'integer',
        'min_samples' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the linktree that owns this test.
     */
    public function linktree(): BelongsTo
    {
        return $this->belongsTo(Linktree::class);
    }

    /**
     * Get the results for this test.
     */
    public function results(): HasMany
    {
        return $this->hasMany(LinktreeAbTestResult::class, 'ab_test_id');
    }

    /**
     * Check if test is currently running.
     */
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Check if test has enough data to evaluate.
     */
    public function hasEnoughData(): bool
    {
        return $this->results()->count() >= $this->min_samples;
    }

    /**
     * Get impressions count for a variant.
     */
    public function getImpressions(string $variant): int
    {
        return $this->results()->where('variant', $variant)->count();
    }

    /**
     * Get click count for a variant.
     */
    public function getClicks(string $variant): int
    {
        return $this->results()->where('variant', $variant)->where('is_click', true)->count();
    }

    /**
     * Get conversion rate for a variant.
     */
    public function getConversionRate(string $variant): float
    {
        $impressions = $this->getImpressions($variant);
        if ($impressions === 0) {
            return 0.0;
        }
        return round(($this->getClicks($variant) / $impressions) * 100, 2);
    }

    /**
     * Determine winner based on conversion rates.
     */
    public function evaluate(): ?string
    {
        if (!$this->hasEnoughData()) {
            return null;
        }

        $rateA = $this->getConversionRate('variant_a');
        $rateB = $this->getConversionRate('variant_b');

        // Minimum 1% difference to declare a winner
        if (abs($rateA - $rateB) < 1.0) {
            return null; // No significant difference
        }

        return $rateA >= $rateB ? 'variant_a' : 'variant_b';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'running' => 'Berjalan',
            'paused' => 'Dijeda',
            'completed' => 'Selesai',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'running' => 'success',
            'paused' => 'warning',
            'completed' => 'blue',
            default => 'secondary',
        };
    }

    /**
     * Determine which variant to show for a given visitor.
     */
    public function getVariantForVisitor(string $visitorId): string
    {
        // Deterministic assignment based on visitor ID hash
        $hash = crc32($visitorId . $this->id);
        $bucket = $hash % 100;

        return $bucket < $this->traffic_split ? 'variant_a' : 'variant_b';
    }
}
