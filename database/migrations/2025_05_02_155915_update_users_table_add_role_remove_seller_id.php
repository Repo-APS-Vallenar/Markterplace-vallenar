<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Elimina primero la foreign key
            $table->dropForeign(['seller_id']);

            // Luego elimina la columna
            $table->dropColumn('seller_id');

            // Agrega el campo 'role'
            $table->string('role')->default('buyer')->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Vuelve a agregar la columna
            $table->unsignedBigInteger('seller_id')->nullable();

            // Recupera la foreign key si lo deseas (ajusta la tabla referenciada si es necesario)
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('set null');

            // Elimina el campo 'role'
            $table->dropColumn('role');
        });
    }
};
