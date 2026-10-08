<div>
    <x-home::widget title="Countries">
        <ul class="countries-scroll max-h-[210px] divide-y divide-[#f0f0f0] overflow-y-auto">
            @foreach ($countries as $country)
                <x-home::link-row :href="$country['href']" :image="$country['image']" :label="$country['label']" image-class="rounded-sm" />
            @endforeach
        </ul>
    </x-home::widget>
</div>
