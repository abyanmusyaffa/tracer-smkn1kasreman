<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $guarded = ['id'];

    public function alumnis(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
    }
}
