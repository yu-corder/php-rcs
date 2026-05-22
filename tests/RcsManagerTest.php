<?php

declare(strict_types=1);

namespace YuCorder\Tests;

use PHPUnit\Framework\TestCase;
use YuCorder\RcsManager;

class RcsManagerTest extends TestCase
{
    private RcsManager $rcs;
    private string $testFile;
    private string $rcsJson;
    private string $rcsDir;
    private string $errorFile;
    private string $errorDir;

    protected function setUp(): void
    {
        $this->rcs = new RcsManager();
        $this->testFile = __DIR__ . '/sandbox.txt';
        $this->rcsDir = __DIR__ . '/.rcs';
        $this->rcsJson = $this->rcsDir . '/sandbox.txt.json';
        $this->errorFile = __DIR__ . '/error.txt';
        $this->errorDir = __DIR__ . '/';

        $this->cleanUpFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanUpFiles();
    }

    private function cleanUpFiles(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        if (file_exists($this->rcsJson)) {
            unlink($this->rcsJson);
        }
        if (file_exists($this->rcsDir) && is_dir($this->rcsDir)) {
            rmdir($this->rcsDir);
        }
    }

    public function test_can_instantiate_rcs_manager(): void
    {
        $this->assertInstanceOf(RcsManager::class, $this->rcs);
    }

    public function test_first_check_in_stores_file_and_log_correctly(): void
    {
        file_put_contents($this->testFile, "Hello, World! - Version 1");
        $this->rcs->checkIn($this->testFile, "First commit! Initialized sandbox.");

        $this->assertFileExists($this->rcsJson);

        $history = $this->rcs->log($this->testFile);
        $this->assertSame(1, $history[0]["version"]);
        $this->assertSame("First commit! Initialized sandbox.", $history[0]["comment"]); 
        $this->assertSame("Hello, World! - Version 1", $history[0]["content"]);
    }

    public function test_can_log_specific_version_and_check_out(): void
    {
        file_put_contents($this->testFile, "Hello, World! - Version 1");
        $this->rcs->checkIn($this->testFile, "First commit!");

        file_put_contents($this->testFile, "Hello, World! - Version 2");
        $this->rcs->checkIn($this->testFile, "2nd commit! Initialized sandbox.");

        $history = $this->rcs->log($this->testFile, 2);
        $this->assertSame(2, $history[0]["version"]);
        $this->assertSame("2nd commit! Initialized sandbox.", $history[0]["comment"]); 
        $this->assertSame("Hello, World! - Version 2", $history[0]["content"]);

        $this->rcs->checkOut($this->testFile, 1);
        $file_content = file_get_contents($this->testFile);
        $this->assertSame("Hello, World! - Version 1", $file_content);

    }

    public function test_check_in_throws_exception_when_file_does_not_exist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->rcs->checkIn($this->errorFile, "First commit!");
    }

    public function test_log_throws_exception_when_file_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->rcs->log($this->errorFile);
    }

    public function test_log_throws_exception_when_path_is_directory(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->rcs->log($this->errorDir);
    }

    public function test_log_throws_exception_when_non_existent_history(): void
    {
        file_put_contents($this->testFile, "Hello");
        $this->rcs->checkIn($this->testFile, "Init");
        $this->expectException(\InvalidArgumentException::class);
        $history = $this->rcs->log($this->testFile, 100);
    }

    public function test_check_out_throws_exception_when_file_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->rcs->checkOut($this->errorFile, 1);
    }

    public function test_check_out_throws_exception_when_path_is_directory(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->rcs->checkOut($this->errorDir, 1);
    }

    public function test_check_out_throws_exception_when_non_existent_version(): void
    {
        file_put_contents($this->testFile, "Hello");
        $this->rcs->checkIn($this->testFile, "Init");
        $this->expectException(\InvalidArgumentException::class);
        $this->rcs->checkOut($this->testFile, 100);
    }

}