<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteWaypoint extends Model
{
    use HasFactory;
    protected $fillable = [

        'route_id',
        'sequence',
        'latitude',
        'longitude',
        'course',
        'distance_nm'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
