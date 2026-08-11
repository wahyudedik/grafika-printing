<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;

class FlashMessage
{
    /**
     * Standard flash message keys used across the application.
     */
    private static array $keys = [
        'success' => 'toast_success',
        'error'   => 'toast_error',
        'warning' => 'toast_warning',
        'info'    => 'toast_info',
    ];

    /**
     * Send a flash message with consistent key naming.
     */
    public static function send(
        RedirectResponse $redirect,
        string $message,
        string $type = 'success'
    ): RedirectResponse {
        $key = self::$keys[$type] ?? 'toast_success';
        return $redirect->with($key, $message);
    }

    /**
     * Send a success flash message.
     */
    public static function success(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'success');
    }

    /**
     * Send an error flash message.
     */
    public static function error(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'error');
    }

    /**
     * Send a warning flash message.
     */
    public static function warning(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'warning');
    }

    /**
     * Send an info flash message.
     */
    public static function info(RedirectResponse $redirect, string $message): RedirectResponse
    {
        return self::send($redirect, $message, 'info');
    }

    /**
     * Send a success message and redirect back.
     */
    public static function backSuccess(string $message): RedirectResponse
    {
        return self::success(back(), $message);
    }

    /**
     * Send an error message and redirect back.
     */
    public static function backError(string $message): RedirectResponse
    {
        return self::error(back(), $message);
    }
}
