<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('likes', function (Blueprint $table) {
            // Xóa cột post_id
            $table->dropColumn('post_id');

            // Thêm cột likeable_id và likeable_type
            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
