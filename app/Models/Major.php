<?php

namespace App\Models;

use App\Models\Alumni;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    protected $table = 'majors';

    protected $guarded = ['id'];
    
    public function alumnis(): HasMany
    {
        return $this->hasMany(Alumni::class);
    }
}
