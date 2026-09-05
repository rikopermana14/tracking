<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
      protected $fillable = [
        'route_name',
        'origin',
        'destination'
    ];

    public function waypoints()
    {
        return $this->hasMany(RouteWaypoint::class);
    }
}
