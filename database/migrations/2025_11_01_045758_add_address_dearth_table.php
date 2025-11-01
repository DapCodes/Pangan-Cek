<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dearth_reports', function (Blueprint $table) {
            $table->foreignId('province_id')->nullable()->after('commodity_id')->constrained('provinces')->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->after('province_id')->constrained('regencies')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('regency_id')->constrained('districts')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->after('district_id')->constrained('villages')->nullOnDelete();
            
            $table->index('province_id');
            $table->index('regency_id');
            $table->index('district_id');
            $table->index('village_id');
        });
    }

    public function down()
    {
        Schema::table('dearth_reports', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['regency_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['village_id']);
            $table->dropColumn(['province_id', 'regency_id', 'district_id', 'village_id']);
        });
    }
};
