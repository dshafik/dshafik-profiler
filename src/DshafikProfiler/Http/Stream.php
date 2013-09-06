<?php
namespace DshafikProfiler\Http;

use DshafikProfiler\Http\Profiler\Profiler;

class Stream {
    static protected $registered = false;
    static protected $profiler;

    public $context = null;
    /* @var \Zend\Http\Client */
    protected $client;

    /* @var Profiler */

    /* @var array */
    protected $response = false;

    protected $startTime = 0;
    protected $endTime = 0;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        try {
            $this->client = new \Zend\Http\Client($path);
            $this->configureClient();
        } catch (\Exception $e) {
            var_dump($e); exit;
        }

        static::$profiler->startRequest($this->client->getMethod(), $path);
        $response = $this->client->send();
        static::$profiler->endRequest($response->getStatusCode() . ' ' . $response->getReasonPhrase());
        if (!$response->isSuccess()) {
            return false;
        }
        $this->response = $response->getBody();

        return true;
    }

    public function stream_read($length)
    {
        $read = substr($this->response, 0, $length);
        $this->response = substr($this->response, $length);

        return $read;
    }

    public function stream_eof()
    {
        return empty($this->response);
    }

    public function stream_flush()
    {
        return true;
    }

    public function stream_close()
    {

    }

    public function stream_start()
    {
        return [];
    }

    public function __call($what, $args)
    {
        var_dump($what, $args);
    }


    protected function getContext()
    {
        $defaults = stream_context_get_options(stream_context_get_default());
        if (isset($defaults['http'])) {
            $defaults = $defaults['http'];
        } else {
            $defaults = [];
        }

        $context = (!is_null($this->context)) ? stream_context_get_options($this->context) : [];
        if (isset($context['http'])) {
            $context = $context['http'];
        } else {
            $context = [];
        }

        $context = array_merge(
            [
                'method' => 'GET',
                'user_agent' => 'dshafik/http-profiler PHP/' .PHP_VERSION,
            ],
            $defaults,
            $context
        );

        if (isset($context['header']) && is_string($context['header'])) {
            $context['header'] = explode("\r\n", $context['header']);
        }

        return $context;
    }

    protected function configureClient()
    {
        $context = $this->getContext();

        $this->client->setMethod($context['method']);

        if (isset($context['header'])) {
            $this->client->setHeaders($context['header']);
        }

        if (isset($context['user_agent'])) {
            $this->client->getRequest()->getHeaders()->addHeaderLine('User-Agent', $context['user_agent']);
        }

        if (isset($context['content'])) {
            $this->client->setRawBody($context['content']);
        }

        if (isset($context['follow_location']) && $context['follow_location'] == 0) {
            $this->client->setOptions(['maxredirects' => 0]);
        }

        if (isset($context['max_redirects'])) {
            if ($context['max_redirects'] <= 1) {
                $this->client->setOptions(['maxredirects' => 0]);
            } else {
                $this->client->setOptions(['maxredirects' => $context['max_redirects']]);
            }
        }

        if (isset($context['timeout'])) {
            $this->client->setOptions(['timeout' => $context['timeout']]);
        }
    }

    static public function register()
    {
        if (!static::$registered) {
            foreach (['https', 'http', 'dshafik-test'] as $protocol) {
                @stream_wrapper_unregister($protocol);
                stream_wrapper_register($protocol, '\\DshafikProfiler\\Http\\Stream');
            }
        }

        static::setProfiler(\DshafikProfiler\Http\Profiler\Profiler::instance());
    }

    static public function unregister()
    {
        foreach (['https', 'http', 'dshafik-test'] as $protocol) {
            stream_wrapper_unregister($protocol);
            @stream_wrapper_restore($protocol);
        }
    }

    static public function setProfiler(Profiler $profiler)
    {
        static::$profiler = $profiler;
    }

    static public function getProfiler()
    {
        return static::$profiler;
    }
}