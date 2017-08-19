<?php

define('LOCAL_VERSION_INFO', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'version.json');

Updater::setName('main');
Updater::setLocalPath(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '.');
Updater::setRemoteRepository('http://127.0.0.1/updates');

class Updater
{
    public static function setName($name)
    {
        Updater::$updateName = $name;
    }

    public static function setLocalPath($path)
    {
        Updater::$updateLocalPath = $path;
    }

    public static function setRemoteRepository($path)
    {
        Updater::$updateRemoteRepository = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    const UPDATER_SUCCESSFUL = 3;
    const UPDATER_FAILED = 2;
    const UPDATER_NEW_VERSION = 1;
    const UPDATER_NO_NEW_VERSION = 0;
    private static $updateName;
    private static $updateLocalPath;
    private static $updateRemoteRepository;
    private static $cachedLatestVersionInfo;

    public static function getCurrentVersion()
    {
        if (!is_file(LOCAL_VERSION_INFO)) {
            $info = array('current_version' => 0);
            file_put_contents(LOCAL_VERSION_INFO, json_encode($info));

            return 0;
        } else {
            $info = json_decode(file_get_contents(LOCAL_VERSION_INFO));

            return $info->current_version;
        }
    }

    public static function isUpdateAvailable()
    {
        $tempUpdateName = Updater::$updateName;
        if (Updater::$cachedLatestVersionInfo === null) {
            $tempData = json_decode(file_get_contents(Updater::$updateRemoteRepository . 'latest-versions.json'));
            if (isset($tempData->$tempUpdateName)) {
                Updater::$cachedLatestVersionInfo = $tempData->$tempUpdateName;
            }
        }
        if (Updater::$cachedLatestVersionInfo != null && Updater::$cachedLatestVersionInfo->version_code > Updater::getCurrentVersion()) {
            return true;
        } else {
            return false;
        }
    }

    public static function attemptUpdate()
    {
        if (Updater::isUpdateAvailable()) {
            echo '<table><thead><tr><th>Local File</th><th>Remote File</th></tr></thead><tbody>';
            foreach (Updater::$cachedLatestVersionInfo->files_to_add as $file) {
                $file = preg_replace('#$\\\\|$/|\.\.#', '', $file);
                $remotePath = Updater::$updateRemoteRepository . Updater::$updateName . '/' . $file;
                $localPath = preg_replace('#\\\\|/#', DIRECTORY_SEPARATOR, Updater::$updateLocalPath . DIRECTORY_SEPARATOR . $file);
                echo '<tr><td><code><u>' . $localPath . '</u></code></td><td><code><u>' . $remotePath . '</u></code></td></tr>';
                file_put_contents($localPath, file_get_contents($remotePath));
            }
            echo '</tbody></table>';
        }
    }

}
