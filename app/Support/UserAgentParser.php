<?php

namespace App\Support;

class UserAgentParser
{
    public static function parse(?string $ua): array
    {
        $ua = $ua ?? '';

        $browser = 'Unknown';
        if (preg_match('/Edg\//i', $ua))                        $browser = 'Edge';
        elseif (preg_match('/OPR\/|Opera/i', $ua))              $browser = 'Opera';
        elseif (preg_match('/Chrome\//i', $ua))                 $browser = 'Chrome';
        elseif (preg_match('/Firefox\//i', $ua))                $browser = 'Firefox';
        elseif (preg_match('/Safari\//i', $ua))                 $browser = 'Safari';
        elseif (preg_match('/MSIE|Trident/i', $ua))             $browser = 'Internet Explorer';

        $platform = 'Unknown';
        if (preg_match('/Windows NT 10/i', $ua))                $platform = 'Windows 10/11';
        elseif (preg_match('/Windows/i', $ua))                  $platform = 'Windows';
        elseif (preg_match('/Macintosh|Mac OS X/i', $ua))       $platform = 'macOS';
        elseif (preg_match('/Android/i', $ua))                  $platform = 'Android';
        elseif (preg_match('/iPhone|iPad|iPod/i', $ua))         $platform = 'iOS';
        elseif (preg_match('/Linux/i', $ua))                    $platform = 'Linux';

        $deviceType = 'desktop';
        if (preg_match('/iPad|Tablet/i', $ua))                  $deviceType = 'tablet';
        elseif (preg_match('/Mobile|Android.*Mobile|iPhone|iPod/i', $ua)) $deviceType = 'mobile';

        return [
            'browser'     => $browser,
            'platform'    => $platform,
            'device_type' => $deviceType,
        ];
    }
}
