<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spmb_sekolah_asals', function (Blueprint $table) {
            $table->foreignId('master_smp_id')
                ->nullable()
                ->after('id')
                ->constrained('spmb_master_smps')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('spmb_sekolah_asals', function (Blueprint $table) {
            $table->dropForeign(['master_smp_id']);
            $table->dropColumn('master_smp_id');
        });
    }
};
