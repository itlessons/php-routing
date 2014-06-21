<?php

namespace Routing;

class Utils
{
    public static function writeFile($file, $content)
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (false === mkdir($dir, 0777, true)) {
                throw new \RuntimeException(sprintf('Unable to create the %s directory', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Unable to write in the %s directory', $dir));
        }

        $tmpFile = tempnam($dir, basename($file));

        if (false !== file_put_contents($tmpFile, $content) && rename($tmpFile, $file)) {
            chmod($file, 0666 & ~umask());
        } else {
            throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $file));
        }
    }
}