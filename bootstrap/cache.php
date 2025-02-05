<?php

use App\BaseModel;

BaseModel::setCachePath($c->config['cache']['path']); // Change for all models inheriting from BaseModel
BaseModel::setCacheDuration($c->config['cache']['duration']); // Change for all models inheriting from BaseModel