<?php
$base = '/home/1584168.cloudwaysapps.com/njfxrssvcj/public_html';
$files = [
    $base . '/contact.html',
    $base . '/boat-summer-service-fresno-ca.html',
    $base . '/navigation.html',
    $base . '/services/boat-summer-service-fresno-ca.html',
];

$results = [];
foreach ($files as $file) {
    if (!file_exists($file)) {
        $results[] = ['file' => basename($file), 'path' => $file, 'status' => 'NOT FOUND'];
        continue;
    }
    $content = file_get_contents($file);
    if ($content === false) {
        $results[] = ['file' => basename($file), 'path' => $file, 'status' => 'CANNOT READ'];
        continue;
    }

    // Check if we own the file (can unlink + recreate for full ownership transfer)
    $stat = stat($file);
    $myUid = posix_getuid();
    $canUnlink = ($stat['uid'] === $myUid);

    if ($canUnlink) {
        // We own it — delete and recreate to ensure clean ownership
        unlink($file);
    }
    // Write (either recreating or overwriting group-writable file)
    $written = file_put_contents($file, $content);
    if ($written !== false) {
        chmod($file, 0664);
    }

    $newStat = stat($file);
    $ownerInfo = posix_getpwuid($newStat['uid']);
    $results[] = [
        'file'   => basename($file),
        'path'   => $file,
        'status' => ($written !== false) ? 'FIXED' : 'WRITE FAILED',
        'owner'  => $ownerInfo['name'] ?? 'uid:' . $newStat['uid'],
        'bytes'  => $written
    ];
}
header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
