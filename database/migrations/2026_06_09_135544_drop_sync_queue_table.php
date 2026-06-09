<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('sync_queue');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_local_id');
            $table->string('operation');
            $table->longText('payload');
            $table->boolean('synced')->default(0);
            $table->text('sync_error')->nullable();
            $table->timestamps();
        });
    }
};
