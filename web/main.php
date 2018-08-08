<?php

function main($argv)
{
    init();

    run();


    var_dump(getState());


    logmsg('exit');
}

function run()
{
    $state = getState();
    if(!$state['timer'])
    {
        return;
    }

    checkInterval();
}

function checkInterval()
{
    $getIntervalFromState = function ()
    {
        $state = getState()['ac'];
        if($state)
        {
            return getState()['interval'][0];
        }
        else
        {
            return getState()['interval'][1];
        }
    };

    $interval = $getIntervalFromState();
    $last     = getState()['last_check'];
    if(time() > ($last + $interval))
    {
        toggleState();
        syncState(['last_check' => time()]);
    }
}

function toggleState()
{
    logmsg('toggle');
    $state = getState()['ac'];
    if($state)
    {
        turnOff();
    }
    else
    {
        turnOn();
    }
}

function init()
{
    if(!file_exists(getStateFile()))
    {
        touch(getStateFile());
        assert(file_exists(getStateFile()));
        setState(getDefaultMap());
    }
}

function getDefaultMap()
{
    return [
        'timer'      => true,
        'interval'   => [10 * 60, 20 * 60],
        'last_check' => time(),
        'ac'         => true,
    ];
}

function start_timer()
{
    logmsg('start timer');
    $state          = getState();
    $state['timer'] = true;
    turnOn();
    $state['ac']         = true;
    $state['last_check'] = time();
    setState($state);
}


function stop_timer()
{
    $state          = getState();
    $state['timer'] = false;
    setState($state);
}

function getState()
{
    $ret = unserialize(file_get_contents(getStateFile()));

//    assert(count(array_keys(getDefaultMap())) <= count(array_keys($ret)));

    return $ret;
}

function setState($state)
{
    assert(file_exists(getStateFile()));
    file_put_contents(getStateFile(), serialize($state));
}

function getStateFile()
{
    return '/tmp/ac-state.ser.txt';
}

function logmsg($msg)
{
    echo date('Y-m-d H:i:s', time()) . " -- $msg";
    echo "\n";
}

function getLogFile()
{
    return '/tmp/ac.log';
}

function viewLog()
{
    echo file_get_contents('/tmp/ac.log');
}

function getDeviceName()
{
    return 'lt';
}

function getWionStatus()
{
    $ret   = shell_exec(sprintf('/home/hassen/config/scripts/private/bin/%sst', getDeviceName()));
    $isOn  = stripos($ret, 'rw_byte: 1') !== false;
    $isOff = stripos($ret, 'rw_byte: 0') !== false;

    logmsg('Status ' . $isOn);
    if($isOn)
    {
        logmsg('Status 1 ');

        return true;
    }
    else if($isOff)
    {
        logmsg('Status 0 ');

        return false;
    }
}

function turnOn()
{
    logmsg('Turn on');
    while(!getWionStatus())
    {
        shell_exec(sprintf('/home/hassen/config/scripts/private/bin/%s1', getDeviceName()));
        logmsg('On');
        sleep(5);
    }
    syncState(['ac' => false]);
}


function turnOff()
{
    logmsg('Turn off');
    while(getWionStatus())
    {
        shell_exec(sprintf('/home/hassen/config/scripts/private/bin/%s0', getDeviceName()));
        logmsg('Off');
        sleep(5);
    }

    syncState(['ac' => true]);
}

function syncState($mstate)
{
    $state = getState();
    $ret   = array_merge($state, $mstate);
    setState($ret);

    return $ret;
}

main($argv);

?>