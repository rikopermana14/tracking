<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;
    protected $table = 'ships';
    protected $primaryKey = "id";
    protected $fillable = [
        'DATE_TIME_(UTC)',
        'MMSI',
        'LATITUDE',
        'LONGITUDE',
        'COURSE',
        'SPEED',
        'HEADING',
        'NAVSTAT',
        'IMO',
        'NAME',
        'CALLSIGN',
        'AISTYPE',
        'A',
        'B',
        'C',
        'D',
        'DRAUGHT',
        'DESTINATION',
        'ETA',
    ];
}
