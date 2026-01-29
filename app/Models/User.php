<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'referred_by',
        'enrolled_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrolled_date' => 'date',
        ];
    }

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the referrer (the user who referred this user).
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get users referred by this user.
     */
    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Get the categories this user belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_category');
    }

    /**
     * Check if user is a distributor.
     *
     * @return bool
     */
    public function isDistributor(): bool
    {
        return $this->categories()->where('category_id', 1)->exists();
    }

    /**
     * Get distributors referred by this user up to a specific date.
     *
     * @param  string  $date
     * @return int
     */
    public function getReferredDistributorsCountAsOf(string $date): int
    {
        return $this->referredUsers()
            ->whereHas('categories', function ($query) {
                $query->where('category_id', 1); // Distributor
            })
            ->where('enrolled_date', '<=', $date)
            ->count();
    }
}
