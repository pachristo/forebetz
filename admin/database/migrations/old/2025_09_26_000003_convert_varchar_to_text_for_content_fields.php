<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * NOTE: Changing column types with the query builder requires the doctrine/dbal package.
     * Run: composer require doctrine/dbal
     *
     * This migration is defensive: it checks for the column existence and wraps each
     * change in a try/catch so it will not abort if a particular column/table is missing.
     */
    public function up(): void
    {
        // seo_pages: head1, head2, meta_keywords, meta_description -> text
        try {
            if (Schema::hasTable('seo_pages')) {
                Schema::table('seo_pages', function (Blueprint $table) {
                    if (Schema::hasColumn('seo_pages', 'head1')) {
                        $table->text('head1')->nullable()->change();
                    }
                    if (Schema::hasColumn('seo_pages', 'head2')) {
                        $table->text('head2')->nullable()->change();
                    }
                    if (Schema::hasColumn('seo_pages', 'meta_keywords')) {
                        $table->text('meta_keywords')->nullable()->change();
                    }
                    if (Schema::hasColumn('seo_pages', 'meta_description')) {
                        $table->text('meta_description')->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
            // ignore; user can install doctrine/dbal or run manual SQL if needed
        }

        // game_cats: head1, head2 -> text
        try {
            if (Schema::hasTable('game_cats')) {
                Schema::table('game_cats', function (Blueprint $table) {
                    if (Schema::hasColumn('game_cats', 'head1')) {
                        $table->text('head1')->nullable()->change();
                    }
                    if (Schema::hasColumn('game_cats', 'head2')) {
                        $table->text('head2')->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }

        // single_bet_texts: title -> text (title can be long in some cases)
        try {
            if (Schema::hasTable('single_bet_texts')) {
                Schema::table('single_bet_texts', function (Blueprint $table) {
                    if (Schema::hasColumn('single_bet_texts', 'title')) {
                        $table->text('title')->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }

        // blogs: content, meta_keywords, meta_description -> text
        try {
            if (Schema::hasTable('blogs')) {
                Schema::table('blogs', function (Blueprint $table) {
                    if (Schema::hasColumn('blogs', 'content')) {
                        $table->text('content')->nullable()->change();
                    }
                    if (Schema::hasColumn('blogs', 'meta_keywords')) {
                        $table->text('meta_keywords')->nullable()->change();
                    }
                    if (Schema::hasColumn('blogs', 'meta_description')) {
                        $table->text('meta_description')->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        // revert back to string(255) where possible
        try {
            if (Schema::hasTable('seo_pages')) {
                Schema::table('seo_pages', function (Blueprint $table) {
                    if (Schema::hasColumn('seo_pages', 'head1')) {
                        $table->string('head1', 255)->nullable()->change();
                    }
                    if (Schema::hasColumn('seo_pages', 'head2')) {
                        $table->string('head2', 255)->nullable()->change();
                    }
                    if (Schema::hasColumn('seo_pages', 'meta_keywords')) {
                        $table->string('meta_keywords', 255)->nullable()->change();
                    }
                    if (Schema::hasColumn('seo_pages', 'meta_description')) {
                        $table->string('meta_description', 255)->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('game_cats')) {
                Schema::table('game_cats', function (Blueprint $table) {
                    if (Schema::hasColumn('game_cats', 'head1')) {
                        $table->string('head1', 255)->nullable()->change();
                    }
                    if (Schema::hasColumn('game_cats', 'head2')) {
                        $table->string('head2', 255)->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('single_bet_texts')) {
                Schema::table('single_bet_texts', function (Blueprint $table) {
                    if (Schema::hasColumn('single_bet_texts', 'title')) {
                        $table->string('title', 255)->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }

        try {
            if (Schema::hasTable('blogs')) {
                Schema::table('blogs', function (Blueprint $table) {
                    if (Schema::hasColumn('blogs', 'content')) {
                        $table->string('content', 65535)->nullable()->change();
                    }
                    if (Schema::hasColumn('blogs', 'meta_keywords')) {
                        $table->string('meta_keywords', 255)->nullable()->change();
                    }
                    if (Schema::hasColumn('blogs', 'meta_description')) {
                        $table->string('meta_description', 255)->nullable()->change();
                    }
                });
            }
        } catch (\Throwable $e) {
        }
    }
};
