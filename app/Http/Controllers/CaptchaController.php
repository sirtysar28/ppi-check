<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Captcha SVG (tanpa dependency GD) - karakter acak + noise.
 * Kode disimpan di session dan divalidasi case-insensitive.
 */
class CaptchaController extends Controller
{
    public function __invoke(Request $request)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa I, O, 0, 1 agar tidak membingungkan
        $length = 5;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $request->session()->put('captcha', $code);

        $width = 190;
        $height = 64;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" role="img" aria-label="captcha">';

        // Background gradien
        $svg .= '<defs><linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0" stop-color="#e8f4f0"/><stop offset="1" stop-color="#d3e8fb"/>'
            . '</linearGradient></defs>';
        $svg .= '<rect width="100%" height="100%" rx="10" fill="url(#bg)"/>';

        // Noise dots
        for ($i = 0; $i < 45; $i++) {
            $svg .= sprintf(
                '<circle cx="%d" cy="%d" r="%d" fill="rgba(13,110,84,%s)"/>',
                random_int(4, $width - 4),
                random_int(4, $height - 4),
                random_int(1, 3),
                number_format(mt_rand(10, 35) / 100, 2, '.', '')
            );
        }

        // Noise lines
        $colors = ['#0d6e54', '#1a6fb5', '#b5771a'];
        for ($i = 0; $i < 5; $i++) {
            $svg .= sprintf(
                '<path d="M %d %d Q %d %d %d %d" fill="none" stroke="%s" stroke-width="%d" opacity="0.35"/>',
                random_int(-10, 40),
                random_int(8, $height - 8),
                random_int(60, 130),
                random_int(0, $height),
                random_int($width - 50, $width + 15),
                random_int(8, $height - 8),
                $colors[array_rand($colors)],
                random_int(1, 2)
            );
        }

        // Karakter dengan rotasi & posisi acak
        $slot = ($width - 30) / $length;
        for ($i = 0; $i < $length; $i++) {
            $x = 18 + ($slot * $i) + random_int(-2, 6);
            $y = 44 + random_int(-4, 4);
            $rotate = random_int(-16, 16);
            $fontSize = random_int(27, 33);
            $colorsText = ['#0b5c45', '#15577f', '#7c3f0c', '#43296b'];
            $svg .= sprintf(
                '<text x="%.1f" y="%d" font-family="Georgia, \'Times New Roman\', serif" font-size="%d" font-weight="bold" fill="%s" transform="rotate(%d %.1f %d)">%s</text>',
                $x,
                $y,
                $fontSize,
                $colorsText[array_rand($colorsText)],
                $rotate,
                $x,
                $y,
                $code[$i]
            );
        }

        $svg .= '</svg>';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
