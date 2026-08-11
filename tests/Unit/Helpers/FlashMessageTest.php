<?php

namespace Tests\Unit\Helpers;

use App\Http\Responses\FlashMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class FlashMessageTest extends TestCase
{
    // ═══════════════════════════════════════════════════════════════════
    // Helper: create a redirect response with session
    // ═══════════════════════════════════════════════════════════════════

    private function makeRedirect(): RedirectResponse
    {
        return redirect('/test-redirect-target');
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::send() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_send_sets_success_flash_message(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::send($redirect, 'Berhasil', 'success');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('Berhasil', $result->getSession()->get('toast_success'));
    }

    public function test_send_sets_error_flash_message(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::send($redirect, 'Gagal', 'error');

        $this->assertEquals('Gagal', $result->getSession()->get('toast_error'));
    }

    public function test_send_sets_warning_flash_message(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::send($redirect, 'Peringatan', 'warning');

        $this->assertEquals('Peringatan', $result->getSession()->get('toast_warning'));
    }

    public function test_send_sets_info_flash_message(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::send($redirect, 'Informasi', 'info');

        $this->assertEquals('Informasi', $result->getSession()->get('toast_info'));
    }

    public function test_send_defaults_to_success_for_unknown_type(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::send($redirect, 'Fallback', 'unknown');

        $this->assertEquals('Fallback', $result->getSession()->get('toast_success'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::success() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_success_sets_correct_key(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::success($redirect, 'Data tersimpan');

        $this->assertEquals('Data tersimpan', $result->getSession()->get('toast_success'));
        $this->assertNull($result->getSession()->get('toast_error'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::error() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_error_sets_correct_key(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::error($redirect, 'Terjadi kesalahan');

        $this->assertEquals('Terjadi kesalahan', $result->getSession()->get('toast_error'));
        $this->assertNull($result->getSession()->get('toast_success'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::warning() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_warning_sets_correct_key(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::warning($redirect, 'Hati-hati');

        $this->assertEquals('Hati-hati', $result->getSession()->get('toast_warning'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::info() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_info_sets_correct_key(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::info($redirect, 'Catatan');

        $this->assertEquals('Catatan', $result->getSession()->get('toast_info'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::backSuccess() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_back_success_returns_redirect_back(): void
    {
        $result = FlashMessage::backSuccess('Berhasil disimpan');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(302, $result->getStatusCode());
    }

    // ═══════════════════════════════════════════════════════════════════
    // FlashMessage::backError() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_back_error_returns_redirect_back(): void
    {
        $result = FlashMessage::backError('Terjadi kesalahan');

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(302, $result->getStatusCode());
    }

    // ═══════════════════════════════════════════════════════════════════
    // Return Type Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_all_methods_return_redirect_response(): void
    {
        $redirect = $this->makeRedirect();

        $this->assertInstanceOf(RedirectResponse::class, FlashMessage::send($redirect, 'msg'));
        $this->assertInstanceOf(RedirectResponse::class, FlashMessage::success($redirect, 'msg'));
        $this->assertInstanceOf(RedirectResponse::class, FlashMessage::error($redirect, 'msg'));
        $this->assertInstanceOf(RedirectResponse::class, FlashMessage::warning($redirect, 'msg'));
        $this->assertInstanceOf(RedirectResponse::class, FlashMessage::info($redirect, 'msg'));
    }

    // ═══════════════════════════════════════════════════════════════════
    // Key Naming Convention Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_flash_keys_follow_toast_naming_convention(): void
    {
        $successResult = FlashMessage::success($this->makeRedirect(), 'ok');
        $errorResult = FlashMessage::error($this->makeRedirect(), 'fail');
        $warningResult = FlashMessage::warning($this->makeRedirect(), 'warn');
        $infoResult = FlashMessage::info($this->makeRedirect(), 'info');

        $this->assertNotNull($successResult->getSession()->get('toast_success'), 'Success key should be toast_success');
        $this->assertNotNull($errorResult->getSession()->get('toast_error'), 'Error key should be toast_error');
        $this->assertNotNull($warningResult->getSession()->get('toast_warning'), 'Warning key should be toast_warning');
        $this->assertNotNull($infoResult->getSession()->get('toast_info'), 'Info key should be toast_info');
    }

    // ═══════════════════════════════════════════════════════════════════
    // Message Content Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_indonesian_messages_work_correctly(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::success($redirect, 'Data berhasil disimpan ke database');

        $this->assertEquals('Data berhasil disimpan ke database', $result->getSession()->get('toast_success'));
    }

    public function test_empty_message_is_set(): void
    {
        $redirect = $this->makeRedirect();
        $result = FlashMessage::success($redirect, '');

        $this->assertEquals('', $result->getSession()->get('toast_success'));
    }
}
