<?php

namespace Tests\Unit\Services;

use App\Services\XenditBalanceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditBalanceServiceTest extends TestCase
{
    public function test_gets_balance_successfully()
    {
        // Mock successful response
        Http::fake([
            'api.xendit.co/v2/balance' => Http::response([
                'balance' => 10000000,
                'currency' => 'IDR'
            ], 200)
        ]);

        $service = new XenditBalanceService();
        $result = $service->getBalance();

        $this->assertTrue($result['success']);
        $this->assertEquals(10000000, $result['balance']);
        $this->assertEquals('IDR', $result['currency']);
    }

    public function test_handles_api_failure()
    {
        // Mock failed response
        Http::fake([
            'api.xendit.co/v2/balance' => Http::response([], 500)
        ]);

        $service = new XenditBalanceService();
        $result = $service->getBalance();

        $this->assertFalse($result['success']);
        $this->assertEquals(0, $result['balance']);
    }

    public function test_caches_balance()
    {
        // Mock successful response
        Http::fake([
            'api.xendit.co/v2/balance' => Http::response([
                'balance' => 10000000,
                'currency' => 'IDR'
            ], 200)
        ]);

        $service = new XenditBalanceService();

        // First call
        $result1 = $service->getBalance();

        // Second call should use cache
        $result2 = $service->getBalance();

        $this->assertEquals($result1['balance'], $result2['balance']);

        // Verify only one HTTP request was made
        Http::assertSentCount(1);
    }

    public function test_formats_balance_correctly()
    {
        $service = new XenditBalanceService();

        $formatted = $service->formatBalance(1000000, 'IDR');
        $this->assertEquals('Rp 1.000.000', $formatted);

        $formatted = $service->formatBalance(1000.50, 'USD');
        $this->assertEquals('USD 1,000.50', $formatted);
    }

    public function test_determines_balance_status()
    {
        $service = new XenditBalanceService();

        $this->assertEquals('critical', $service->getBalanceStatus(500000));
        $this->assertEquals('warning', $service->getBalanceStatus(2000000));
        $this->assertEquals('healthy', $service->getBalanceStatus(10000000));
    }

    public function test_clears_cache()
    {
        Cache::put('xendit_balance', ['balance' => 1000000], 300);

        $service = new XenditBalanceService();
        $service->clearCache();

        $this->assertFalse(Cache::has('xendit_balance'));
    }
}
