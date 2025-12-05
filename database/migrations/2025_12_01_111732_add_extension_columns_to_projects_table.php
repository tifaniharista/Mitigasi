<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('actual_end_date')->nullable()->after('end_date');
            $table->text('extension_reason')->nullable()->after('actual_end_date');
            $table->integer('extension_days')->default(0)->after('extension_reason');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['actual_end_date', 'extension_reason', 'extension_days']);
        });
    }
};
