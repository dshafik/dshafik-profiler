<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'DshafikProfiler\\Http' => function ($sm = null) {
                $collector = new \DshafikProfiler\Http\Profiler\HttpCollector();
                $collector->setProfiler($sm->get('DshafikProfiler\\Http\\Profiler'));
                return $collector;
            },
            'DshafikProfiler\\Http\Profiler' => function ($sm = null) {
                return \DshafikProfiler\Http\Profiler\Profiler::instance();
            }
        ),
    ),
    'zenddevelopertools' => array(
        'profiler' => array(
            'collectors' => array('http' => 'DshafikProfiler\\Http'),
        ),
        'toolbar' => array(
            'entries' => array(
                'http' => 'toolbar/http',
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'dshafik-profiler' => __DIR__ . '/../view',
        ),
    ),
);