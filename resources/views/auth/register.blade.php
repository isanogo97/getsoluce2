<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register</title>
</head>
<body>
@if ($errors->any())
    <div>{{ implode(', ', $errors->all()) }}</div>
@endif
<form method="POST" action="{{ route('register') }}">
    @csrf
    <label>Name: <input type="text" name="name"></label>
    <label>Email: <input type="email" name="email"></label>
    <label>Password: <input type="password" name="password"></label>
    <label>Confirm: <input type="password" name="password_confirmation"></label>
    <button type="submit">Register</button>
</form>
</body>
</html>
