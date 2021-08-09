<?php declare(strict_types=1);

// setup demo login
if (\is_file($file = __DIR__ . '/../Web/Backend/login.tpl.php')) {
    \unlink($file);
}

\copy(__DIR__ . '/demo/login.tpl.php', $file);

echo '░░░░░░░░░░';
