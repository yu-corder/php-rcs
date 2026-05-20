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

    /**
     * @param $string, $string
     */
    public function checkin(string $filePath, string $commet): bool {

        if (is_dir($filePath)) {
            return false;
        }

        $dir = dirname($filePath);
        $file = basename($filePath);
        $rcsPath = $dir . '/.rcs/';

        $files = glob($rcsPath . $file . '*');
        $rev = ".v";
        $version = 1;
        foreach($files as $revfile) {
            $tmpfile = basename($revfile);
            $v = strrpos($tmpfile, 'v');
            $tmp = substr($tmpfile, $v + 1) + 1;
            if ($version <= $tmp) $version = $tmp;
        }

        $final_rev = $rev . $version;
        if (!copy($filePath, $rcsPath . $file . $final_rev)) {
            return false;
        }
        return true;
    }
}