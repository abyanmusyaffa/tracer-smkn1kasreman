<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Alumni extends Model
// {
    
// }


namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

use App\Models\Major;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Alumni extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($alumni) {
            if ($alumni->photo && $alumni->photo !== '/default/alumni.svg') {
                Storage::disk('public')->delete($alumni->photo);
            }
        });
    
        // Menghapus file lama saat record diperbarui
        static::updating(function ($alumni) {
            if ($alumni->isDirty('photo')) {
                $oldPhoto = $alumni->getOriginal('photo');
                if ($oldPhoto && $oldPhoto !== '/default/alumni.svg') {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        });
    }

    public function testimonials(): HasOne
    {
        return $this->hasOne(Testimonial::class);
    }

    public function majors(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
}
