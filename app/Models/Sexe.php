<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sexe extends Model
{
    use HasFactory;

     
    protected $table = 'sexes';

    // Clé primaire
    protected $primaryKey = 'id_sexe';

     
    protected $fillable = [
        'sexe',
    ];

    
    public $timestamps = false;
}
