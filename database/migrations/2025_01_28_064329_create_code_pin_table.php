<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodePinTable extends Migration
{
    
    public function up()
    {
        Schema::create('code_pin', function (Blueprint $table) {
            $table->id('id_code_pin');  
            $table->integer('codepin')->unique()->nullable(false); 
            $table->dateTime('date_expiration')->nullable(false);  
            $table->unsignedBigInteger('id_utilisateurs'); 

             
            $table->foreign('id_utilisateurs')
                ->references('id')
                ->on('utilisateurs')
                ->onDelete('cascade');
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('code_pin');
    }
}
