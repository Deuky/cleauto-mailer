<?php

namespace App\Twig\Extension;

use Twig\Attribute\AsTwigFilter;

class EmojiExtension
{
    #[AsTwigFilter('emoji')]
    public function formatEmoji(string $value): string
    {
        return match($value) {
            'true' => '✅',
            'false' => '❌',
            default => $value
        };
    }
}