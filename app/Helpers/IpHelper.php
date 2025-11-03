<?php

namespace App\Helpers;

class IpHelper
{
    /**
     * Get the real client IP address from various sources
     *
     * @return string
     */
    public static function getRealIp(): string
    {
        // Check various headers for real IP
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                $ip = $_SERVER[$header];

                // X-Forwarded-For can contain multiple IPs, get the first one
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }

                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to Laravel's built-in method
        return request()->ip() ?? '0.0.0.0';
    }

    /**
     * Get client IP including private/local IPs
     *
     * @return string
     */
    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                $ip = $_SERVER[$header];

                // X-Forwarded-For can contain multiple IPs, get the first one
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]);
                }

                // Validate IP address (allow private ranges for local development)
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return request()->ip() ?? '0.0.0.0';
    }
}
