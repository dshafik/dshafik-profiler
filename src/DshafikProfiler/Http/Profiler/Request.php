<?php

namespace DshafikProfiler\Http\Profiler;

use DshafikProfiler\Http\Profiler\Profiler;

class Request
{
    protected $method = '';
    protected $url = '';
    protected $status;
    protected $startTime = null;
    protected $endTime = null;

    public function __construct($method, $url)
    {
        $this->method = $method;
        $this->url = $url;
    }

    public function start()
    {
        $this->startTime = microtime(true);
        return $this;
    }

    public function end()
    {
        $this->endTime = microtime(true);
        return $this;
    }

    public function hasEnded()
    {
        return ($this->endTime !== null);
    }

    public function getElapsedTime()
    {
        if (!$this->hasEnded()) {
            return false;
        }
        return $this->endTime - $this->startTime;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getRequestMethod()
    {
        switch ($this->method) {
            case "GET":
                $method = Profiler::GET;
                break;
            case "POST":
                $method = Profiler::POST;
                break;
            case "PUT":
                $method = Profiler::PUT;
                break;
            case "DELETE":
                $method = Profiler::DELETE;
                break;
            case "HEAD":
                $method = Profiler::HEAD;
                break;
            case "PATCH":
                $method = Profiler::PATCH;
                break;
            case "OPTIONS":
                $method = Profiler::OPTIONS;
                break;
            default:
                $method = Profiler::GET;
        }

        return $method;
    }

    public function toArray()
    {
        return array(
            'type'    => $this->method,
            'method'    => $this->method,
            'url'     => $this->url,
            'status'     => $this->status,
            'start'   => $this->startTime,
            'end'     => $this->endTime,
            'elapsed' => $this->getElapsedTime(),
        );
    }
}
