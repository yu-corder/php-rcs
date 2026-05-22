# php-rcs

A pure PHP implementation of a Revision Control System (RCS) for file versioning.

`php-rcs` is a lightweight revision control library inspired by traditional RCS tools and modern version control concepts.

This project started as a learning exercise to better understand how revision control systems work internally, including:

- snapshot storage
- revision history management
- file versioning
- CLI tooling
- repository structure design

Although the project is still evolving, it is fully functional and intended to be usable for small personal projects and experimentation.

---

# Features

- Check in file revisions
- Check out previous versions
- View revision history
- Simple JSON-based storage
- Lightweight PHP implementation
- CLI support
- PSR-4 autoloading support

---

# Requirements

- PHP 8.2+

---

# Security Notice

If you use `php-rcs` to manage sensitive files such as:

- `.env`
- API keys
- configuration files
- private credentials

make sure to add the `.rcs/` directory to your `.gitignore`.

The `.rcs` directory stores full file snapshots, meaning sensitive file contents are saved directly inside JSON history files.

Example:

```gitignore
.rcs/
```

Without this, sensitive data may accidentally be committed to Git repositories.

---

# Installation

## Via Composer

```bash
composer require yu-corder/php-rcs
```

---

# CLI Usage

The package provides a CLI tool:

```bash
php bin/rcs
```

---

## Check in a file

```bash
php bin/rcs checkin example.txt "Initial version"
```

Example output:

```text
✓ Successfully checked in.
```

---

## Check out a specific version

```bash
php bin/rcs checkout example.txt 1
```

Example output:

```text
✓ Successfully checked out to v1.
```

---

## Show revision history

```bash
php bin/rcs log example.txt
```

Example output:

```text
v1 | 2026-05-22 10:00:00 | Initial version
v2 | 2026-05-22 10:05:12 | Updated content
```

---

# PHP API Usage

```php
<?php

declare(strict_types=1);

use YuCorder\RcsManager;

$rcs = new RcsManager();

// Check in a file
$rcs->checkIn('example.txt', 'Initial version');

// Restore a previous version
$rcs->checkOut('example.txt', 1);

// Retrieve revision history
$history = $rcs->log('example.txt');

print_r($history);
```

---

# Repository Structure

`php-rcs` automatically creates a `.rcs` directory next to the target file.

```text
project/
├── example.txt
└── .rcs/
    └── example.txt.json
```

---

# Example JSON Structure

```json
{
    "file_name": "example.txt",
    "latest_version": 2,
    "history": [
        {
            "version": 1,
            "date": "2026-05-22 10:00:00",
            "comment": "Initial version",
            "content": "Hello"
        },
        {
            "version": 2,
            "date": "2026-05-22 10:05:12",
            "comment": "Updated content",
            "content": "Hello World"
        }
    ]
}
```

---

# Testing

Run PHPUnit tests:

```bash
vendor/bin/phpunit
```

---

# Code Style

Run PHP-CS-Fixer:

```bash
vendor/bin/php-cs-fixer fix
```

---

# Current Limitations

`php-rcs` currently uses full snapshot storage for simplicity.

The following features are not yet supported:

- diff-based storage
- concurrent write locking
- binary file handling
- branch/merge workflows
- object hash storage
- compression
- ignore rules

---

# Roadmap

Planned future improvements:

- [ ] diff support
- [ ] latest version retrieval
- [ ] gzip compression
- [ ] binary file support
- [ ] metadata separation
- [ ] lock file support
- [ ] `.rcsignore`
- [ ] hash-based object storage
- [ ] improved atomic writes

---

# Motivation

This project was created to explore how revision control systems work internally by implementing core concepts from scratch in pure PHP.

The goal is to keep the system simple, understandable, and extensible while learning practical software design patterns along the way.

---

# Support

- Issues: https://github.com/yu-corder/php-rcs/issues
- Source: https://github.com/yu-corder/php-rcs

---

# Author

- yu-corder
- https://yu-syumilog.com/

---

# License

MIT License