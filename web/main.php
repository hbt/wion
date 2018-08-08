#!/usr/bin/env php
<?php

/**
 * controls timer. Every X minutes, wion device is On then off for Y minutes
 * 
 * Run it with cron: "* * * * * /home/hassen/wion/web/main.php >> /tmp/ac.log"
 * 
 * Change wion device in getDeviceName
 * 
 * 
 * cmds are:
 * - start -- starts timer
 * - stop -- stops timer
 * - set [interval X on, interval Y off] -- sets interval 
 */

include(__DIR__ . DIRECTORY_SEPARATOR . 'lib.php');

timerCLI($argv);

?>