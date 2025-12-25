<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dziennik Azayaka</title>
    </head>
    <body>
        <div id="app">
			<noscript>Ta aplikacja wymaga włączonej obsługi JavaScript.</noscript>
		</div>
        {{Vite::useHotFile(public_path('hot-authentication'))
                ->useBuildDirectory('build-authentication')
                ->useManifestFilename('.vite/manifest.json')
                ->withEntryPoints(['src/main.ts'])}}
    </body>
</html>
