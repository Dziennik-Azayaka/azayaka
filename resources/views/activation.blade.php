@extends("layout")

@section("content")
	<h1>Account activation</h1>
	<form action="/api/activation/lookup" method="post">
		<h2>Lookup</h2>
		@csrf
		<label for="code">Code</label>
		<input type="text" name="code" id="code">
		<button type="submit">Look up</button>
	</form>

	<form action="/api/activation/emailAvailability" method="post">
		<h2>Email Availability</h2>
		@csrf
		<label for="email">Email</label>
		<input type="email" name="email" id="email">
		<button type="submit">Check</button>
	</form>

	<form action="/api/activation" method="post">
		<h2>Activation</h2>
		@csrf
		<label for="code">Code</label>
		<input type="text" name="code" id="code">
		<label for="email">Email</label>
		<input type="email" name="email" id="email">
		<label for="password">Password</label>
		<input type="password" name="password" id="password">
		<button type="submit">Activate</button>
	</form>
@endsection
