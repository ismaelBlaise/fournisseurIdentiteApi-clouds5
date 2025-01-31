<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokensTable extends Migration
{
     
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id('id_token');  
            $table->string('token');  
            $table->dateTime('date_expiration');  
            $table->foreignId('id_utilisateurs')->constrained('utilisateurs');  
        });
    }

 
    public function down()
    {
        Schema::dropIfExists('tokens');
    }
}
