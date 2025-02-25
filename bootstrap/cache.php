<?php

use Lib\Cache;

Cache::status($c->config['cache']['enable']);
Cache::path($c->config['cache']['path']);
Cache::ttl($c->config['cache']['ttl']);