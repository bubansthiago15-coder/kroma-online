<?php
declare(strict_types=1);

// Load project-local settings for XAMPP. Variables already defined by Apache
// or the system take precedence over values in .env.
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile) && is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) || getenv($name) !== false) {
            continue;
        }

        if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
            $value = substr($value, 1, -1);
        } else {
            $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
        }

        putenv($name . '=' . $value);
    }
}

function config(string $key, mixed $default = null): mixed
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return $key === 'APP_DEBUG' ? filter_var($value, FILTER_VALIDATE_BOOL) : $value;
}
