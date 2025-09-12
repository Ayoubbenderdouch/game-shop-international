<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BrevoEmailService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl = 'https://api.brevo.com/v3/';

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Send transactional email via Brevo API
     */
    public function sendTransactionalEmail($to, $subject, $htmlContent, $params = [])
    {
        try {
            $data = [
                'to' => [['email' => $to]],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
                'sender' => [
                    'name' => config('mail.from.name'),
                    'email' => config('mail.from.address'),
                ],
            ];

            if (!empty($params)) {
                $data['params'] = $params;
            }

            $response = $this->client->post('smtp/email', [
                'json' => $data,
            ]);

            $result = json_decode($response->getBody(), true);

            Log::info('Brevo email sent successfully', [
                'messageId' => $result['messageId'] ?? null,
                'to' => $to,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Brevo email sending failed', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
            throw $e;
        }
    }

    /**
     * Create or update contact in Brevo
     */
    public function createOrUpdateContact($email, $attributes = [])
    {
        try {
            $data = [
                'email' => $email,
                'updateEnabled' => true,
            ];

            if (!empty($attributes)) {
                $data['attributes'] = $attributes;
            }

            $response = $this->client->post('contacts', [
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Brevo contact creation failed', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
            throw $e;
        }
    }

    /**
     * Add contact to a list
     */
    public function addContactToList($email, $listId)
    {
        try {
            $response = $this->client->post("contacts/lists/{$listId}/contacts/add", [
                'json' => [
                    'emails' => [$email],
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Failed to add contact to list', [
                'error' => $e->getMessage(),
                'email' => $email,
                'listId' => $listId,
            ]);
            throw $e;
        }
    }

    /**
     * Send SMS via Brevo
     */
    public function sendSms($recipient, $content)
    {
        try {
            $response = $this->client->post('transactionalSMS/sms', [
                'json' => [
                    'type' => 'transactional',
                    'sender' => config('services.brevo.sms_sender', 'GameStore'),
                    'recipient' => $recipient,
                    'content' => $content,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Brevo SMS sending failed', [
                'error' => $e->getMessage(),
                'recipient' => $recipient,
            ]);
            throw $e;
        }
    }
}
