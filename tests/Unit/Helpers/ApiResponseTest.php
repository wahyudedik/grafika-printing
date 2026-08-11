<?php

namespace Tests\Unit\Helpers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::success() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_success_returns_200_by_default(): void
    {
        $response = ApiResponse::success(['id' => 1]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Success', $data['message']);
        $this->assertEquals(['id' => 1], $data['data']);
    }

    public function test_success_with_custom_message(): void
    {
        $response = ApiResponse::success(null, 'Data berhasil dimuat');

        $data = $response->getData(true);
        $this->assertEquals('Data berhasil dimuat', $data['message']);
        $this->assertNull($data['data']);
    }

    public function test_success_with_custom_status_code(): void
    {
        $response = ApiResponse::success(['key' => 'value'], 'OK', 201);

        $this->assertEquals(201, $response->getStatusCode());
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::error() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_error_returns_400_by_default(): void
    {
        $response = ApiResponse::error('Terjadi kesalahan');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Terjadi kesalahan', $data['message']);
    }

    public function test_error_with_custom_status_code(): void
    {
        $response = ApiResponse::error('Unauthorized', 401);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_error_with_validation_errors(): void
    {
        $errors = ['name' => ['Nama wajib diisi'], 'email' => ['Email tidak valid']];
        $response = ApiResponse::error('Validasi gagal', 422, $errors);

        $data = $response->getData(true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $data);
        $this->assertEquals($errors, $data['errors']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::paginated() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_paginated_returns_paginated_data(): void
    {
        $items = collect([['id' => 1], ['id' => 2], ['id' => 3]]);
        $paginator = new LengthAwarePaginator($items, 30, 10, 1);

        $response = ApiResponse::paginated($paginator);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('meta', $data);
        $this->assertEquals(30, $data['meta']['total']);
        $this->assertEquals(10, $data['meta']['per_page']);
        $this->assertEquals(1, $data['meta']['current_page']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::created() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_created_returns_201(): void
    {
        $response = ApiResponse::created(['id' => 5]);

        $this->assertEquals(201, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Data berhasil dibuat', $data['message']);
        $this->assertEquals(['id' => 5], $data['data']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::noContent() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_no_content_returns_204(): void
    {
        $response = ApiResponse::noContent();

        $this->assertEquals(204, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Data berhasil dihapus', $data['message']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::validationError() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_validation_error_returns_422(): void
    {
        $errors = ['field' => ['Error message']];
        $response = ApiResponse::validationError($errors);

        $this->assertEquals(422, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals($errors, $data['errors']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::unauthorized() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_unauthorized_returns_401(): void
    {
        $response = ApiResponse::unauthorized();

        $this->assertEquals(401, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Tidak terautentikasi', $data['message']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::forbidden() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_forbidden_returns_403(): void
    {
        $response = ApiResponse::forbidden();

        $this->assertEquals(403, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Akses ditolak', $data['message']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // ApiResponse::notFound() Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_not_found_returns_404(): void
    {
        $response = ApiResponse::notFound();

        $this->assertEquals(404, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Data tidak ditemukan', $data['message']);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Response Format Consistency Tests
    // ═══════════════════════════════════════════════════════════════════

    public function test_all_responses_have_success_field(): void
    {
        $responses = [
            ApiResponse::success(),
            ApiResponse::error('err'),
            ApiResponse::created(),
            ApiResponse::noContent(),
            ApiResponse::validationError([]),
            ApiResponse::unauthorized(),
            ApiResponse::forbidden(),
            ApiResponse::notFound(),
        ];

        foreach ($responses as $response) {
            $data = $response->getData(true);
            $this->assertArrayHasKey('success', $data, 'Response should have "success" field');
            $this->assertArrayHasKey('message', $data, 'Response should have "message" field');
        }
    }

    public function test_success_responses_have_data_field(): void
    {
        $responses = [
            ApiResponse::success(['key' => 'value']),
            ApiResponse::created(['id' => 1]),
        ];

        foreach ($responses as $response) {
            $data = $response->getData(true);
            $this->assertArrayHasKey('data', $data, 'Success response should have "data" field');
        }
    }

    public function test_error_type_responses_return_json(): void
    {
        $errorResponses = [
            ApiResponse::error('err', 400),
            ApiResponse::validationError(['field' => ['msg']]),
            ApiResponse::unauthorized(),
            ApiResponse::forbidden(),
            ApiResponse::notFound(),
        ];

        foreach ($errorResponses as $response) {
            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));
        }
    }
}
