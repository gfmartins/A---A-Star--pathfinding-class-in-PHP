<?php
include 'AStar.php';

$max_x = 144;
$max_y = 144;

$map = AStar::getRandomMap($max_x, $max_y);
$map[133][117] = 0;
$map[1][2] = 0;

$path = new AStar($map, 1, 2, 133, 117);

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
   echo "The path was found in ".(round($totaltime, 3) * 1000)." milliseconds"; 
}
else
    echo "No path found!!!";
echo '<table style="border:solid 1px #CCC;" cellpadding="0" cellspacing="0"><tr>';
for ($y=0; $y<$max_y; $y++)
{
    for ($x=0; $x<$max_x; $x++)
    {
        echo '<td style="font-size: 10px;width: 5px;height:5px;';
		
		if ( isset($path->shortestPath[$x][$y]) && $path->shortestPath[$x][$y] )
			echo 'background-color: red;';
		elseif ( $map[$x][$y] )
			echo 'background-color: grey;';
		
		echo '"></td>';
    }
    
    if ($y<$max_y)
        echo '</tr><tr>';
}
echo '</tr></table>'
?>