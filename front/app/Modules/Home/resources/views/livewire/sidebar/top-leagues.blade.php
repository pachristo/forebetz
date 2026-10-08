<div>
    <x-home::widget title="Top Leagues">
        <ul class="divide-y divide-[#f0f0f0]">
            @foreach ($leagues as $league)
                <x-home::link-row :href="$league['href']" :image="$league['image']" :label="$league['label']" />
            @endforeach
        </ul>
    </x-home::widget>
</div>
