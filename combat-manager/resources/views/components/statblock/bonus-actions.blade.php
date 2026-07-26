@if($bonusActions = collect($npc->sections ?? [])->firstWhere('title', 'Bonus Actions'))

<section class="section">

    <h2 class="section-title">
        Ações Bônus
    </h2>

    @foreach($bonusActions->items ?? [] as $item)

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