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
    public function checkin(string $filePath, string $comment): bool {

        if (is_dir($filePath)) {
            return false;
        }

        $dir = dirname($filePath);
        $file = basename($filePath);
        $rcsPath = $dir . '/.rcs/';

        if (!is_dir($rcsPath)) {
            mkdir($rcsPath, 0755, true);
        }

        $files = glob($rcsPath . $file . '*json');
        $data = [];
        if (!$files) {
            $now = date("Y-m-d H:i:s");
            $content = file_get_contents($filePath);
            $data = [
                "file_name" => $file,
                "latest_version" => 1,
                "history" => [[
                    "version" => 1,
                    "date" => $now,
                    "comment" => $comment,
                    "content" => $content,
                ]]
            ];
        } else {
            $data = json_decode(file_get_contents($files[0]), true);

            $newRev = ++$data["latest_version"];
            $addData = file_get_contents($filePath);
            $now = date("Y-m-d H:i:s");
            $final = [
                "version" => $newRev,
                "date" => $now,
                "comment" => $comment,
                "content" => $addData,
            ];
            array_push($data["history"], $final);
        }

        $data = json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents($rcsPath . $file . ".json", $data) !== false;
    }
}