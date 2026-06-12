<?php

return [
    'sensitive_patterns' => [
        'SQLSTATE',
        'invalid\s+input\s+syntax',
        '\bselect\b.+\bfrom\b',
        '\binsert\s+into\b',
        'vendor\/(laravel|symfony|doctrine)',
        '\.env',
        'AKIA[0-9A-Z]{16}',
        'sk_live_[0-9a-zA-Z]+',
        'eval\s*\(',
        'shell_exec',
        'base64_decode',
    ],
];
