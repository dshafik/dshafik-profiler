# DshafikProfiler

A ZendDeveloperToolbar profiler for HTTP requests. This profiler
will capture all requests made via the HTTP/HTTPS streams (and
not via Zend\Http\Client).

## Installation

This package can be installed using composer.

Add the following to your `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "http://github.com/dshafik/dshafik-profiler"
    }
],
"require": {
    "php": ">=5.3.3",
    "zendframework/zendframework": "2.*",
    "zendframework/zend-developer-tools": "dev-master",
    "dshafik/dshafik-profiler": "dev-master"
}
```
## Configuration & Usage

To use the profiler, you must edit your `application.config.php` to add:

```php
if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    $config['modules'][] = 'ZendDeveloperTools';
    $config['modules'][] = 'DshafikProfiler';
    \DshafikProfiler\Http\Stream::register();
}
```

This enables the ZendDeveloperTools and DshafikProfiler modules for localhost only (e.g. dev),
and then registers the `\DshafikProfiler\Http\Stream` wrapper.
