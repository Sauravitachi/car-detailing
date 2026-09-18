<?php

namespace App\Http\Controllers;

use App\Models\JobCard;
use App\Models\Workshop;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobCardController extends Controller
{
    public function create(Workshop $workshop)
    {
        return view('checkin.create', compact('workshop'));
    }

    public function store(Request $request, Workshop $workshop, WhatsAppService $whatsapp)
    {
        $validated = $request->validate([
            'vehicle_number' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9 -]+$/'],
            'customer_name' => ['required', 'string', 'max:200'],
            'customer_phone' => ['required', 'regex:/^(?:\+?91[- ]?)?[6-9]\d{9}$/'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'photos' => ['required', 'array', 'min:1', 'max:12'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'tags' => ['required', 'array', 'min:1', 'max:12'],
            'tags.*' => ['required', 'string', Rule::in(['FRONT', 'REAR', 'LEFT', 'RIGHT', 'SCRATCH', 'GENERAL'])],
        ]);

        $jobCard = DB::transaction(function () use ($validated, $request, $workshop): JobCard {
            $cleanVehicle = strtoupper(preg_replace('/\s+/', '', $validated['vehicle_number']));

            $job = JobCard::create([
                'workshop_id' => $workshop->id,
                'vehicle_number' => $cleanVehicle,
                'customer_name' => trim($validated['customer_name']),
                'customer_phone' => $validated['customer_phone'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store("workshops/{$workshop->id}/jobs/{$job->id}", 'public');
                $job->media()->create([
                    'media_path' => $path,
                    'tag' => $validated['tags'][$index],
                ]);
            }

            return $job;
        });

        $whatsapp->sendCheckinLink(
            $jobCard->customer_phone,
            $jobCard->customer_name,
            $jobCard->vehicle_number,
            $jobCard->tracking_token
        );

        return response()->json([
            'status' => 'success',
            'tracking_url' => route('track', $jobCard->tracking_token),
        ], 201);
    }

    public function track(string $token)
    {
        $job = JobCard::with(['workshop', 'media'])
            ->where('tracking_token', $token)
            ->firstOrFail();

        return view('track', compact('job'));
    }
}
