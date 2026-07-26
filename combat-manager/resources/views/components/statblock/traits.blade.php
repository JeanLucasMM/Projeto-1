@if($traits = collect($npc->sections ?? [])->firstWhere('title', 'Traits'))

<section class="section">

    <h2 class="section-title">
        Habilidades
    </h2>

    @foreach($traits->items ?? [] as $item)

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