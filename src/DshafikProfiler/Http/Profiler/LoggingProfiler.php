<?php
namespace DshafikProfiler\Http\Profiler;

use Zend\Log\Logger;

class LoggingProfiler extends Profiler
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var int
     */
    protected $priority = Logger::DEBUG;
    /**
     * How many request profiles could be stored in memory.
     * Useful for long-running scripts with tons of request that can take all the memory.
     * -1 - store all profiles
     * 0 - do not store any profiles
     * N > 0 - store N profiles, discard when there are more than N
     *
     * @var int
     */
    protected $maxProfiles = 100;
    /**
     * Query parameters to log on request start
     *
     * @var array
     * @see Query
     */
    protected $parametersStart = array('method', 'url');
    /**
     * Query parameters to log on request finish
     *
     * @var array
     * @see Query
     */
    protected $parametersFinish = array('status', 'elapsed');

    public function __construct(Logger $logger, $enabled = true, array $options = array())
    {
        parent::__construct($enabled);
        $this->setLogger($logger);

        if (isset($options['priority'])) $this->setPriority($options['priority']);
        if (isset($options['maxProfiles'])) $this->setMaxProfiles($options['maxProfiles']);
        if (isset($options['parametersStart'])) $this->setParametersStart($options['parametersStart']);
        if (isset($options['parametersFinish'])) $this->setParametersFinish($options['parametersFinish']);
    }

    public function profilerStart($target)
    {
        parent::profilerStart($target);

        /** @var Request $lastRequest*/
        $lastRequest = end($this->profiles);
        $this->getLogger()->log(
            $this->getPriority(),
            'Request started',
            array_intersect_key($lastRequest->toArray(), array_flip($this->getParametersStart()))
        );
    }

    public function profilerFinish()
    {
        parent::profilerFinish();

        /** @var Request $lastRequest */
        $lastRequest = end($this->profiles);
        $this->getLogger()->log(
            $this->getPriority(),
            'Request finished',
            array_intersect_key($lastRequest->toArray(), array_flip($this->getParametersFinish()))
        );

        $maxProfiles = $this->getMaxProfiles();
        if ($maxProfiles > -1) {
            if (count($this->profiles) > $maxProfiles) $this->profiles = array();
        }
    }

    /**
     * @param int $level
     */
    public function setPriority($level)
    {
        $this->priority = $level;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param \Zend\Log\Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Zend\Log\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param int $maxProfiles
     */
    public function setMaxProfiles($maxProfiles)
    {
        $this->maxProfiles = $maxProfiles;
    }

    /**
     * @return int
     */
    public function getMaxProfiles()
    {
        return $this->maxProfiles;
    }

    /**
     * @param array $parametersFinish
     */
    public function setParametersFinish(array $parametersFinish)
    {
        $this->parametersFinish = $parametersFinish;
    }

    /**
     * @return array
     */
    public function getParametersFinish()
    {
        return $this->parametersFinish;
    }

    /**
     * @param array $parametersStart
     */
    public function setParametersStart(array $parametersStart)
    {
        $this->parametersStart = $parametersStart;
    }

    /**
     * @return array
     */
    public function getParametersStart()
    {
        return $this->parametersStart;
    }
}
