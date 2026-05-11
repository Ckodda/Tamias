<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Events', function (Blueprint $table) {
            $table->id('Id');
            $table->foreignId('CostCenterId')->constrained('CostCenters', 'Id');
            $table->string('EventName');
            $table->decimal('TargetAmount', 15, 2);
            $table->enum('EventStatus', ['Active', 'Completed', 'Cancelled']);
            $table->date('StartDate');
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });

        Schema::create('Commitments', function (Blueprint $table) {
            $table->id('Id');
            $table->foreignId('UserId')->constrained('Users', 'Id');
            $table->foreignId('CostCenterId')->constrained('CostCenters', 'Id');
            $table->foreignId('EventId')->nullable()->constrained('Events', 'Id');
            $table->decimal('CommitmentAmount', 15, 2);
            $table->enum('FrequencyType', ['Monthly', 'OneTime']);
            $table->enum('CurrentStatus', ['Active', 'Fulfilled', 'Cancelled']);
            $table->timestamp('CreatedAt')->useCurrent();
            $table->timestamp('UpdatedAt')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Commitments');
        Schema::dropIfExists('Events');
    }
};
