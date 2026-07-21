@if($reactions = collect($npc->sections)->firstWhere('title', 'Reactions'))

<section class="section">

    <h2 class="section-title">
        Reação
    </h2>

    @foreach($reactions->items as $item)

        <p class="feature">

            <span class="feature-title">

                {{ $item->title }}.

            </span>

            {!! $item->text !!}

        </p>

    @endforeach

</section>

@endif