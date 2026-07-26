@if($reactions = collect($npc->sections ?? [])->firstWhere('title', 'Reactions'))

<section class="section">

    <h2 class="section-title">
        Reações
    </h2>

    @foreach($reactions->items ?? [] as $item)

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