<?php
include 'AStar2.php';

$max_x = 7;
$max_y = 5;

$map = AStar::getEmptyMap($max_x, $max_y);
$map[3][1] = 1;
$map[3][2] = 1;
$map[3][3] = 1;

$path = new AStar($map, 1, 2, 5, 2);

$path->findShortestPath();

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