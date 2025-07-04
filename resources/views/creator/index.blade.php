<h1>Creator dashboard</h1>
<ul>
@foreach($enterprises as $enterprise)
    <li>{{ $enterprise->name }}</li>
@endforeach
</ul>
<a href="{{ route('creator.create') }}">Add enterprise</a>
