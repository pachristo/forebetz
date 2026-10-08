<?php

return [
    // Free predictions shown per "page" of the homepage feed (Load more adds this many).
    'predictions_per_page' => 10,

    // Perks listed under every package's own benefits.
    'package_perks' => ['24/7 Support', 'Access to Risk Management Guide.'],

    // Display order (API-Football league ids) for featured leagues in the Top Leagues widget.
    'top_league_order' => [2, 39, 140, 135, 78, 61, 3, 88, 848, 144, 71],

    // League tabs (label => API-Football league id) used by the standings and top scorers widgets.
    'league_tabs' => ['ENG' => 39, 'SPA' => 140, 'GER' => 78, 'ITA' => 135, 'FRA' => 61],

    // Rows shown in the standings and top scorers widgets.
    'league_table_rows' => 20,
    'top_scorer_rows' => 15,
];
