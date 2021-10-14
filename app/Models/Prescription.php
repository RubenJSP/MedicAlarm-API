<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'description',
        'medicament_id',
        'patient_id',
        'medic_id',
        'interval',
        'duration',
        'finished',
    ];

    /**
     * Get the patient that owns the Prescription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
     /**
     * Get the patient that owns the Prescription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medic()
    {
        return $this->belongsTo(User::class, 'medic_id');
    }
    /**
     * Get the patient that owns the Prescription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_id');
    }
    
}
