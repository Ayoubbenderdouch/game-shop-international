<?php

namespace App\Services;

class PubgUcService
{
    /**
     * Mock PUBG UC charging API
     * In production, this would integrate with actual PUBG API
     */
    public function chargeUc($playerId, $ucAmount)
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
            'transaction_id' => 'TXN' . strtoupper(uniqid()),
            'player_id' => $playerId,
            'uc_amount' => $ucAmount,
            'timestamp' => now()->toISOString()
        ];
    }

    private function validatePlayerId($playerId)
    {
        // Mock validation - check if player ID matches expected format
        return preg_match('/^\d{8,12}$/', $playerId);
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
}
