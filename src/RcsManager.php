<?php

declare(strict_types=1);

namespace YuCorder;

class RcsManager
{
    /**
     * @param string $filePath
     * @param string $comment
     * @return bool
     */
    public function checkIn(string $filePath, string $comment): bool {
        if (is_dir($filePath) || !file_exists($filePath)) {
            return false;
        }

        $jsonPath = $this->getJsonPath($filePath);
        $rcsDir = dirname($jsonPath);

        if (!is_dir($rcsDir)) {
            mkdir($rcsDir, 0755, true);
        }

        $now = date("Y-m-d H:i:s");
        $currentContent = file_get_contents($filePath);
        $fileName = basename($filePath);

        if (!file_exists($jsonPath)) {
            $rcsData = [
                "file_name" => $fileName,
                "latest_version" => 1,
                "history" => [[
                    "version" => 1,
                    "date" => $now,
                    "comment" => $comment,
                    "content" => $currentContent,
                ]]
            ];
        } else {
            $rcsData = json_decode(file_get_contents($jsonPath), true);
            $nextVersion = ++$rcsData["latest_version"];
            $newHistoryItem = [
                "version" => $nextVersion,
                "date" => $now,
                "comment" => $comment,
                "content" => $currentContent,
            ];
            array_push($rcsData["history"], $newHistoryItem);
        }

        $jsonString = json_encode($rcsData, JSON_PRETTY_PRINT);
        return file_put_contents($jsonPath, $jsonString) !== false;
    }

    /**
     * @param string $filePath
     * @param int $version
     * @return bool
     * @throws \InvalidArgumentException|\RuntimeException
     */
    public function checkOut(string $filePath, int $version): bool {
        if (is_dir($filePath)) {
            throw new \InvalidArgumentException("Target path is a directory");
        }

        $jsonPath = $this->getJsonPath($filePath);

        if (!file_exists($jsonPath)) {
            throw new \RuntimeException("RCS repository JSON file not found. Check in first.");
        }

        $rcsData = json_decode(file_get_contents($jsonPath), true);
        $index = $version - 1;
        if (!isset($rcsData["history"][$index])) {
            throw new \InvalidArgumentException("Version {$version} not found in history.");
        }
        
        $targetContent = $rcsData["history"][$index]["content"];
        return file_put_contents($filePath, $targetContent) !== false;
    }

     /**
     * @param string $filePath
     * @param int|null $version
     * @return array
     * @throws \InvalidArgumentException|\RuntimeException
     */
    public function log(string $filePath, ?int $version = null): array {
        if (is_dir($filePath)) {
            throw new \InvalidArgumentException("Target path is a directory.");
        }

        $jsonPath = $this->getJsonPath($filePath);

        if (!file_exists($jsonPath)) {
            throw new \RuntimeException("RCS repository JSON file not found. Check in first.");
        }

        $rcsData = json_decode(file_get_contents($jsonPath), true);

        if ($version !== null) {
            $index = $version - 1;
            if (!isset($rcsData["history"][$index])) {
                throw new \InvalidArgumentException("Version {$version} not found in history.");
            }
            $log[] = $rcsData["history"][$index];
        } else {
            $log = $rcsData["history"];
        }

        return $log;
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getJsonPath(string $filePath): string {
        $dir = dirname($filePath);
        $file = basename($filePath);
        return $dir . '/.rcs/' . $file . '.json';
    }
}