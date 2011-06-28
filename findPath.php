<?php
class findPath
{
    private $map = array();
    private $your_coords = array();
    private $destination = array();
    public $shortestPath = array();
    
    public function set_coords($your_y, $your_x, $dest_y, $dest_x)
    {
        $this -> your_coords['y'] = $your_y;
        $this -> your_coords['x'] = $your_x;
        
        $this -> destination['y'] = $dest_y;
        $this -> destination['x'] = $dest_x;
    }
    
    public function set_map($min_y, $min_x, $max_y, $max_x, $map)
    {
        $this -> map['from_y'] = $min_y;
        $this -> map['to_y'] = $max_y;
        $this -> map['from_x'] = $min_x;
        $this -> map['to_x'] = $max_x;
        
        foreach ($map as $val)
            $this -> map['map'][$val[0]][$va[1]] = true;
    }
    
    public function findShortestPath()
    {
        $old_y = $this -> your_coords['y'];
        $old_x = $this -> your_coords['x'];
        
        $this -> shortestPath[$old_y][$old_x] = true;
        
        while (true)
        {
            if ($new_y < $this -> destination['y'])
                $new_y = $old_y + 1;
            elseif ($new_y > $this -> destination['y'])
                $new_y = $old_y - 1;
            
            if ($new_x < $this -> destination['x'])
                $new_x = $old_x + 1;
            elseif ($new_x > $this -> destination['x'])
                $new_x = $old_x - 1;
            
            if (!$this -> map['map'][$new_y][$new_x])
            {
                $this -> shortestPath[$new_y][$new_x] = true;
                
                if ($new_y == $this -> destination['y'] && $new_x == $this -> destination['x'])
                    break;
                
            }
            
        }
    }
}

$path = new findPath();


$map = array( // array(y, z) - забранени блокчета
    //array(3, 5),
    //array(4, 7),
    //array(5, 2),
);

$your_y = 15; // твоята стартова y точка
$your_x = 20; // твоята стартова ь точка

$dest_y = 120; // до y който трябва да стигне
$dest_x = 115; // до x който трябва да стигне

$min_y = 0;
$min_x = 0;
$max_y = 143;
$max_x = 143;

$path -> set_coords($your_y, $your_x, $dest_y, $dest_x);
$path -> set_map($min_y, $min_x, $max_y, $max_x, $map);
$path -> findShortestPath();

echo '<table border="1"><tr>';
for ($y=$min_y; $y<=$max_y; $y++)
{
    for ($x=$min_x; $x<=$max_x; $x++)
    {
        echo '<td style="font-size: 10px;'.(($path -> shortestPath[$y][$x]) ? ' background-color: red;' : '').'">'.$y.'<br />'.$x.'</td>';
    }
    
    if ($y<$max_y)
        echo '</tr><tr>';
}
echo '</tr></table>'
?>