<?php

namespace App\Helpers;

class BrowserHelper
{
    /**
     * Parse user agent string to extract browser and device information
     */
    public static function parseUserAgent(string $userAgent): array
    {
        // Try using jenssegers agent if available
        if (class_exists('\Jenssegers\Agent\Agent')) {
            return self::parseWithAgent($userAgent);
        }

        // Fallback to manual parsing
        return self::parseManually($userAgent);
    }

    /**
     * Parse using Jenssegers Agent library
     */
    private static function parseWithAgent(string $userAgent): array
    {
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($userAgent);

        return [
            'browser' => $agent->browser() ?: 'Unknown',
            'browser_version' => $agent->version($agent->browser()) ?: '0',
            'operating_system' => $agent->platform() ?: 'Unknown',
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ];
    }

    /**
     * Manual parsing of user agent string (fallback)
     */
    private static function parseManually(string $userAgent): array
    {
        $browser = 'Unknown';
        $browserVersion = '0';
        $os = 'Unknown';
        $deviceType = 'desktop';

        // Detect browser
        if (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
            if (preg_match('/Firefox\/(\d+\.\d+)/', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
        } elseif (str_contains($userAgent, 'Chrome')) {
            $browser = 'Chrome';
            if (preg_match('/Chrome\/(\d+\.\d+)/', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
        } elseif (str_contains($userAgent, 'Safari')) {
            $browser = 'Safari';
            if (preg_match('/Version\/(\d+\.\d+)/', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
        } elseif (str_contains($userAgent, 'Edge')) {
            $browser = 'Edge';
            if (preg_match('/Edge\/(\d+\.\d+)/', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
        } elseif (str_contains($userAgent, 'Opera')) {
            $browser = 'Opera';
            if (preg_match('/Version\/(\d+\.\d+)/', $userAgent, $matches)) {
                $browserVersion = $matches[1];
            }
        }

        // Detect OS
        if (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            $os = 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $os = 'iOS';
        }

        // Detect device type
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || 
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'BlackBerry')) {
            $deviceType = 'mobile';
        } elseif (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
            $deviceType = 'tablet';
        }

        return [
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'operating_system' => $os,
            'device_type' => $deviceType,
        ];
    }
}
