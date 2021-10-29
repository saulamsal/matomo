<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\ArchiveProcessor;

use Piwik\Common;
use Piwik\Concurrency\Lock;
use Piwik\Concurrency\LockBackend;
use Piwik\Container\StaticContainer;
use Piwik\SettingsPiwik;

class ArchivingStatus
{
    const LOCK_KEY_PREFIX = 'Archiving';
    const DEFAULT_ARCHIVING_TTL = 7200; // 2 hours

    /**
     * @var LockBackend
     */
    private $lockBackend;

    protected $lockId;
    /**
     * @var int
     */
    private $archivingTTLSecs;

    /**
     * @var Lock[]
     */
    private $lockStack = [];

    public function __construct(LockBackend $lockBackend, $archivingTTLSecs = self::DEFAULT_ARCHIVING_TTL)
    {
        $this->lockBackend = $lockBackend;
        $this->archivingTTLSecs = $archivingTTLSecs;
    }

    public function archiveStarted(Parameters $params)
    {
        $this->lockId = $this->setLockId($params);
        $lock = $this->makeArchivingLock($this->lockId);
        $lock->acquireLock($this->lockId);
        return $lock;
    }


    public function getLockId()
    {
        return $this->lockId;
    }

    public function getCurrentArchivingLock()
    {
        if (empty($this->lockStack)) {
            return null;
        }
        return end($this->lockStack);
    }


    protected function setLockId($params)
    {
        $timePeriod = $params->getPeriod()->getDateStart()->toString() . '.' . $params->getPeriod()->getDateEnd()->toString();
        return $timePeriod . Rules::getDoneStringFlagFor([$params->getSite()->getId()], $params->getSegment(),
            $params->getPeriod()->getLabel(), $params->getRequestedPlugin());
    }

    private function makeArchivingLock($id)
    {
        return new Lock(StaticContainer::get(LockBackend::class), $id, $this->archivingTTLSecs);
    }
}