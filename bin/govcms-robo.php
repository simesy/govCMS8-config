<?php

/**
 * @file
 * govCMS Robo commands wrapper.
 */

$root_path = locate_root();

if (empty($root_path)) {
    print "Unable to find vendor path for govCMS\n";
    exit(1);
}

$autoload = require_once $root_path . '/vendor/autoload.php';
if (!isset($autoload) || empty($autoload)) {
    print "Unable to find autoloader for govCMS\n";
    exit(1);
}

require_once __DIR__ . '/govcms-robo-commands.php';

/**
 * Locate root path.
 *
 * @return string
 *   Root path string.
 */
function locate_root()
{
    $possible_paths = [
        $_SERVER['PWD'],
        getcwd(),
        realpath(__DIR__ . '/../'),
        realpath(__DIR__ . '/../../../'),
    ];
    $needle = [
        'vendor/bin/govcms',
        'vendor/autoload.php',
    ];
    foreach ($possible_paths as $possible_path) {
        if ($root_path = find_directory_containing_files($possible_path, $needle)) {
            return $root_path;
        }
    }
    // bin folder is under project root.
    $needle_bin = [
        'bin/govcms',
        'vendor/autoload.php',
    ];
    foreach ($possible_paths as $possible_path) {
        if ($root_path = find_directory_containing_files($possible_path, $needle_bin)) {
            return $root_path;
        }
    }
}

/**
 * Traverses file system upwards in search of a given file.
 *
 * Begins searching for $file in $working_directory and climbs up directories
 * $max_height times, repeating search.
 *
 * @param string $working_directory
 * @param array $files
 * @param int $max_height
 *
 * @return bool|string
 *   FALSE if file was not found. Otherwise, the directory path containing the
 *   file.
 */
function find_directory_containing_files($working_directory, $files, $max_height = 10)
{
    // Find the root directory of the git repository containing govCMS.
    // We traverse the file tree upwards $max_height times until we find
    // vendor/bin/govcms.
    $file_path = $working_directory;
    for ($i = 0; $i <= $max_height; $i++) {
        if (files_exist($file_path, $files)) {
            return $file_path;
        } else {
            $file_path = realpath($file_path . '/..');
        }
    }

    return false;
}

/**
 * Determines if an array of files exist in a particular directory.
 *
 * @param string $dir
 * @param array $files
 *
 * @return bool
 */
function files_exist($dir, $files)
{
    foreach ($files as $file) {
        if (!file_exists($dir . '/' . $file)) {
            return false;
        }
    }

    return true;
}
