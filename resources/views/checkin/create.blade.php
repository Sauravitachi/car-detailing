<x-app-layout>
    <div x-data="checkinForm()" class="space-y-5">
        <div>
            <h2 class="text-lg font-bold text-slate-100">New Vehicle Intake</h2>
            <p class="text-xs text-slate-400">Capture mandatory details & scratch photos</p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Step 1: Vehicle & Customer Info -->
            <div class="bg-slate-900 p-4 rounded-xl border border-slate-800 space-y-3">
                <div>
                    <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Plate Number</label>
                    <input type="text" x-model="formData.vehicle_number" placeholder="DL01AB1234" 
                           class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2.5 text-base uppercase font-mono tracking-wider focus:outline-none focus:border-sky-500 text-slate-100" required>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Customer Name</label>
                        <input type="text" x-model="formData.customer_name" placeholder="Rahul Sharma" 
                               class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-sky-500 text-slate-100" required>
                    </div>
                    <div>
                        <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">WhatsApp No</label>
                        <input type="tel" x-model="formData.customer_phone" placeholder="9876543210" 
                               class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-sky-500 text-slate-100" required>
                    </div>
                </div>
            </div>

            <!-- Step 2: Inspection Photo Grid with Native Camera Hook -->
            <div class="bg-slate-900 p-4 rounded-xl border border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Condition Photos (<span x-text="photos.length"></span>)</label>
                    <span class="text-[10px] text-amber-400">Min. 1 photo required</span>
                </div>

                <!-- Hidden Native Camera Inputs -->
                <input type="file" x-ref="cameraInput" @change="handleFileUpload($event)" accept="image/*" capture="environment" class="hidden">

                <!-- Photo Tiles Grid -->
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="(photo, index) in photos" :key="index">
                        <div class="relative aspect-square rounded-lg overflow-hidden border border-slate-700 bg-slate-950">
                            <img :src="photo.preview" class="w-full h-full object-cover">
                            <span class="absolute bottom-1 left-1 bg-slate-900/90 text-[9px] font-semibold px-1 rounded text-slate-300" x-text="photo.tag"></span>
                            <button type="button" @click="removePhoto(index)" class="absolute top-1 right-1 bg-rose-600/80 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs">×</button>
                        </div>
                    </template>

                    <!-- Add Button -->
                    <button type="button" @click="triggerCapture('SCRATCH')" 
                            class="aspect-square border-2 border-dashed border-slate-800 hover:border-slate-700 rounded-lg flex flex-col items-center justify-center gap-1 text-slate-500 hover:text-slate-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-[10px] font-medium">Capture</span>
                    </button>
                </div>
            </div>

            <!-- Step 3: Remarks -->
            <div class="bg-slate-900 p-4 rounded-xl border border-slate-800">
                <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Damage / Service Remarks</label>
                <textarea x-model="formData.notes" rows="2" placeholder="e.g. Deep scratch on left rear bumper, clean engine bay requested..."
                          class="w-full mt-1 bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-sm focus:outline-none focus:border-sky-500 text-slate-100 resize-none"></textarea>
            </div>

            <!-- Sticky Bottom Action -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-slate-950/90 backdrop-blur border-t border-slate-800 max-w-md mx-auto z-20">
                <button type="submit" :disabled="loading || photos.length === 0"
                        class="w-full py-3 bg-sky-500 hover:bg-sky-400 disabled:opacity-50 text-slate-950 font-bold rounded-xl flex items-center justify-center gap-2 text-sm shadow-lg shadow-sky-500/10">
                    <span x-show="!loading">Send Check-in Link</span>
                    <span x-show="loading">Dispatching WhatsApp...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function checkinForm() {
            return {
                loading: false,
                currentTag: 'SCRATCH',
                formData: {
                    workshop_id: '{{ auth()->user()->workshop_id ?? "WS-DEMO-001" }}',
                    vehicle_number: '',
                    customer_name: '',
                    customer_phone: '',
                    notes: ''
                },
                photos: [],
                triggerCapture(tag) {
                    this.currentTag = tag;
                    this.$refs.cameraInput.click();
                },
                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.photos.push({
                        file: file,
                        tag: this.currentTag,
                        preview: URL.createObjectURL(file)
                    });
                    event.target.value = '';
                },
                removePhoto(index) {
                    this.photos.splice(index, 1);
                },
                async submitForm() {
                    this.loading = true;
                    const data = new FormData();
                    data.append('workshop_id', this.formData.workshop_id);
                    data.append('vehicle_number', this.formData.vehicle_number);
                    data.append('customer_name', this.formData.customer_name);
                    data.append('customer_phone', this.formData.customer_phone);
                    data.append('notes', this.formData.notes);

                    this.photos.forEach((item, idx) => {
                        data.append(`photos[${idx}]`, item.file);
                        data.append(`tags[${idx}]`, item.tag);
                    });

                    try {
                        const res = await fetch('/api/jobcards/checkin', {
                            method: 'POST',
                            body: data,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const result = await res.json();
                        if (result.status === 'success') {
                            alert('Job Card created & WhatsApp sent!');
                            window.location.reload();
                        }
                    } catch (e) {
                        alert('Submission failed. Check network.');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>