<?php

/**
 * web UI
 * 
 * php -S 0.0.0.0:8989 index.php
 * 
 * 
 */

include(__DIR__ . DIRECTORY_SEPARATOR . 'lib.php');

function main()
{

}

main();

var_dump(getState());
if(array_key_exists('cmd', $_GET))
{
    $cmd = ($_GET['cmd']);
    
    $cmd = str_ireplace('%s20', ' ', $cmd);
    $args = explode(' ', $cmd);
    
    
    $argv = array_merge(['main.php'],  explode(' ', $cmd));
    timerCLI($argv);
    
}

?>
<html>
<body style="background-color: black; color: white">

<h1>
    
<br/>
<br/>

<a href="./index.php?cmd=start">START Timer</a>
<br/>
<br/>

<a href="./index.php?cmd=stop">STOP Timer</a>
<br/>
<br/>

<a href="./index.php?cmd=set 10 10">Set 10 10</a>
<br/>
<br/>

<a href="./index.php?cmd=set 10 20">Set 10 20</a>
<br/>
<br/>


<a href="./index.php?cmd=set 20 10">Set 20 10</a>
<br/>
<br/>
    
</h1>

</body>
</html>
