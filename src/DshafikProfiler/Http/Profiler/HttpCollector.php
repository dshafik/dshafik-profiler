<?php
/**
 * Zend Developer Tools for Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/ZendDeveloperTools for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 */

namespace DshafikProfiler\Http\Profiler;

use Serializable;
use Zend\Mvc\MvcEvent;
use ZendDeveloperTools\Collector\CollectorInterface;
use ZendDeveloperTools\Collector\AutoHideInterface;
/**
 * HTTP Data Collector.
 *
 */
class HttpCollector implements CollectorInterface, AutoHideInterface, Serializable
{
    /**
     * @var Profiler
     */
    protected $profiler;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'http';
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 10;
    }

    /**
     * @inheritdoc
     */
    public function collect(MvcEvent $mvcEvent)
    {
        return;
    }

    /**
     * Has the collector access to Bjy's Db Profiler?
     *
     * @return bool
     */
    public function hasProfiler()
    {
        return isset($this->profiler);
    }

    /**
     * Returns Bjy's Db Profiler
     *
     * @return Profiler
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     * Sets Bjy's Db Profiler
     *
     * @param  Profiler $profiler
     * @return self
     */
    public function setProfiler(Profiler $profiler)
    {
        $this->profiler = $profiler;

        return $this;
    }

    /**
     * Returns the number of requests sent
     *
     * You can use the constants in the Profiler class to specify
     * what kind of requests you want to get, e.g. Profiler::GET.
     *
     * @param  integer $mode
     * @return self
     */
    public function getRequestCount($mode = null)
    {
        return count($this->profiler->getRequestProfiles($mode));
    }

    /**
     * Returns the total time the HTTP requests took to execute.
     *
     * You can use the constants in the Profiler class to specify
     * what kind of requests you want to get, e.g. Profiler::GET.
     *
     * @param  integer $mode
     * @return float|integer
     */
    public function getRequestTime($mode = null)
    {
        $time = 0;

        foreach ($this->profiler->getRequestProfiles($mode) as $request) {
            $time += $request->getElapsedTime();
        }

        return $time;
    }

    /**
     * @see \Serializable
     */
    public function serialize()
    {
        return serialize($this->profiler);
    }

    /**
     * @see \Serializable
     */
    public function unserialize($profiler)
    {
        $this->profiler = unserialize($profiler);
    }

    /**
     * Returns true if the collector can be hidden, because it is empty.
     *
     * @return bool
     */
    public function canHide()
    {
        return !$this->profiler->getRequestProfiles();
    }
}