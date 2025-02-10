<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RonExchangeRate extends Model
{
    protected $table = 'ron_exchange_rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'currency',
        'rate',
        'date'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'date' => 'date'
    ];

    public $timestamps = true;
}
