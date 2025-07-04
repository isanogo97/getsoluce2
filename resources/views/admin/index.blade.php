<h1>Admin dashboard</h1>
<ul>
@foreach($users as $user)
    <li>{{ $user->name }} ({{ $user->role }})</li>
@endforeach
</ul>
