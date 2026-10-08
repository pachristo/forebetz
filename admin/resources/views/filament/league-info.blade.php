@props(['league'])

<div class="flex items-center">
    @if ($league->league_logo)
        <img src="{{ preg_match('#^https?://#', $league->league_logo) ? $league->league_logo : asset('storage/' . ltrim($league->league_logo, '/')) }}"
            alt="League Logo" class="w-10 h-10 object-cover mr-3 rounded">
    @endif
    <div>
        <strong>{{ $league->league_name ?? ($league->name ?? 'Unknown League') }}</strong><br>
        <small
            class="text-gray-500">{{ $league->country->country_name ?? ($league->country->name ?? 'Unknown Country') }}</small>
    </div>
</div>
