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
        Schema::create('wbeng_state', static function (Blueprint $table) {
            $table->uuid()->primary();
            $table->uuid('session_uuid')->index();
            $table->string('base_uri')->index();
            $table->string('endpoint', 32)->index();
            $table->json('query');
            $table->json('result');
            $table->json('attrs')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wbeng_state');
    }
};
