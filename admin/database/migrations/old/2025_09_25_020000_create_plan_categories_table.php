<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('plan_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('benefits')->nullable();
            $table->timestamps();
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_category_id')->nullable()->after('id');
            $table->foreign('plan_category_id')->references('id')->on('plan_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['plan_category_id']);
            $table->dropColumn('plan_category_id');
        });

        Schema::dropIfExists('plan_categories');
    }
};
