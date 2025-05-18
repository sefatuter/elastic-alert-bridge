<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('rule_id')->nullable(); // ElastAlert rule ID
            $table->string('level'); // info, warning, critical
            $table->string('host')->nullable(); // system hostname
            $table->text('summary')->nullable(); // short message
            $table->json('raw_payload'); // original alert body
            $table->boolean('acknowledged')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alerts');
    }
};
