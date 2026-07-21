@if($traits = collect($npc->sections)->firstWhere('title', 'Traits'))

<section class="section">

    <h2 class="section-title">
        Habilidades
    </h2>

    @foreach($traits->items as $item)

        <p class="feature">

            <span class="feature-title">

                {{ $item->title }}.

            </span>

            {!! $item->text !!}

        </p>

    @endforeach

</section>

@endif