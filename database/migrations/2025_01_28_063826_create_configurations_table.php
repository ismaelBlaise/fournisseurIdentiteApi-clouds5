<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
     
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id('id_configuration');
            $table->string('cle', 50)->unique()->nullable(false);  
            $table->string('valeur', 50)->nullable(false);  
        });
    }

 
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
