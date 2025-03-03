<?php

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
ini_set('ignore_repeated_errors', $c->config['errors']['ignore_repeated_errors']); // always use TRUE
ini_set('display_errors', $c->config['errors']['display_errors']); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', $c->config['errors']['log_errors']); // Error/Exception file logging engine.
ini_set('error_log', $c->config['errors']['error_log']); // Logging file path