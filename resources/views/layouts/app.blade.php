<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Workshop OS' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-slate-100 antialiased font-sans flex flex-col">
    <!-- Top Bar -->
    <header class="h-14 border-b border-slate-800 bg-slate-900/80 backdrop-blur px-4 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-bold tracking-tight text-sm text-slate-200">GarageFlow</span>
        </div>
        <span class="text-xs font-mono bg-slate-800 text-sky-400 px-2 py-1 rounded">Bay #01</span>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 overflow-y-auto p-4 max-w-md w-full mx-auto pb-24">
        {{ $slot }}
    </main>
</body>
</html>