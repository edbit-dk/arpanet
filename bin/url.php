<?php

function parse_get($get) {
    parse_str( parse_url( $_POST[$get], PHP_URL_QUERY), $query);
    return $query;
}