<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinktreeAbTestResult extends Model
{
    protected $fillable = [
        'ab_test_id',
        'variant',
        'visitor_id',
        'is_click',
        'shown_at',
    ];

    protected $casts = [
        'is_click' => 'boolean',
        'shown_at' => 'datetime',
    ];

    /**
     * Get the A/B test that owns this result.
     */
    public function abTest(): BelongsTo
    {
        return $this->belongsTo(LinktreeAbTest::class, 'ab_test_id');
    }
}
