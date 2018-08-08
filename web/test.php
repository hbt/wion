#!/usr/bin/env php
<?php

include(__DIR__ . DIRECTORY_SEPARATOR . 'lib.php');

function main($argv)
{
    configAsserts();

    $oneTest = '';
    
    $tests   = [

        'clean and init ' => function ()
        {
            @unlink(getStateFile());
            assert(!file_exists(getStateFile()));
            runMain();
            assert(file_exists(getStateFile()));
        },

        'on'            => function ()
        {
            turnOn();
            assert(getState()['ac']);
        },

        'curl'            => function ()
        {
            echo shell_exec('curl http://localhost:8989/index.php?cmd=set%s2030%s2030');
            assert(getState()['interval'] == [30 * 60, 30 * 60]);
        },

        'curl2'            => function ()
        {
            echo shell_exec('curl http://localhost:8989/index.php?cmd=start');
            assert(getState()['ac']);
        },

        'start timer'     => function ()
        {
            runMain(['start']);
            $state = getState();
            assert($state['timer']);
            assert($state['ac']);
            assert(getWionStatus());
        },

        'stop timer'      => function ()
        {
            runMain(['stop']);
            $state = getState();
            assert(!$state['timer']);
            assert(!$state['ac']);
            assert(!getWionStatus());
        },


        'set interval'    => function ()
        {
            runMain(['set', '15', '35']);
            $state = getState();
            assert(getState()['interval'] == [15 * 60, 35 * 60]);
        },


    ];

    if($oneTest)
    {
        $tests[$oneTest]();
    }
    else
    {

        foreach($tests as $title => $test)
        {
            echo $title . "\n\n";
            echo $test();
        }
    }
}

function runMain($args = [])
{
    echo shell_exec(__DIR__ . DIRECTORY_SEPARATOR . 'main.php ' . implode(' ', $args));
}

function configAsserts()
{
    assert_options(ASSERT_ACTIVE, true);
    assert_options(ASSERT_BAIL, true);
    ini_set('assert.active', 1);
    ini_set('assert.bail', 1);
    ini_set('assert.warning', 1);
    ini_set('assert.exception', 1);
    ini_set('zend.assertions', 1);
}

main($argv);

?>