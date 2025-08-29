<?php

namespace App\Services;

class FreeFireService
{
    /**
     * Mock Free Fire Diamond charging API
     * In production, this would integrate with actual Garena API
     */
    public function chargeDiamonds($playerId, $diamondAmount)
    {
        // Simulate API call delay
        sleep(1);

        // Mock validation
        if (!$this->validatePlayerId($playerId)) {
            return [
                'success' => false,
                'error' => 'Invalid Player ID'
            ];
        }

        // Mock successful charge
        return [
            'success' => true,
            'transaction_id' => 'FF-TXN' . strtoupper(uniqid()),
            'player_id' => $playerId,
            'diamond_amount' => $diamondAmount,
            'timestamp' => now()->toISOString()
        ];
    }

    private function validatePlayerId($playerId)
    {
        // Mock validation - check if player ID matches expected format
        // Free Fire IDs are typically 9-12 digits
        return preg_match('/^\d{9,12}$/', $playerId);
    }

    public function getPlayerInfo($playerId)
    {
        // Mock player info retrieval
        return [
            'player_id' => $playerId,
            'player_name' => 'Player_' . substr($playerId, -4),
            'level' => rand(1, 100),
            'region' => 'Global',
        ];
    }

    /**
     * Generate a Free Fire redemption code
     * In production, this would be retrieved from actual inventory
     */
    public function generateRedemptionCode()
    {
        // Generate a mock Free Fire redemption code format
        $prefix = 'FF';
        $middle = strtoupper(bin2hex(random_bytes(4)));
        $suffix = strtoupper(bin2hex(random_bytes(4)));

        return $prefix . $middle . $suffix;
    }
}
