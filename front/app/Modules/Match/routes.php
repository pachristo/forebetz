<?php

use App\Models\Fixture;
use App\Modules\Match\Livewire\MatchPage;
use App\Support\MatchUrl;
use Illuminate\Support\Facades\Route;

Route::livewire('/{matchId}/{slug}', MatchPage::class)
    ->where(['matchId' => '[0-9]+', 'slug' => '[A-Za-z0-9\-_]+'])
    ->name('match');

Route::get('/match', function () {
    $fixture = Fixture::query()
        ->where('match_id', (int) request()->query('id', 0))
        ->first(['match_id', 'home_name', 'away_name']);

    abort_unless($fixture, 404);

    return redirect(MatchUrl::make($fixture->match_id, $fixture->home_name, $fixture->away_name), 301);
})->name('match.legacy');
