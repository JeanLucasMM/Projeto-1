@if(!empty($section->items))

<section class="section">

    <h2 class="section-title">
        {{ $section->title }}
    </h2>

    @foreach($section->items as $item)

        <p class="feature">

            <span class="feature-title">

                {{ $item->title }}

            </span>

            {!! $item->text !!}

        </p>

    @endforeach

</section>

@endif