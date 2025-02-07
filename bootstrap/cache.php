<?php

use Lib\Cache;

Cache::path($c->config['cache']['path']);
Cache::ttl($c->config['cache']['ttl']);