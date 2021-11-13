<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'description',
        'days',
        'frecuency',
        'next_alarm',
        'end_date',
        'patient_id',
        'contact_id',
        'notify'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'frecuency' => 'integer',
        'patient_id' => 'integer',
        'contact_id' => 'integer',
        'notify' => 'integer',
        'days' => 'integer',

    ];
    /**
     * Get the contact that owns the Alarm
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
