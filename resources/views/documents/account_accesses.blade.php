<!doctype html>
<html lang="pl">
<head>
	<!-- both charset tags are recommended for dompdf -->
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Wydruk - instrukcja aktywacji dostępu</title>
	<style>
		body {
			font-family: Geist, sans-serif;
			line-height: 1;
		}

		.access {
			margin-top: 2rem;
		}

		header {
			font-weight: bold;
		}

		.employee-role {
			background-color: #000;
			color: #fff;
			border-radius: 3px;
			padding: .25rem .5rem;
			margin-right: .5rem;
		}

		h1 {
			margin: 0;
		}

		p {
			margin: .5rem 0;
		}

		.list_rows ol {
			display: inline-block;
			padding-left: 1.5rem;
			font-weight: bold;
			margin-bottom: 0;
			margin-top: 2rem;
		}

		.list_even {
			margin-left: 5rem;
		}

		.access {
			border-bottom: 1px dotted #000;
		}

		.start_without_margin {
			margin-top: 0;
		}

		.new_page {
			page-break-after: always;
		}

		@font-face {
			font-family: "Geist";
			font-style: normal;
			font-weight: 400;
			src: url("geist-v4-latin-ext-regular.ttf") format("truetype");
		}

		@font-face {
			font-family: "Geist";
			font-style: normal;
			font-weight: 700;
			src: url("geist-v4-latin-ext-700.ttf") format("truetype");
		}
	</style>
</head>
<body>
@if(count($accesses) > 0)
	@for($i = 0; $i < count($accesses); $i++)
		@php
			$access = $accesses[$i];
		@endphp
		<div class="access @if($i % 3 == 0) start_without_margin @endif @if($i % 3 == 2) new_page @endif">
			<header>
				<span class="employee-role">{{ $access->getAccessTypeName() }}</span>
				<span>{{ $access->name }}</span>
			</header>
			<main>
				<h1>Dostęp do dziennika elektronicznego</h1>
				<p>Aby aktywować Twój dostęp do dziennika elektronicznego, wejdź na stronę
					<b>{{ route("activateAccess") }}</b> i postępuj zgodnie z instrukcjami.</p>
				<p>Twój kod aktywacji, który będzie Ci potrzebny do aktywacji dostępu do dziennika:</p>
				<div class="list_rows">
					<ol>
						@for($j = 0; $j < count($access->code); $j+=2)
							<li value="{{ $j + 1 }}">{{ $access->code[$j] }}</li>
						@endfor
					</ol>
					<ol class="list_even">
						@for($j = 1; $j < count($access->code); $j+=2)
							<li value="{{ $j + 1 }}">{{ $access->code[$j] }}</li>
						@endfor
					</ol>
				</div>
			</main>
		</div>
	@endfor
@else
    <h1>Nie wybrano żadnych dostępów do wydrukowania.</h1>
@endif
</body>
</html>
