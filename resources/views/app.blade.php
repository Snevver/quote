<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="description" content="quote">
	<meta name="theme-color" content="#ffffff">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-title" content="snev">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
	<link rel="icon" type="image/webp" href="/images/logo.webp">
	<link rel="apple-touch-icon" href="/images/logo.webp">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@vite(['resources/js/app.js', 'resources/css/app.css'])
	<title>{{ $title ?? 'Home' }}</title>
</head>

<body>
	<div id="app" data-page="{{ $page }}" data-auth="{{ Auth::check() ? 'true' : 'false' }}" data-props="{{ json_encode($props ?? []) }}"></div>
</body>

</html>