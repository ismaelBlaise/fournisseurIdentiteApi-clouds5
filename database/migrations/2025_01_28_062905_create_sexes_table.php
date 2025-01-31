<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSexesTable extends Migration
{
     
    public function up()
    {
        Schema::create('sexes', function (Blueprint $table) {
            $table->id('id_sexe');  
            $table->string('sexe');  
        });
    }

     
    public function down()
    {
        Schema::dropIfExists('sexes');
    }
}
