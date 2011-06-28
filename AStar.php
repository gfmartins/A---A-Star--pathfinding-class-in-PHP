<?php
class AStar
{
	private $map = array();
	private $startX, $startY, $endX, $endY;
    public $shortestPath = array();
    
    private $openList = array(), $closedList = array();
    private $currentX, $currentY, $currentG;
    
    public function __construct($map, $startX, $startY, $endX, $endY)
    {
    	$this->map = $map;
    	
    	$this->startX = $startX;
    	$this->startY = $startY;
    	
    	$this->endX = $endX;
    	$this->endY = $endY;
    }
    
    public static function getEmptyMap($width, $height)
    {
    	$map = array();
    	
    	for ( $x = 0; $x < $width; $x++ )
    		for ( $y = 0; $y < $height; $y++ )
    			$map[$x][$y] = 0;
 			
		return $map;
    }
    
    public static function getRandomMap($width, $height)
    {
    	$map = array();
    	
    	for ( $x = 0; $x < $width; $x++ )
    		for ( $y = 0; $y < $height; $y++ )
    			$map[$x][$y] = !(bool)rand(0, 8);
 			
		return $map;
    }
    
    public function findShortestPath()
    {
   		$this->moveTo($this->startX, $this->startY, 0, false);
   		$this->addToClosedList($this->startX, $this->startY);
   		
   		while ( !$this->areYouThereYet() )
   		{
   			$this->addNeighboursToList($this->currentX, $this->currentY);
		 	  
   			$next = $this->getLowestCost();
   			
   			if ( $next['x'] < 0 )
   				break;
   			
   			$this->moveTo($next['x'], $next['y'], $next['g']);
		   
   			//echo "Moving to: ". $next['x'] ."x". $next['y'] ."<br />";
		}
		
		//echo 'Tracing back and logging path<br />';
		$this->traceBack($this->endX, $this->endY);
		
		//var_dump($this->shortestPath);
		//echo "Wow, I'm here :P<br />";
    }
    
    private function traceBack($x, $y)
    {
		//echo "Tracing ". $x . "x". $y ." back to ". $this->closedList[$x][$y]['x'] . "x" . $this->closedList[$x][$y]['y']. "<br />";

		while ( true )
		{
    		$this->shortestPath[$x][$y] = true;
    	
    		if ( !isset($this->closedList[$x][$y]) || $this->closedList[$x][$y] === false )
    			return;

			$cx = $this->closedList[$x][$y]['x'];
			$y = $this->closedList[$x][$y]['y'];
			$x = $cx;
		}
			
   		//return $this->traceBack($this->closedList[$x][$y]['x'], $this->closedList[$x][$y]['y']);
    }
    
    private function moveTo($x, $y, $g = 0, $toClosed = true)
    {
    	$this->currentX = $x;
    	$this->currentY = $y;
    	$this->currentG = $g;
    	
    	if ( $toClosed )
   		 	$this->moveToClosedList($x, $y);
    }
    
    private function areYouThereYet()
    {
    	return ( $this->currentX == $this->endX && $this->currentY == $this->endY );
    }
    
    private function getLowestCost()
    {
    	$lowestF = -1;
    	$lowestG = -1;
    	$lowestX = -1;
    	$lowestY = -1;
    	
   		foreach ( $this->openList as $x => $ar )
   		{
   			//if ( abs($x - $this->currentX) > 1 )
   			//	continue;
   			
   			foreach ( $ar as $y => $parent )
			{
				//if ( abs($y - $this->currentY) > 1 )
				//	continue;
				
				$cost = $parent['f'];
				//$cost = $this->getCost($x, $y);
				
				if ( $cost['f'] <= $lowestF || $lowestF == -1 )
				{
					$lowestF = $cost['f'];
					$lowestG = $cost['g'];
					$lowestX = $x;
					$lowestY = $y;
				}
				
  				//echo $x. "x" .$y. " cost: ". $f ."<br />";
  			}
		}
  			
		//echo "Lowest cost: ". $lowestF ." on ". $lowestX ."x". $lowestY ."<br />";
		
		return array('x' => $lowestX, 'y' => $lowestY, 'g' => $lowestG);
    }
    
    /**
	* F = G + H
	*
	* G = the movement cost to move from the starting point A to a given square on the grid, following the path generated to get there. 
	* H = the estimated movement cost to move from that given square on the grid to the final destination.
	* 
	* F = total cost
	*/
    private function getCost($x, $y)
    {
  		$G = $this->getG($this->startX, $this->startY, $x, $y);
   			
		//$H = ( sqrt(pow( $x - $this->endX, 2 ) + pow($y - $this->endY, 2) ) ) * 10;
        $H = $this->getG($x, $y, $this->endX, $this->endY) * 0.9;
        
		return array('f' => $G + $H, 'g' => $G);
    }
    
