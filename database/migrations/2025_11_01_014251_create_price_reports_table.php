<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('price_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 12, 2);
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('quantity_unit', 20)->nullable();
            $table->enum('source', ['USER', 'ENUMERATOR', 'OFFICIAL'])->default('USER');
            $table->timestamp('reported_at')->useCurrent();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('APPROVED');
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['commodity_id', 'reported_at'], 'idx_pr_commodity_date');
            $table->index(['lat', 'lng'], 'idx_pr_lat_lng');
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_reports');
    }
};