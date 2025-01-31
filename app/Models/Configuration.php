<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

     
    protected $table = 'configurations';

     
    protected $primaryKey = 'id_configuration';

     
    protected $fillable = [
        'cle',
        'valeur',
    ];

     
    public $timestamps = false;

    
    public function __toString()
    {
        return "Configuration{id_configuration={$this->id_configuration}, cle='{$this->cle}', valeur='{$this->valeur}'}";
    }
}
