<?php

$dir = __DIR__ . '/classes';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

for ($i = 0; $i < 2000; $i++) {
    $content = <<<PHP
<?php
namespace Bench\Classes;

class Class$i
{
    public function __construct()
    {
        // no-op
    }
}
PHP;
    file_put_contents("$dir/Class$i.php", $content);
}

echo "Generated 2000 classes.\n";

