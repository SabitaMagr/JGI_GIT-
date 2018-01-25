<?php

$dirList = ["procedure", "triggers", "function"];
$fileDataAll = "";


foreach ($dirList as $dir) {
    $scanned_directory = array_diff(scandir($dir), array('..', '.'));
    foreach ($scanned_directory as $filename) {
        $file = $dir . "/" . $filename;
        if (is_file($file)) {
            $fileDataAll .= file_get_contents($file)."/
            ";
        }
    }
}

$my_file = 'database/fns_trgs_procs.sql';
$handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
fwrite($handle, $fileDataAll);

