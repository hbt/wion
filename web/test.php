<?php

include(__DIR__ . DIRECTORY_SEPARATOR . 'lib.php');

function main($argv)
{
    configAsserts();

    $tests = [

        'clean and init ' => function ()
        {
            @unlink(getStateFile());
            assert(!file_exists(getStateFile()));
            runMain();
            assert(file_exists(getStateFile()));
        },

        'start timer'     => function ()
        {
            runMain(['start']);
            $state = getState();
            assert($state['timer']);
            assert($state['ac']);
            assert(getWionStatus());
        },

        'stop timer'     => function ()
        {
            runMain(['stop']);
            $state = getState();
            assert(!$state['timer']);
            assert(!$state['ac']);
            assert(!getWionStatus());
        },


        'set interval'     => function ()
        {
            runMain(['set', '15', '35']);
            $state = getState();
            assert(getState()['interval'] == [15 * 60, 35 * 60]);
            
        },

    ];

    foreach($tests as $title => $test)
    {
        echo $title . "\n\n";
        echo $test();
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