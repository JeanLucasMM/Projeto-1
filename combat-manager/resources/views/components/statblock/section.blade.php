@if(!empty($section) && !empty($section->items))

<section class="section">

    <h2 class="section-title">
        {{ $section->title }}
    </h2>

    @foreach($section->items as $item)

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