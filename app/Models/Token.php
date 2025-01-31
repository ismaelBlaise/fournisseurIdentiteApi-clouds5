<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

     
    protected $table = 'tokens';

    
    protected $primaryKey = 'id_token';

     
    protected $fillable = [
        'token',
        'date_expiration',
        'id_utilisateurs',
    ];

     
    public $timestamps = false;

     
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateurs');
    }
}
