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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Customer
            $table->string('title'); // Short subject/summary
            $table->enum('type', ['sales', 'support']); // Enquiry type
            $table->string('project')->nullable(); // Project/Product
            $table->text('description'); // Detailed explanation
            $table->enum('status', ['open', 'assigned', 'in_progress', 'resolved'])->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users'); // Developer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
