<?php

namespace Tests\Feature;

use App\Models\JobCard;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobCardIntakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_workshop_bound_checkin_persists_media_and_returns_a_tracking_link(): void
    {
        Storage::fake('public');
        Http::fake();
        config()->set('services.whatsapp.token', 'test-token');
        config()->set('services.whatsapp.phone_id', '123456');
        config()->set('services.whatsapp.endpoint', 'https://graph.facebook.com/test/messages');

        $workshop = Workshop::create([
            'name' => 'Gloss Garage',
            'phone' => '9876543210',
            'address' => 'Bengaluru',
        ]);

        $response = $this->post(route('jobcards.store', $workshop), [
            'vehicle_number' => 'KA 01 AB 1234',
            'customer_name' => 'Asha Rao',
            'customer_phone' => '9876543211',
            'notes' => 'Scratch on left rear bumper',
            'photos' => [UploadedFile::fake()->image('left.jpg')],
            'tags' => ['SCRATCH'],
        ]);

        $jobCard = JobCard::firstOrFail();

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('tracking_url', route('track', $jobCard->tracking_token));
        $this->assertSame($workshop->id, $jobCard->workshop_id);
        $this->assertSame('KA01AB1234', $jobCard->vehicle_number);
        $this->assertDatabaseHas('job_card_media', [
            'job_card_id' => $jobCard->id,
            'tag' => 'SCRATCH',
        ]);
        Storage::disk('public')->assertExists($jobCard->media()->firstOrFail()->media_path);
        Http::assertSent(fn ($request) => $request->url() === 'https://graph.facebook.com/test/messages'
            && $request['to'] === '919876543211');

        $this->get(route('track', $jobCard->tracking_token))
            ->assertOk()
            ->assertSee('KA01AB1234')
            ->assertSee('Scratch on left rear bumper')
            ->assertSee('Intake inspection');
    }

    public function test_a_checkin_requires_an_indian_whatsapp_number_and_at_least_one_photo(): void
    {
        $workshop = Workshop::create([
            'name' => 'Gloss Garage',
            'phone' => '9876543210',
        ]);

        $this->postJson(route('jobcards.store', $workshop), [
            'vehicle_number' => 'KA01AB1234',
            'customer_name' => 'Asha Rao',
            'customer_phone' => '12345',
            'photos' => [],
            'tags' => [],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_phone', 'photos']);

        $this->assertDatabaseCount('job_cards', 0);
    }
}
