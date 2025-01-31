<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilisateursTable extends Migration
{
    public function up()
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id('id_utilisateurs');   
            $table->string('email')->unique();   
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('mot_de_passe');
            $table->boolean('etat')->default(false); 
            $table->integer('nb_tentative')->default(0);  
            $table->foreignId('id_sexe')->constrained('sexes')->onDelete('set null');   
             
        });
    }

    public function down()
    {
        Schema::dropIfExists('utilisateurs');
    }
}
