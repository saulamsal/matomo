<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Concurrency\LockBackend;


use Piwik\Common;
use Piwik\Concurrency\LockBackend;
use Piwik\Db;

class FileLockBackend implements LockBackend
{
    const FILE_PATH = './tmp/';


    public function getKeysMatchingPattern($pattern)
    {
        // TODO: Implement getKeysMatchingPattern() method.
    }

    public function setIfNotExists($lockKey, $lockValue, $ttlInSeconds)
    {
        // TODO: Implement setIfNotExists() method.
    }

    public function get($key)
    {
        $fileName = md5(__FILE__ . $key);
        $file = fopen(self::FILE_PATH . $fileName . '.lock', "w+");
        if (false == flock($file, LOCK_EX | LOCK_NB)) {
            throw new Exception('Could not found lock key:'.$key);
        }

        return true;
    }

    public function deleteIfKeyHasValue($lockKey, $lockValue)
    {
        // TODO: Implement deleteIfKeyHasValue() method.
        if(!empty($lockKey))
        {
            flock($fp, LOCK_UN);
        }

    }

    public function expireIfKeyHasValue($lockKey, $lockValue, $ttlInSeconds)
    {
        // TODO: Implement expireIfKeyHasValue() method.
    }
}