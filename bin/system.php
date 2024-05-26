<?php

// Function to display Message of the Day
function boot() {
    include('sys/var/boot.txt');
}

function motd() {
    require('sys/lib/motd.php');
}