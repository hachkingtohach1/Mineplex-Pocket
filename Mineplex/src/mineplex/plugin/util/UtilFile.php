<?php

namespace mineplex\plugin\util;

class UtilFile
{
    public static function deleteDir($dirPath)
    {
        if (! is_dir($dirPath))
        {
            unlink($dirPath);
            return;
        }

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')
        {
            $dirPath .= '/';
        }

        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file)
        {
            self::deleteDir($file);
        }
        rmdir($dirPath);
    }
}
