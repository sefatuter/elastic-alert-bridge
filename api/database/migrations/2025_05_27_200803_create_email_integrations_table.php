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
        Schema::create('email_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('smtp_host');
            $table->integer('smtp_port');
            $table->boolean('smtp_ssl')->default(true);
            $table->string('smtp_username');
            $table->string('smtp_password');
            $table->string('from_address');
            $table->string('default_recipient')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_integrations');
    }
};
