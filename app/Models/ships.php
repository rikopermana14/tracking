<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ships extends Model
{
    protected $table = 'ships';

    protected $fillable = [
        'gpsid','vname','status','latitude','longitude',
        'speed','direct','mileage','datetime_utc'
    ];

    protected $casts = [
        'latitude'     => 'float',
        'longitude'    => 'float',
        'speed'        => 'float',
        'direct'       => 'float',
        'mileage'      => 'float',
        'datetime_utc' => 'datetime:Y-m-d H:i:s',
    ];
}
