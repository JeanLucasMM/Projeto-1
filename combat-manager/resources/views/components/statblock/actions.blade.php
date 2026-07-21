@if($actions = collect($npc->sections)->firstWhere('title', 'Actions'))

<section class="section">

    <h2 class="section-title">
        Ação
    </h2>

    @foreach($actions->items as $item)

        <p class="feature">

            <span class="feature-title">

                {{ $item->title }}

            </span>

            {!! $item->text !!}

        </p>

    @endforeach

</section>

@endif
