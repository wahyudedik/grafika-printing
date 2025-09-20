<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class XenditBalanceService
{
    private $baseUrl;
    private $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.xendit.base_url', 'https://api.xendit.co');
        $this->secretKey = config('services.xendit.secret_key');
    }

    /**
     * Get Xendit account balance
     */
    public function getBalance()
    {
        try {
            // Cache balance for 5 minutes to avoid too many API calls
            return Cache::remember('xendit_balance', 300, function () {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                    'Content-Type' => 'application/json'
                ])->get($this->baseUrl . '/v2/balance');

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'balance' => $data['balance'] ?? 0,
                        'currency' => $data['currency'] ?? 'IDR',
                        'last_updated' => now()
                    ];
                } else {
                    Log::error('Xendit balance API failed: ' . $response->body());
                    return [
                        'success' => false,
                        'error' => 'Failed to fetch balance',
                        'balance' => 0,
                        'currency' => 'IDR',
                        'last_updated' => now()
                    ];
                }
            });
        } catch (\Exception $e) {
            Log::error('Xendit balance service error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable',
                'balance' => 0,
                'currency' => 'IDR',
                'last_updated' => now()
            ];
        }
    }

    /**
     * Get balance with detailed breakdown
     */
    public function getDetailedBalance()
    {
        try {
            return Cache::remember('xendit_detailed_balance', 300, function () {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                    'Content-Type' => 'application/json'
                ])->get($this->baseUrl . '/v2/balance');

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'balance' => $data['balance'] ?? 0,
                        'currency' => $data['currency'] ?? 'IDR',
                        'available_balance' => $data['available_balance'] ?? 0,
                        'pending_balance' => $data['pending_balance'] ?? 0,
                        'last_updated' => now(),
                        'raw_data' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'Failed to fetch detailed balance',
                        'balance' => 0,
                        'currency' => 'IDR',
                        'last_updated' => now()
                    ];
                }
            });
        } catch (\Exception $e) {
            Log::error('Xendit detailed balance service error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable',
                'balance' => 0,
                'currency' => 'IDR',
                'last_updated' => now()
            ];
        }
    }

    /**
     * Format balance for display
     */
    public function formatBalance($amount, $currency = 'IDR')
    {
        if ($currency === 'IDR') {
            return 'Rp ' . number_format($amount, 0, ',', '.');
        }

        return $currency . ' ' . number_format($amount, 2);
    }

    /**
     * Get balance status (healthy, warning, critical)
     */
    public function getBalanceStatus($balance)
    {
        if ($balance < 1000000) { // Less than 1M
            return 'critical';
        } elseif ($balance < 5000000) { // Less than 5M
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Clear balance cache
     */
    public function clearCache()
    {
        Cache::forget('xendit_balance');
        Cache::forget('xendit_detailed_balance');
    }

    /**
     * Get balance with status
     */
    public function getBalanceWithStatus()
    {
        $balanceData = $this->getBalance();

        if ($balanceData['success']) {
            $balanceData['status'] = $this->getBalanceStatus($balanceData['balance']);
            $balanceData['formatted_balance'] = $this->formatBalance($balanceData['balance'], $balanceData['currency']);
        }

        return $balanceData;
    }
}
