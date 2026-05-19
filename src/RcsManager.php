<?php

declare(strict_types=1);

namespace YuCorder;

class RcsManager
{
    /**
     * @param $string
     */
    public function init (string $projectPath): bool {
        $rcsPath = rtrim($projectPath, '/') . '/.rcs';

        if (is_dir($rcsPath)) {
            return true;
        }
        if (!mkdir($rcsPath, 0755, true)) {
            return false;
        }

        return true;
    }
}