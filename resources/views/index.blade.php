<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Dziennik Azayaka</title>

	<link rel="shortcut icon" href="{{ Vite::asset('resources/static/favicon.ico') }}" type="image/x-icon">

	<style>
		.splash-container {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: #fff;
		}

		.splash-logo {
			will-change: transform, opacity, filter;
			animation: logo-enter 1s cubic-bezier(0.16, 1, 0.3, 1) both;
			animation-delay: 400ms;
		}

		@keyframes logo-enter {
			0% {
				opacity: 0;
				transform: scale(0.85) translateY(15px);
			}

			100% {
				opacity: 1;
				transform: scale(1) translateY(0);
			}
		}
	</style>


	@vite('resources/js/main.ts')
</head>

<body>
	<div id="app">
		<div class="splash-container">
			<svg
				class="splash-logo"
				width="200"
				viewBox="0 0 203 191"
				fill="none"
				xmlns="http://www.w3.org/2000/svg"
				aria-label="Proszę czekać..."
			>
				<path
					d="M31.3594 57.0735C36.2836 43.0815 55.0691 40.7162 63.3076 53.051L72.667 67.0647L60.6904 102.277C57.1968 112.547 63.5146 123.552 74.1455 125.713L117.76 134.579L136.461 162.579C145.49 176.098 133.39 193.712 117.531 190.135L14.0469 166.793C3.54016 164.423 -2.5473 153.419 1.0283 143.259L31.3594 57.0735ZM91.3281 12.2073C96.115 -1.86494 114.93 -4.3988 123.27 7.90551L198.964 119.592C208.013 132.944 196.284 150.542 180.478 147.329L117.76 134.579L72.667 67.0647L91.3281 12.2073Z"
					fill="url(#paint0_linear_18_2)" />
				<defs>
					<linearGradient
						id="paint0_linear_18_2"
						x1="158.851"
						y1="63.5036"
						x2="7.52191"
						y2="159.114"
						gradientUnits="userSpaceOnUse">
						<stop stop-color="#2360FF" />
						<stop offset="1" stop-color="#001CB0" />
					</linearGradient>
				</defs>
			</svg>
		</div>
	</div>
</body>

</html>
