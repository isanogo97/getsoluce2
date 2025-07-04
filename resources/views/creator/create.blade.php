<form method="POST" action="{{ route('creator.store') }}">
    @csrf
    <input type="text" name="name" placeholder="Enterprise name">
    <button type="submit">Create</button>
</form>
