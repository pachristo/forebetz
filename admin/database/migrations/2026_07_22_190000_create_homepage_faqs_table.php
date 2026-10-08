<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('homepage_faqs')) {
            return;
        }

        Schema::create('homepage_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('homepage_faqs')->insert([
            [
                'question' => 'Which is the only site that predict football matches correctly?',
                'answer' => 'No prediction site can guarantee 100% accuracy. Use tips as a tool to inform betting decisions rather than relying on them alone.',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Where can I get 100 accurate football predictions?',
                'answer' => 'WinningPredict publishes free tips daily and VIP tips sent ahead of kickoff. Check the free predictions table above or join VIP.',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Where can I get 90 accurate football predictions?',
                'answer' => 'Our free football tips and banker of the day categories publish high-confidence selections every day with probability percentages and odds.',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Who are the Top 5 Prediction Site?',
                'answer' => 'Among top prediction sites, WinningPredict is built for accuracy, education, and consistent daily tips.',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_faqs');
    }
};
