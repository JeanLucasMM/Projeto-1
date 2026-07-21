@if($legendary = collect($npc->sections)->firstWhere('title', 'Legendary Actions'))

<section class="section">

    <h2 class="section-title">
        Legendary Actions
    </h2>

    @foreach($legendary->items as $item)

        <p class="feature">

            <span class="feature-title">
                {{ $item->title }}
            </span>

            {!! $item->text !!}

        </p>

    @endforeach

</section>

@endif