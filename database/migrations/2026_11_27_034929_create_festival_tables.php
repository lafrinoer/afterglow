<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_number')->unique();
            $table->string('title');
            $table->date('event_date');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('ticket_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_day_id')->constrained()->cascadeOnDelete();
            $table->string('zone_name');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quota');
            $table->unsignedInteger('sold_count')->default(0);
            $table->json('benefits');
            $table->timestamps();
            $table->unique(['festival_day_id', 'zone_name']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_tier_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedSmallInteger('unique_code');
            $table->unsignedBigInteger('total_amount');
            $table->string('payment_proof_path')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_tier_id')->constrained()->restrictOnDelete();
            $table->uuid('ticket_code')->unique();
            $table->text('qr_payload');
            $table->boolean('is_checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open')->index();
            $table->text('admin_reply')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('ticket_tiers');
        Schema::dropIfExists('festival_days');
    }
};
