<?php

function currentUser()
{
    return \Illuminate\Support\Facades\Auth::user();
}


function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);
    curl_close ($ch);
    return $return;
}

function img($img, $path=null){
    if($img == ""){
        if ($path=='products/') return asset('images/no_image_placeholder.jpg');
        return asset('images/avatar.png');
    }else{
        return asset('images/'.$path.$img);
    }
}

function photo($img){
    if($img == ""){
        return asset('images/avatar.png');
    }else{
        return $img;
    }
}


function seoPages(): array
{
    return [
        'static' => [
            'home' => 'Home Page',
            'about' => 'About Us Page',
            'blog' => 'Blog Page',
        ],
        'tips' => [
            'solo-pick' => 'Solo Pick Page',
            'all-predictions' => 'All Predictions',
            'banker-tips' => 'Banker Tips Page',
            'double-chance' => 'Double Chance Page',
            '1-5-goals' => '1.5 Goals Page',
            '2-5-goals' => '2.5 Goals Page',
            'both-teams-score' => 'Both Teams Score Page',
            'draws' => 'Draws Page',
            'home-win' => 'Home Win Page',
            'away-win' => 'Away Win Page',
        ]
    ];
}

function storeKeyMap(): array
{
    return [
        'prob_HW' => '1',
        'prob_D' => 'X',
        'prob_AW' => '2',
        'prob_HW_D' => '1X',
        'prob_AW_D' => 'X2',
        'prob_HW_AW' => '12',
        'prob_O_1' => 'Over 1.5',
        'prob_U_1' => 'Under 1.5',
        'prob_O' => 'Over 2.5',
        'prob_U' => 'Under 2.5',
        'prob_O_3' => 'Over 3.5',
        'prob_U_3' => 'Under 3.5',
        'prob_bts' => 'BTS',
    ];
}

function oddKeyMap(): array
{
    return [
        '1' => 'odd_1',
        'X' => 'odd_x',
        '2' => 'odd_2',
        '1X' => 'odd_1x',
        'X2' => 'odd_x2',
        '12' => 'odd_12',
        'Over 1.5' => 'o+1.5',
        'Under 1.5' => 'u+1.5',
        'Over 2.5' => 'o+2.5',
        'Under 2.5' => 'u+2.5',
        'Over 3.5' => 'o+3.5',
        'Under 3.5' => 'u+3.5',
        'BTS' => 'bts_yes',
    ];
}

function tipCategories(): array
{
    return [
        'free_pick' => 'Free Tips',
        'solo_pick' => 'Solo Pick',
        'banker_tips' => 'Banker Tips',
        'double_chance' => 'Double Chance',
        '1_5_goals' => '1.5 Goals',
        '2_5_goals' => '2.5 Goals',
        'both_teams_score' => 'Both Teams Score',
        'draws' => 'Draws',
        'home_win' => 'Home Win',
        'away_win' => 'Away Win',
    ];
}

function marketOddKeys(): array
{
    return [
        'home_win' => 'Match Winner',
        'away_win' => 'Match Winner',
        'draws' => 'Match Winner',
        '1_5_goals' => 'Goals Over/Under',
        '2_5_goals' => 'Goals Over/Under',
        'both_teams_score' => 'Both Teams Score',
        'double_chance' => 'Double Chance',
    ];
}
