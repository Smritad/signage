<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_details', function (Blueprint $table) {
            $table->longText('other_information')->nullable()->after('faqs');
        });
    }

    public function down(): void
    {
        Schema::table('products_details', function (Blueprint $table) {
            $table->dropColumn('other_information');
        });
    }
};
