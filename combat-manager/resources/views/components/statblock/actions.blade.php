@if($actions = collect($npc->sections ?? [])->firstWhere('title', 'Actions'))

<section class="section">

    <h2 class="section-title">
        Ações
    </h2>

    @foreach($actions->items ?? [] as $item)

        <div class="feature">

            @if(!empty($item->title))
                <strong class="feature-title">
                    {{ rtrim($item->title, '.') }}.
                </strong>
            @endif

            {!! $item->text !!}

        </div>

    @endforeach

</section>

@endif