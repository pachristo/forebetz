<?php

namespace App\Filament\Widgets;

use App\Models\Blog;
use Filament\Widgets\Widget;
// use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
class BlogManagementWidget extends Widget
{
      use HasWidgetShield; // <-- Add this line

    protected static ?string $heading = 'Blog Management';
    protected static ?int $sort = 2;
    protected static string $view = 'filament.widgets.blog-management-widget';

    // UI state
    public $category = null;
    public $recentLimit = 5;

    public function deleteByStatus(string $status)
    {
        try {
            $normalized = strtolower($status);
            $query = Blog::query()->whereRaw('LOWER(status) = ?', [$normalized]);
            $count = $query->count();
            if ($count === 0) {
                \Filament\Notifications\Notification::make()->title('No posts found')->warning()->send();
                return;
            }

            $query->delete();

            \Filament\Notifications\Notification::make()->title("Deleted {$count} {$status} posts")->success()->send();
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()->title('Error deleting posts')->danger()->body($e->getMessage())->send();
        }
    }

    public function deleteRecent()
    {
        try {
            $limit = (int) $this->recentLimit ?: 5;
            $posts = Blog::query()->latest()->limit($limit)->get();
            $count = $posts->count();
            if ($count === 0) {
                \Filament\Notifications\Notification::make()->title('No recent posts found')->warning()->send();
                return;
            }

            $ids = $posts->pluck('id')->toArray();
            Blog::whereIn('id', $ids)->delete();

            \Filament\Notifications\Notification::make()->title("Deleted {$count} recent posts")->success()->send();
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()->title('Error deleting recent posts')->danger()->body($e->getMessage())->send();
        }
    }

    public function deleteByCategory()
    {
        try {
            $cat = trim((string) $this->category);
            if ($cat === '') {
                \Filament\Notifications\Notification::make()->title('Please provide a category')->warning()->send();
                return;
            }

            $query = Blog::query()->where('category', $cat);
            $count = $query->count();
            if ($count === 0) {
                \Filament\Notifications\Notification::make()->title('No posts found for that category')->warning()->send();
                return;
            }

            $query->delete();
            \Filament\Notifications\Notification::make()->title("Deleted {$count} posts in category: {$cat}")->success()->send();
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()->title('Error deleting posts by category')->danger()->body($e->getMessage())->send();
        }
    }

    public function deleteAll()
    {
        try {
            $count = Blog::count();
            if ($count === 0) {
                \Filament\Notifications\Notification::make()->title('No posts to delete')->warning()->send();
                return;
            }
            Blog::truncate();
            \Filament\Notifications\Notification::make()->title("Deleted all posts ({$count})")->success()->send();
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()->title('Error truncating posts')->danger()->body($e->getMessage())->send();
        }
    }

    public function getStats(): array
    {
        return [
            'total' => Blog::count(),
            'published' => Blog::whereRaw('LOWER(status) = ?', ['publish'])->count(),
            'draft' => Blog::whereRaw('LOWER(status) = ?', ['draft'])->count(),
            'archived' => Blog::whereRaw('LOWER(status) = ?', ['archived'])->count(),
        ];
    }
}
