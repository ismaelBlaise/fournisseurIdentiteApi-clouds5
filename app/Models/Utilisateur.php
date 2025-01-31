<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;

     
    protected $table = 'utilisateurs';

     
    protected $primaryKey = 'id_utilisateurs';

     
    public $incrementing = true;
    protected $fillable = [
        'email',
        'nom',
        'prenom',
        'date_naissance',
        'mot_de_passe',
        'etat',
        'nb_tentative',
        'id_sexe',
    ];

     
    public $timestamps = false;

    
    public function sexe()
    {
        return $this->belongsTo(Sexe::class, 'id_sexe');
    }

     
    public function __toString()
    {
        return sprintf(
            "Utilisateur { id: %d, email: %s, nom: %s, prenom: %s, date_naissance: %s, mot_de_passe: [PROTÉGÉ], etat: %s, nb_tentative: %d, sexe: %s }",
            $this->id_utilisateurs,
            $this->email,
            $this->nom,
            $this->prenom,
            $this->date_naissance,
            $this->etat ? 'true' : 'false',
            $this->nb_tentative,
            $this->sexe ? $this->sexe->sexe : 'Non spécifié'
        );
    }
}
