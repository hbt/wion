<?php

/**
 * web UI
 * 
 */

include(__DIR__ . DIRECTORY_SEPARATOR . 'lib.php');

function main()
{

}

main();

if(array_key_exists('cmd', $_GET))
{
    $cmd = ($_GET['cmd']);
    
    $cmd = str_ireplace('%s20', ' ', $cmd);
    $args = explode(' ', $cmd);
    
    
    $argv = array_merge([''],  explode(' ', $cmd));
    timerCLI($argv);
    
}

?>
<html>
<body>

<br/>
<br/>

<a href="./index.php?cmd=start">START Timer</a>
<br/>
<br/>

<a href="./index.php?cmd=start">STOP Timer</a>
<br/>
<br/>

<a href="./index.php?cmd=set 10 10">Set 10 10</a>
<br/>
<br/>

<a href="./index.php?cmd=set 10 20">Set 10 20</a>
<br/>
<br/>


<a href="./index.php?cmd=set 20 20">Set 20 20</a>
<br/>
<br/>

</body>
</html>
