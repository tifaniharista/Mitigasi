<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_closed')->default(false)->after('extension_days');
            $table->unsignedBigInteger('closed_by')->nullable()->after('is_closed');
            $table->timestamp('closed_at')->nullable()->after('closed_by');
            $table->text('closure_reason')->nullable()->after('closed_at');

            // Foreign key constraint
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['is_closed', 'closed_by', 'closed_at', 'closure_reason']);
        });
    }
};
