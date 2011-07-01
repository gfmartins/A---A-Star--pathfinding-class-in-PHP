<?php
include 'AStar.php';
include 'AStarWithHeap.php';

$max_x = 100;
$max_y = 100;

$map = AStar::getRandomMap($max_x, $max_y);
$map[98][98] = 0;
$map[1][2] = 0;

$path = new AStar($map, 1, 2, 98, 98);

$map2 = array();
foreach ( $map as $x => $m )
	foreach ( $m as $y => $n )
		if ( !(bool)$map[$x][$y] )
			$map2[$x][$y] = true;

$path2 = new AStarWithHeap($map2, 1, 2, 98, 98);

function count_recursive($ar)
{
	$c = 0;
	foreach ( $ar as $k => $v )
		$c += count($v);
		
	return $c;
}

$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; 
    
if ( $path->findShortestPath() )
{
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime);
	
	echo "AStar: The path was found in ".(round($totaltime, 3) * 1000)." milliseconds. Length: ". count_recursive($path->shortestPath) ."<br/>"; 
}
else
    echo "AStar: No path found!!!<br/>";

$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; 

if ( $path2->findShortestPath() )
{
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime);
	
	echo "AStar-heap: The path was found in ".(round($totaltime, 3) * 1000)." milliseconds. Length: ". count_recursive($path2->shortestPath) ."<br/>"; 
}
else
    echo "AStar-heap: No path found!!!<br/>";

echo '<table style="border:solid 1px #CCC;" cellpadding="0" cellspacing="0"><tr>';
for ($y=0; $y<$max_y; $y++)
{
    for ($x=0; $x<$max_x; $x++)
    {
        echo '<td style="font-size: 10px;width: 5px;height:5px;';
		
		if ( isset($path2->shortestPath[$x][$y]) && $path2->shortestPath[$x][$y] && isset($path->shortestPath[$x][$y]) && $path->shortestPath[$x][$y] )
			echo 'background-color: red;';
		elseif ( isset($path2->shortestPath[$x][$y]) && $path2->shortestPath[$x][$y] )
			echo 'background-color: green;';
		elseif ( isset($path->shortestPath[$x][$y]) && $path->shortestPath[$x][$y] )
			echo 'background-color: blue;';
		elseif ( $map[$x][$y] )
			echo 'background-color: grey;';
		
		echo '"></td>';
    }
    
    if ($y<$max_y)
        echo '</tr><tr>';
}
echo '</tr></table>'
?>