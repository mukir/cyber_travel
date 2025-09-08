<?php

namespace Tests\Feature;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_must_verify_email_before_accessing_protected_routes()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertRedirect('/verify-email');
    }

    public function test_verified_users_can_access_protected_routes()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
    }

    public function test_registration_sends_verification_email()
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0712345678',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/verify-email');
        
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Mail::assertSent(VerifyEmailMail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
    }

    public function test_email_verification_works()
    {
        Event::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/dashboard?verified=1');
        
        $this->assertNotNull($user->fresh()->email_verified_at);
        
        Event::assertDispatched(Verified::class);
    }

    public function test_verification_link_expires()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(-1), // Expired
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertStatus(403);
        
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_resend_verification_email()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect();
        
        Mail::assertSent(VerifyEmailMail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
    }
}
