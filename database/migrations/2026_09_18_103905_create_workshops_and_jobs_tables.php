<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workshops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone')->unique();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('job_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->string('vehicle_number')->index();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->uuid('tracking_token')->unique();
            $table->enum('status', ['CHECKED_IN', 'IN_PROGRESS', 'READY_FOR_DELIVERY', 'DELIVERED'])->default('CHECKED_IN');
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_card_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_card_id')->constrained('job_cards')->cascadeOnDelete();
            $table->string('media_path');
            $table->string('tag'); // FRONT, REAR, SCRATCH_LEFT, etc.
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('job_card_media');
        Schema::dropIfExists('job_cards');
        Schema::dropIfExists('workshops');
    }
};