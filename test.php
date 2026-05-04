<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$var1 = '$l = 50;';
$var2 = '$L = 2;';
$var3 = '$p = 1.5;';
$var4 = '$l*$L*$p;';
$var5 = '25;';

eval($var1);
echo $l."<br>";

eval($var2);
echo $L."<br>";

eval($var3);
echo $p."<br>";

eval('$val = '.$var4.';');
echo $val."<br>";

eval('$val = '.$var5.';');
echo $val."<br>";