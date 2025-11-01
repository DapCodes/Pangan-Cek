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
        Schema::create('dearth_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained()->onDelete('cascade');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('kabupaten', 100);
            $table->string('kecamatan', 100)->nullable();
            $table->enum('severity', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])
                  ->default('MEDIUM')
                  ->comment('LOW: Sedikit langka, MEDIUM: Cukup langka, HIGH: Sangat langka, CRITICAL: Tidak tersedia');
            $table->text('description')->nullable();
            $table->enum('source', ['USER', 'ENUMERATOR', 'OFFICIAL'])->default('USER');
            $table->timestamp('reported_at')->useCurrent();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('APPROVED');
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['commodity_id', 'reported_at'], 'idx_dr_commodity_date');
            $table->index(['kabupaten', 'reported_at'], 'idx_dr_kabupaten_date');
            $table->index(['lat', 'lng'], 'idx_dr_lat_lng');
            $table->index('severity', 'idx_dr_severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dearth_reports');
    }
};