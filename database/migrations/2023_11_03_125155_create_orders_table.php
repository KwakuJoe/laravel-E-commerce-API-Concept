<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->text('order_id');
            $table->foreignId('user_id');
            $table->datetime('order_date');
            $table->string('location');
            $table->string('name');
            $table->string('phone');
            $table->string('alternate_phone');
            $table->decimal('total_amount');
            $table->text('additional_information');
            $table->string('status')->default(OrderStatus::PROCESSING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
