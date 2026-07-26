@if($legendary = collect($npc->sections ?? [])->firstWhere('title', 'Legendary Actions'))

<section class="section">

    <h2 class="section-title">
        Ações Lendárias
    </h2>

    @foreach($legendary->items ?? [] as $item)

        <div class="feature">

            @if(!empty($item->title))
                <strong class="feature-title">
                    {{ rtrim($item->title, ' .:') }}.
                </strong>
            @endif

            {!! $item->text !!}

        </div>

    @endforeach

</section>

@endif