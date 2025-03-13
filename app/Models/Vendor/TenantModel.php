<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class TenantModel extends Model
{
    protected static function booted()
    {
        static::creating(function ($model) {
            if (session()->has('current_vendor_id')) {
                $model->vendor_id = session('current_vendor_id');
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (session()->has('current_vendor_id')) {
                $builder->where('vendor_id', session('current_vendor_id'));
            }
        });
    }
}
