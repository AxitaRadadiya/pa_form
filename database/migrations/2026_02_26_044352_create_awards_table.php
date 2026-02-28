<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->text('section3_description')->nullable();

            // Member info for this award
            $table->string('surname')->nullable();           // ← ADDED  (award_member_surname[])
            $table->string('first_name')->nullable();        // maps to award_member_name[]
            $table->string('gender')->nullable();            // male | female

            // Award details
            $table->string('department')->nullable();        // ← ADDED  (award_department[])
            $table->string('award_category')->nullable();    // ← ADDED  (award_category[])
            $table->string('award_type')->nullable();        // certificate | award

            // Extras
            $table->string('photo_attached')->nullable();
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->decimal('amount', 10, 2)->nullable();    // certificate=400, award=600
            $table->text('special_comment')->nullable();     // ← ADDED  (award_special_comment[])

            // REMOVED columns (no longer used):
            //   award_name, last_name, relation_id, amount_section3

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};