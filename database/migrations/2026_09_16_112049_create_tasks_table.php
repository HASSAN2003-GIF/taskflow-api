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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        
        // Links the task to its specific column
        $table->foreignId('task_list_id')->constrained()->cascadeOnDelete();
        
        // Denormalization: Links the task directly to the tenant for security/speed
        $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
        
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('position')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
