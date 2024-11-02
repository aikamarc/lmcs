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
        Schema::table('d_liste_skin_cs2', function (Blueprint $table) {
            $table->string("paint_seed")->nullable();
            $table->string("float_value")->nullable();
            $table->string("cs2_screenshot_id")->nullable();
            $table->string("rarity_name")->nullable();
            $table->string("icon_url")->nullable();
            $table->timestamp("last_update")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('d_liste_skin_cs2', function (Blueprint $table) {
            $table->dropColumn("paint_seed");
            $table->dropColumn("float_value");
            $table->dropColumn("cs2_screenshot_id");
            $table->dropColumn("rarity_name");
            $table->dropColumn("icon_url");
            $table->dropColumn("last_update");
        });
    }
};
