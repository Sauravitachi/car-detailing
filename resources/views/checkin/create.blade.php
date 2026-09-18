<x-app-layout>
    <div x-data="checkinForm()" class="space-y-5">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-sky-400">{{ $workshop->name }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-white">New vehicle intake</h1>
            <p class="mt-1 text-sm text-slate-400">Capture condition proof before work begins.</p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
            <section class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <div>
                    <label for="vehicle_number" class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Registration number</label>
                    <input id="vehicle_number" type="text" x-model="formData.vehicle_number" placeholder="DL01AB1234" autocomplete="off" required
                        class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-3 font-mono text-base uppercase tracking-wider text-slate-100 outline-none focus:border-sky-500">
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="customer_name" class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Customer name</label>
                        <input id="customer_name" type="text" x-model="formData.customer_name" placeholder="Rahul Sharma" required
                            class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-3 text-base text-slate-100 outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label for="customer_phone" class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">WhatsApp number</label>
                        <input id="customer_phone" type="tel" x-model="formData.customer_phone" placeholder="9876543210" inputmode="tel" required
                            class="mt-1 w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-3 text-base text-slate-100 outline-none focus:border-sky-500">
                    </div>
                </div>
            </section>

            <section class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <div class="flex items-end justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold text-white">Condition photos</h2>
                        <p class="mt-1 text-xs text-slate-400">Take at least one photo. Add scratch tags where needed.</p>
                    </div>
                    <span class="shrink-0 text-xs font-medium text-amber-300"><span x-text="photos.length"></span>/12</span>
                </div>

                <input type="file" x-ref="cameraInput" @change="handleFileUpload($event)" accept="image/jpeg,image/png,image/webp" capture="environment" class="hidden">

                <div class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                    <template x-for="(photo, index) in photos" :key="photo.preview">
                        <div class="relative aspect-square overflow-hidden rounded-xl border border-slate-700 bg-slate-950">
                            <img :src="photo.preview" :alt="photo.tag + ' inspection photo'" class="h-full w-full object-cover">
                            <span class="absolute bottom-1 left-1 rounded bg-slate-950/85 px-1.5 py-0.5 text-[9px] font-semibold text-slate-200" x-text="photo.tag"></span>
                            <button type="button" @click="removePhoto(index)" :aria-label="'Remove ' + photo.tag + ' photo'" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-sm text-white">&times;</button>
                        </div>
                    </template>

                    <template x-for="tag in tags" :key="tag">
                        <button type="button" @click="triggerCapture(tag)" :disabled="photos.length >= 12"
                            class="flex aspect-square flex-col items-center justify-center gap-1 rounded-xl border border-dashed border-slate-700 text-slate-400 transition hover:border-sky-500 hover:text-sky-300 disabled:opacity-40">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 0 1 2-2h.93a2 2 0 0 0 1.664-.89l.812-1.22A2 2 0 0 1 10.07 4h3.86a2 2 0 0 1 1.664.89l.812 1.22A2 2 0 0 0 17.07 7H19a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            <span class="text-[10px] font-medium" x-text="tag"></span>
                        </button>
                    </template>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <label for="notes" class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Damage or service notes</label>
                <textarea id="notes" x-model="formData.notes" rows="3" maxlength="5000" placeholder="Deep scratch on left rear bumper..."
                    class="mt-1 w-full resize-none rounded-xl border border-slate-800 bg-slate-950 p-3 text-base text-slate-100 outline-none focus:border-sky-500"></textarea>
            </section>

            <p x-show="error" x-text="error" role="alert" class="rounded-xl border border-rose-900 bg-rose-950/50 p-3 text-sm text-rose-200"></p>

            <div class="h-16"></div>
            <div class="fixed bottom-0 left-0 right-0 z-20 border-t border-slate-800 bg-slate-950/95 p-4 backdrop-blur sm:absolute">
                <button type="submit" :disabled="loading || photos.length === 0"
                    class="mx-auto flex w-full max-w-md items-center justify-center rounded-xl bg-sky-400 py-3.5 text-sm font-bold text-slate-950 shadow-lg shadow-sky-500/10 transition hover:bg-sky-300 disabled:cursor-not-allowed disabled:opacity-50">
                    <span x-show="!loading">Create job card & send link</span>
                    <span x-show="loading">Saving inspection...</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function checkinForm() {
            return {
                loading: false,
                error: '',
                currentTag: 'GENERAL',
                tags: ['FRONT', 'REAR', 'LEFT', 'RIGHT', 'SCRATCH', 'GENERAL'],
                formData: { vehicle_number: '', customer_name: '', customer_phone: '', notes: '' },
                photos: [],
                triggerCapture(tag) {
                    this.currentTag = tag;
                    this.$refs.cameraInput.click();
                },
                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file || this.photos.length >= 12) return;
                    this.photos.push({ file, tag: this.currentTag, preview: URL.createObjectURL(file) });
                    event.target.value = '';
                },
                removePhoto(index) {
                    URL.revokeObjectURL(this.photos[index].preview);
                    this.photos.splice(index, 1);
                },
                async submitForm() {
                    this.loading = true;
                    this.error = '';
                    const data = new FormData();
                    Object.entries(this.formData).forEach(([key, value]) => data.append(key, value));
                    this.photos.forEach((item, index) => {
                        data.append(`photos[${index}]`, item.file);
                        data.append(`tags[${index}]`, item.tag);
                    });

                    try {
                        const response = await fetch('{{ route('jobcards.store', $workshop) }}', {
                            method: 'POST',
                            body: data,
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message || 'Please check the form and try again.');
                        window.location.href = result.tracking_url;
                    } catch (exception) {
                        this.error = exception.message || 'Submission failed. Check your connection.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
