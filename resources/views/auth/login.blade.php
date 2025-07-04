<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
@if ($errors->any())
    <div>{{ implode(', ', $errors->all()) }}</div>
@endif
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label>Email: <input type="email" name="email"></label>
    <label>Password: <input type="password" name="password"></label>
    <button type="submit">Login</button>
</form>
<a href="{{ route('register') }}">Register</a>
</body>
</html>
