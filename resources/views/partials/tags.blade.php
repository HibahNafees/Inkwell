@if ($tags->isNotEmpty())
    <ul class="tags">
        @foreach ($tags as $tag)
            <li><a class="tag" href="{{ route('tags.show', $tag) }}">#{{ $tag->name }}</a></li>
        @endforeach
    </ul>
@endif
