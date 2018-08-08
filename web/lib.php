<?php

function execCmd($argv)
{
    $map = [
        'start' => 'start_timer',
        'stop' => 'stop_timer',
        'set' => 'set_interval',
    ];

    if(array_key_exists($argv[1], ($map)))
    {
        $method = $map[$argv[1]];
        $method($argv);
    }
}

function setInterval($iton, $itoff)
{
    logmsg('set interval');
    assert(is_numeric($iton) && $iton > 0);
    assert(is_numeric($itoff) && $itoff > 0);
    
    syncState(['interval' => [$iton * 60, $itoff * 60]]);
}

function set_interval($argv)
{
    assert(count($argv) === 4);
    assert(is_numeric($argv[2]));
    assert(is_numeric($argv[3]));
    
    $iton = (int) $argv[2];
    $itoff = (int) $argv[3];
    
    setInterval($iton, $itoff);
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
        logmsg('init');
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
    $state['last_check'] = time();
    setState($state);
}


function stop_timer()
{
    $state          = getState();
    $state['timer'] = false;
    setState($state);
    turnOff();
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
    $log = date('Y-m-d H:i:s', time()) . " -- $msg";
    $log .= "\n";
    echo $log;
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
    return 'wion';
}

function getBinPath()
{
    return __DIR__ . '/../bin/';
}

function getWionStatus()
{
    $ret   = shell_exec(sprintf('%s%sst', getBinPath(), getDeviceName()));
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
//        shell_exec(sprintf('/home/hassen/config/scripts/private/bin/%s1', getDeviceName()));
        $ret   = shell_exec(sprintf('%s%s1', getBinPath(), getDeviceName()));
        logmsg('On');
        sleep(5);
    }
    syncState(['ac' => true]);
}


function turnOff()
{
    logmsg('Turn off');
    while(getWionStatus())
    {
//        shell_exec(sprintf('/home/hassen/config/scripts/private/bin/%s0', getDeviceName()));
        $ret   = shell_exec(sprintf('%s%s0', getBinPath(), getDeviceName()));
        logmsg('Off');
        sleep(5);
    }

    syncState(['ac' => false]);
}

function syncState($mstate)
{
    $state = getState();
    $ret   = array_merge($state, $mstate);
    setState($ret);

    return $ret;
}

function timerCLI($argv)
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

