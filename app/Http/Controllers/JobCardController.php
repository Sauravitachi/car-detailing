<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobCardController extends Controller
{
    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $validated = $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'vehicle_number' => 'required|string|max:20',
            'customer_name' => 'required|string|max:200',
            'customer_phone'  => 'required|string|max:15',
            'notes'           => 'nullable|string',
            'photos'          => 'required|array|min:1',
            'photos.*'        => 'image|max:8192',
            'tags'            => 'required|array',
        ]);
        
    

        $jobCard = DB::transaction(function () use ($validated, $request) {
            $cleanVehicle = strtoupper(preg_replace('/\s+/', '', $validated['vehicle_number']));

            $job = JobCard::create([
                'workshop_id'    => $validated['workshop_id'],
                'vehicle_number' => $cleanVehicle,
                'customer_name'  => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'notes'          => $validated['notes'] ?? null,
            ]);

            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store("workshops/{$validated['workshop_id']}/jobs/{$job->id}", 'public');
                $job->media()->create([
                    'id'         => (string) Str::uuid(),
                    'media_path' => $path,
                    'tag'        => $validated['tags'][$index] ?? 'GENERAL',
                ]);
            }

            return $job;
        });

        // WhatsApp notification trigger
        $whatsapp->sendCheckinLink(
            $jobCard->customer_phone,
            $jobCard->customer_name,
            $jobCard->vehicle_number,
            $jobCard->tracking_token
        );

        return response()->json([
            'status' => 'success',
            'tracking_url' => url("/track/{$jobCard->tracking_token}")
        ], 201);
    }

    public function track(string $token) {
        $job = JobCard::with(['workshop', 'media'])
            ->where('tracking_token', $token)
            ->firstOrFail();

        return view('track', compact('job'));
    }
}
