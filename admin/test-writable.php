<?php
$base = dirname(__DIR__);
$pages = ['contact.html','about.html','index.html','services.html','services/boat-summer-service-fresno-ca.html'];
foreach($pages as $p) {
    $path = $base . '/' . $p;
    echo $p . ': ' . (is_writable($path) ? 'WRITABLE' : 'NOT WRITABLE') . ' (exists: ' . (file_exists($path) ? 'yes' : 'no') . ")\n";
}
