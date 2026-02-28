<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->string('surname')->nullable();           // ← ADDED (member_surname[])
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->date('dob')->nullable();                 // ← changed string → date (proper type)
            $table->unsignedTinyInteger('age')->nullable();  // ← changed string → tinyInteger
            $table->decimal('amount', 10, 2)->nullable();    // ← changed string → decimal (proper type)
            $table->foreignId('relation_id')->nullable()->constrained('relations')->nullOnDelete();
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->text('section_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};