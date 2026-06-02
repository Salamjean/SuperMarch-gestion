<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('position')->nullable()->after('phone');       // Poste / fonction
            $table->string('department')->nullable()->after('position');  // Département
            $table->date('hire_date')->nullable()->after('department');   // Date d'embauche
            $table->string('address')->nullable()->after('hire_date');    // Adresse
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'position', 'department', 'hire_date', 'address', 'gender']);
        });
    }
};
