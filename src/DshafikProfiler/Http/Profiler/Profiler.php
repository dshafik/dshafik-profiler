<?php

namespace DshafikProfiler\Http\Profiler;

use Zend\Db\Adapter\Profiler\ProfilerInterface;

class Profiler implements ProfilerInterface
{
    static $instance = false;

    /**
     * Logical OR these together to get a proper request type filter
     */
    const GET = 1;
    const POST = 2;
    const PUT = 4;
    const DELETE = 8;
    const HEAD = 16;
    const OPTIONS = 32;
    const PATCH = 64;

    /**
     * @var array
     */
    protected $profiles = array();

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var int
     */
    protected $requestTypes;

    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
        $this->requestTypes = 127;
    }

    public function enable()
    {
        $this->enabled = true;
        return $this;
    }

    public function disable()
    {
        $this->enabled = false;
        return $this;
    }

    public function setFilterRequestType($requestTypes = null)
    {
        $this->requestTypes = $requestTypes;
        return $this;
    }

    public function getFilterRequestType()
    {
        return $this->requestTypes;
    }

    public function startRequest($method, $url)
    {
        if (!$this->enabled) {
            return null;
        }

        $profile = new Request($method, $url);
        $this->profiles[] = $profile;
        $profile->start();

        end($this->profiles);
        return key($this->profiles);
    }

    public function endRequest($status)
    {
        if (!$this->enabled) {
            return false;
        }

        $request = end($this->profiles);
        $request->end();
        $request->setStatus($status);

        return true;
    }

    public function getRequestProfiles($requestTypes = null)
    {
        $profiles = array();

        if (count($this->profiles)) {
            foreach ($this->profiles as $id => $profile) {
                if ($requestTypes === null) {
                    $requestTypes = $this->requestTypes;
                }

                if ($profile->getRequestMethod() & $requestTypes) {
                    $profiles[$id] = $profile;
                }
            }
        }

        return $profiles;
    }

    public function profilerStart($target)
    {
        $method = $target->getRequestMethod();
        $url = $target->getUrl();
        $this->startRequest($method, $url);
    }

    public function profilerFinish()
    {
        $this->endRequest();
    }

    static public function instance($enabled = true)
    {
        if (!static::$instance) {
            $self = __CLASS__;
            static::$instance = new $self($enabled);
        }

        return static::$instance;
    }
}
