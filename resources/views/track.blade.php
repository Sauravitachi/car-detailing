<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->vehicle_number }} | {{ $job->workshop->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="mx-auto max-w-lg px-4 py-6 sm:py-10">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-400">{{ $job->workshop->name }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight">Your vehicle status</h1>
            </div>
            <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">LIVE</span>
        </header>

        <section class="mb-5 rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <p class="text-xs uppercase tracking-wider text-slate-400">Registration</p>
            <p class="mt-1 font-mono text-xl font-bold tracking-widest text-white">{{ $job->vehicle_number }}</p>
            <p class="mt-3 text-sm text-slate-400">Hi {{ $job->customer_name }}, your job card is updated in real time.</p>
        </section>

        <section class="mb-5 rounded-2xl border border-slate-800 bg-slate-900 p-5">
            <h2 class="text-sm font-semibold text-white">Service progress</h2>
            <div class="mt-5 space-y-5">
                @php
                    $steps = [
                        'CHECKED_IN' => ['Checked in', 'Vehicle received and condition recorded.'],
                        'IN_PROGRESS' => ['Work in progress', 'Your technician is working on the vehicle.'],
                        'READY_FOR_DELIVERY' => ['Ready for delivery', 'Final quality check is complete.'],
                        'DELIVERED' => ['Delivered', 'Thank you for trusting the studio.'],
                    ];
                    $statusIndex = array_search($job->status, array_keys($steps), true);
                @endphp
                @foreach ($steps as $status => [$label, $description])
                    @php $stepIndex = array_search($status, array_keys($steps), true); @endphp
                    <div class="relative flex gap-3">
                        @if ($stepIndex < count($steps) - 1)
                            <span class="absolute left-[9px] top-5 h-8 w-px {{ $stepIndex < $statusIndex ? 'bg-sky-400' : 'bg-slate-700' }}"></span>
                        @endif
                        <span class="relative z-10 mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $stepIndex <= $statusIndex ? 'bg-sky-400 text-slate-950' : 'border border-slate-700 text-slate-600' }} text-[10px] font-bold">{{ $stepIndex <= $statusIndex ? '✓' : $stepIndex + 1 }}</span>
                        <div>
                            <p class="text-sm font-semibold {{ $stepIndex <= $statusIndex ? 'text-white' : 'text-slate-500' }}">{{ $label }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        @if ($job->media->isNotEmpty())
            <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Intake inspection</h2>
                    <span class="text-xs text-slate-400">{{ $job->media->count() }} photos</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($job->media as $photo)
                        <figure class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950">
                            <img src="{{ Storage::url($photo->media_path) }}" alt="{{ $photo->tag }} inspection photo" class="aspect-square w-full object-cover">
                            <figcaption class="px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $photo->tag }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($job->notes)
            <p class="mt-4 text-xs leading-5 text-slate-400"><span class="font-semibold text-slate-300">Notes:</span> {{ $job->notes }}</p>
        @endif
    </main>
</body>
</html>
