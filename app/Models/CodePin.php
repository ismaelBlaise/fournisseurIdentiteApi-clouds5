<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodePin extends Model
{
    use HasFactory;

     
    protected $table = 'code_pin';

     
    protected $primaryKey = 'id_code_pin';

     
    protected $fillable = [
        'codepin',
        'date_expiration',
        'id_utilisateurs',
    ];

     
    public $timestamps = false;

     
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateurs');
    }
}