    private function getGThrough($x1, $y1, $x, $y)
    {
    	return $this->getG($this->startX, $this->startY, $x1, $y1) + $this->getG($x1, $y1, $x, $y);
    }
    
    private function getG($startx, $starty, $x, $y)
    {
    	$g = 0;
    	$cx = $startx;
    	$cy = $starty;
    	
        $xDelta = abs($startx - $x);
        $yDelta = abs($starty - $y);
        
        $minDelta = min($xDelta, $yDelta);
        
        $g = $minDelta * 14;
        $g += ( $xDelta - $minDelta ) * 10;
        $g += ( $yDelta - $minDelta ) * 10;
        
   		return $g;
    }
    
    private function addToOpenList($x, $y, $parent = false, $overwrite = false)
    {
    	if ( isset($this->openList[$x][$y]) )
    	{
    		if ( $overwrite )
    			$this->openList[$x][$y] = $parent;
    		
			return true;
    	}
   		
	    $this->openList[$x][$y] = $parent;
    	
    	return false;
    }
    private function removeFromOpenList($x, $y)
    {
    	unset($this->openList[$x][$y]);
    	
    	//echo "Removed from list: ". $x ."x". $y ."<br />";
    }
    
    private function addToClosedList($x, $y, $parent = false)
    {
    	$this->closedList[$x][$y] = $parent;

    	//echo "Added to closed list: ". $x ."x". $y ."<br />";
    }
    private function removeFromClosedList($x, $y)
    {
    	unset($this->closedList[$x][$y]);
    	
    	//echo "Removed from closed list: ". $x ."x". $y ."<br />";
    }
    
    private function moveToClosedList($x, $y)
    {
    	//echo "Moving $x $y to closed list with parent {$this->openList[$x][$y]['x']}x{$this->openList[$x][$y]['y']}";
    	
   		if ( isset($this->openList[$x][$y]) )
   			$parent = $this->openList[$x][$y];
		else
			$parent = false;
			
    	$this->removeFromOpenList($x, $y);
    	$this->addToClosedList($x, $y, $parent);
    }
    
    public function isObstackle($x, $y)
    {
    	return !isset($this->map[$x][$y]) || (bool)$this->map[$x][$y];
    }
    
    public function isClosed($x, $y)
    {
    	return isset($this->closedList[$x][$y]);
    }
    
    private function addNeighboursToList($x, $y)
    {
    	for ( $x1 = $x - 1; $x1 <= $x + 1; $x1++ )
    		for ( $y1 = $y - 1; $y1 <= $y + 1; $y1++ )
    		{
    			if ( $this->isObstackle($x1, $y1) )
   					continue;
   					
				if ( $y1 != $y && $x1 != $x )
				{
					if ( $x1 > $x && $this->isObstackle($x + 1, $y) )
						continue;
					if ( $x1 < $x && $this->isObstackle($x - 1, $y) )
						continue;
					if ( $y1 > $y && $this->isObstackle($x, $y + 1) )
						continue;
					if ( $y1 < $y && $this->isObstackle($x, $y - 1) )
						continue;
				}
    			
    			if ( !$this->isClosed($x1, $y1) )
    			{
    				$alreadyAdded = $this->addToOpenList($x1, $y1, array('x' => $x, 'y' => $y, 'f' => $this->getCost($x, $y)));
    				
    				if ( $alreadyAdded )
    				{
    					if ( $this->getGThrough($this->currentX, $this->currentY, $x1, $y1) < $this->getGThrough($this->openList[$x1][$y1]['x'], $this->openList[$x1][$y1]['y'], $x1, $y1) )
    					{
    						//echo $this->getGThrough($this->currentX, $this->currentY, $x1, $y1). ' == ' .$this->getGThrough($this->openList[$x1][$y1]['x'], $this->openList[$x1][$y1]['y'], $x1, $y1);
    						//echo "Switching ". $x1 ."x". $y1 ." to point to ". $this->currentX ."x". $this->currentY ."<br />";
    						$this->addToOpenList($x1, $y1, array('x' => $this->currentX, 'y' => $this->currentY, 'f' => $this->getCost($x, $y)), true);
    					}
					}
    			}
			}
	}
}
?>