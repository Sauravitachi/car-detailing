<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'GarageFlow' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased">
    <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-slate-800 bg-slate-900/90 px-4 backdrop-blur">
        <div class="flex items-center gap-2">
            <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-400"></span>
            <span class="text-sm font-bold tracking-tight text-slate-200">GarageFlow</span>
        </div>
        <span class="rounded bg-slate-800 px-2 py-1 font-mono text-xs text-sky-400">INTAKE</span>
    </header>
    <main class="mx-auto w-full max-w-md flex-1 px-4 pb-24 pt-5">
        {{ $slot }}
    </main>
</body>
</html>
