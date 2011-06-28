<?php
include 'AStar.php';

$max_x = 144;
$max_y = 144;

$map = AStar::getRandomMap($max_x, $max_y);
$map[125][9] = 0;
$map[1][2] = 0;

$path = new AStar($map, 1, 2, 125, 9);

    $mtime = microtime(); 
    $mtime = explode(" ",$mtime); 
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime; 
    
$path->findShortestPath();

$mtime = microtime(); 
   $mtime = explode(" ",$mtime); 
   $mtime = $mtime[1] + $mtime[0]; 
   $endtime = $mtime; 
   $totaltime = ($endtime - $starttime); 
   echo "The path was found in ".(round($totaltime, 3) * 1000)." milliseconds"; 

echo '<table border="1"><tr>';
for ($y=0; $y<$max_y; $y++)
{
    for ($x=0; $x<$max_x; $x++)
    {
        echo '<td style="font-size: 10px;width: 20px;height:20px;';
		
		if ( isset($path->shortestPath[$x][$y]) && $path->shortestPath[$x][$y] )
			echo 'background-color: red;';
		elseif ( $map[$x][$y] )
			echo 'background-color: grey;';
		
		echo '">'.$x.'<br />'.$y.'</td>';
    }
    
    if ($y<$max_y)
        echo '</tr><tr>';
}
echo '</tr></table>'
?>