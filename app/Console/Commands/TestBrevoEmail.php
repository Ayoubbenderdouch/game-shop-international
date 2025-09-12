<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BrevoEmailService;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class TestBrevoEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brevo:test
                            {type? : The type of test to run (api|smtp|verify|reset|all)}
                            {--email= : The email address to send test emails to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Brevo email integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type') ?? 'all';
        $testEmail = $this->option('email') ?? env('ADMIN_EMAIL', 'admin@yourstore.com');

        $this->info('╔════════════════════════════════════════╗');
        $this->info('║       BREVO EMAIL TESTING SUITE       ║');
        $this->info('╚════════════════════════════════════════╝');

        // Check configuration first
        $this->checkConfiguration();

        switch ($type) {
            case 'api':
                $this->testBrevoApi($testEmail);
                break;
            case 'smtp':
                $this->testBrevoSmtp($testEmail);
                break;
            case 'verify':
                $this->testEmailVerification($testEmail);
                break;
            case 'reset':
                $this->testPasswordReset($testEmail);
                break;
            case 'all':
                $this->runAllTests($testEmail);
                break;
            default:
                $this->error('Invalid test type. Use: api, smtp, verify, reset, or all');
        }
    }

    private function checkConfiguration()
    {
        $this->info('');
        $this->info('CHECKING BREVO CONFIGURATION');
        $this->info('========================================');

        $config = [
            'API Key' => config('services.brevo.api_key'),
            'SMTP Host' => config('services.brevo.smtp_host'),
            'SMTP Port' => config('services.brevo.smtp_port'),
            'SMTP Username' => config('services.brevo.smtp_username'),
            'SMTP Password' => config('services.brevo.smtp_password'),
            'Default Mailer' => config('mail.default'),
            'From Address' => config('mail.from.address'),
            'From Name' => config('mail.from.name'),
        ];

        $table = [];
        foreach ($config as $key => $value) {
            if ($key === 'SMTP Password' || $key === 'API Key') {
                $status = $value ? '✅' : '❌';
                $displayValue = $value ? 'Set (hidden)' : 'Not set';
            } else {
                $status = $value ? '✅' : '❌';
                $displayValue = $value ?: 'Not set';
            }
            $table[] = [$key, $displayValue, $status];
        }

        $this->table(['Configuration', 'Value', 'Status'], $table);

        if (!config('services.brevo.api_key')) {
            $this->warn('⚠️  Brevo API key is not set. Add BREVO_API_KEY to your .env file');
        }

        if (!config('services.brevo.smtp_password')) {
            $this->warn('⚠️  Brevo SMTP password is not set. Add BREVO_PASSWORD to your .env file');
        }
    }

    private function testBrevoApi($email)
    {
        $this->info('');
        $this->info('Testing Brevo API directly...');

        try {
            $brevoService = new BrevoEmailService();

            $htmlContent = '
                <h1>Brevo API Test Email</h1>
                <p>This is a test email sent directly via the Brevo API.</p>
                <p>If you receive this email, your Brevo API integration is working correctly!</p>
                <hr>
                <p><small>Sent at: ' . now()->format('Y-m-d H:i:s') . '</small></p>
            ';

            $result = $brevoService->sendTransactionalEmail(
                $email,
                'Test Email from Brevo API - ' . config('app.name'),
                $htmlContent,
                ['name' => 'Test User']
            );

            $this->info('✅ Brevo API test successful!');
            $this->info('Message ID: ' . ($result['messageId'] ?? 'N/A'));
            $this->info('Email sent to: ' . $email);

            return true;
        } catch (\Exception $e) {
            $this->error('❌ Brevo API test failed: ' . $e->getMessage());

            // Provide troubleshooting tips
            $this->warn('Troubleshooting tips:');
            $this->warn('1. Check if BREVO_API_KEY is set in .env');
            $this->warn('2. Verify the API key is correct');
            $this->warn('3. Check if the sender email is verified in Brevo');

            return false;
        }
    }

    private function testBrevoSmtp($email)
    {
        $this->info('');
        $this->info('Testing Brevo SMTP configuration...');

        try {
            Mail::mailer('brevo')->raw(
                'This is a test email sent via Brevo SMTP. If you receive this, your SMTP configuration is working! Sent at: ' . now(),
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('Test Email from Brevo SMTP - ' . config('app.name'));
                }
            );

            $this->info('✅ Brevo SMTP test successful!');
            $this->info('Email sent to: ' . $email);
            $this->info('Check your inbox for the test email.');

            return true;
        } catch (\Exception $e) {
            $this->error('❌ Brevo SMTP test failed: ' . $e->getMessage());

            $this->warn('Troubleshooting tips:');
            $this->warn('1. Check if BREVO_PASSWORD is set in .env');
            $this->warn('2. Verify BREVO_USERNAME is correct (usually ends with @smtp-brevo.com)');
            $this->warn('3. Check BREVO_HOST (should be smtp-relay.brevo.com)');
            $this->warn('4. Check BREVO_PORT (should be 587 for TLS)');

            return false;
        }
    }

    private function testEmailVerification($email)
    {
        $this->info('');
        $this->info('Testing Email Verification notification...');

        try {
            // Create or get a test user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->info('Creating test user with email: ' . $email);
                $user = User::create([
                    'name' => 'Test User',
                    'email' => $email,
                    'password' => bcrypt('password123'),
                ]);
            }

            // Mark email as unverified for testing
            $user->email_verified_at = null;
            $user->save();

            // Send verification email
            $user->sendEmailVerificationNotification();

            $this->info('✅ Email verification sent successfully!');
            $this->info('Email sent to: ' . $user->email);
            $this->info('Check your inbox for the verification email.');

            return true;
        } catch (\Exception $e) {
            $this->error('❌ Email verification test failed: ' . $e->getMessage());
            return false;
        }
    }

    private function testPasswordReset($email)
    {
        $this->info('');
        $this->info('Testing Password Reset notification...');

        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error('User not found. Creating test user...');
                $user = User::create([
                    'name' => 'Test User',
                    'email' => $email,
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                ]);
            }

            // Generate reset token
            $token = app('auth.password.broker')->createToken($user);

            // Send password reset email
            $user->notify(new CustomResetPassword($token));

            $this->info('✅ Password reset email sent successfully!');
            $this->info('Email sent to: ' . $user->email);
            $this->info('Check your inbox for the password reset email.');

            return true;
        } catch (\Exception $e) {
            $this->error('❌ Password reset test failed: ' . $e->getMessage());
            return false;
        }
    }

    private function runAllTests($email)
    {
        $this->info('');
        $this->info('Running all tests...');
        $this->info('========================================');

        $results = [
            'Brevo API' => $this->testBrevoApi($email),
            'Brevo SMTP' => $this->testBrevoSmtp($email),
            'Email Verification' => $this->testEmailVerification($email),
            'Password Reset' => $this->testPasswordReset($email),
        ];

        $this->info('');
        $this->info('========================================');
        $this->info('TEST RESULTS SUMMARY');
        $this->info('========================================');

        $passed = 0;
        $failed = 0;

        foreach ($results as $test => $result) {
            if ($result) {
                $passed++;
                $this->info("✅ $test: PASSED");
            } else {
                $failed++;
                $this->error("❌ $test: FAILED");
            }
        }

        $this->info('');
        $this->info("Total: $passed passed, $failed failed");
        $this->info('========================================');
    }
}
