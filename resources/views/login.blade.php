@extends("layout")

@section("content")
    @if($errors->any())
        <h2>{{$errors->first()}}</h2>
    @endif
    <form action="/login" method="post">
        @csrf
        <label for="Email">Email</label>
        <input type="email" name="email" id="email">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <button type="submit">Log in</button>
    </form>
@endsection
