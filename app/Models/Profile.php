<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_center_name',
        'cct',
        'zone',
        'rte_name',
        'shift',
        'sector',
        'report_period',
        'commissioned_hours',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
