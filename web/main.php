#!/usr/bin/env php
<?php


include(__DIR__ . DIRECTORY_SEPARATOR . 'lib.php');

function main($argv)
{
    init();

    if(count($argv) === 1)
    {
        run();
    }
    else
    {
        execCmd($argv);
    }

    logmsg('exit');
}

main($argv);

?>