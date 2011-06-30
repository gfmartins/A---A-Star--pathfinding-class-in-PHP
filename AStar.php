<?php
class AStar
{
	const HV_COST 					= 10;
	const D_COST					= 14;
	const ALLOW_DIAGONAL 			= true;
	const ALLOW_DIAGONAL_CORNERING 	= false;
	
    private $map = array();
	private $startX, $startY, $endX, $endY;
    public $shortestPath = array();
    
    private $openList = array(), $closedList = array();
    
    public function __construct($map, $startX, $startY, $endX, $endY)
    {
    	$this->map = $map;
    	
    	$this->startX = $startX;
    	$this->startY = $startY;
    	
    	$this->endX = $endX;
    	$this->endY = $endY;
        
        $H = $this->distBetween($startX, $startY, $endX, $endY);
        $this->addToOpenList($startX, $startY, 0, $H, -1, -1);
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
    			$map[$x][$y] = !(bool)rand(0, 2);
 			
		return $map;
    }
    
    private function distBetween($startx, $starty, $x, $y)
    {    	
        $xDelta = abs($startx - $x);
        $yDelta = abs($starty - $y);
        
        $minDelta = min($xDelta, $yDelta);
        
        if ( self::ALLOW_DIAGONAL )
        	$g = $minDelta * 14;
        else
        	$minDelta = 0;
		$g += ( $xDelta - $minDelta ) * self::HV_COST;
        $g += ( $yDelta - $minDelta ) * self::HV_COST;
                        
   		return $g;
    }
    
    public function findShortestPath()
    {
        $found = false;
   		while ( count($this->openList) != 0 )
        {
            $node = $this->getLowest();
            $x = $node['x'];
            $y = $node['y'];
            
            if ( $x == -1 )
                break;
            
            $this->moveToClosedList($x, $y);
            
            if ( $x == $this->endX && $y == $this->endY )
            {
                $found = true;
                break;
            }
            
            // Foreach neighbour
            for ( $x1 = $x - 1; $x1 <= $x + 1; $x1++ )
        		for ( $y1 = $y - 1; $y1 <= $y + 1; $y1++ )
        		{
        			if ( $this->isObstacle($x1, $y1) )
       					continue;
        			
        			if ( $this->isClosed($x1, $y1) )
                        continue;
       					
    				if ( !self::ALLOW_DIAGONAL_CORNERING && $y1 != $y && $x1 != $x )
    				{
    					if ( $x1 > $x && $this->isObstacle($x + 1, $y) )
    						continue;
    					if ( $x1 < $x && $this->isObstacle($x - 1, $y) )
    						continue;
    					if ( $y1 > $y && $this->isObstacle($x, $y + 1) )
    						continue;
    					if ( $y1 < $y && $this->isObstacle($x, $y - 1) )
    						continue;
    				}
                    
                    $tentative_g_score = $node['data']['g'] + $this->distBetween($x, $y, $x1, $y1);
                    
                    if ( !isset($this->openList[$x1][$y1]) || $tentative_g_score < $this->openList[$x1][$y1]['g'] )
                        $this->addToOpenList($x1, $y1, $tentative_g_score, $this->distBetween($x1, $y1, $this->endX, $this->endY), $x, $y );
                }
        }
        
        if ( $found )
        {
            $this->traceBack($this->endX, $this->endY);
            return true;
        }
        else
            return false;
    }
    
    private function traceBack($x, $y)
    {
		while ( true )
		{
    		$this->shortestPath[$x][$y] = true;
    	
    		if ( !isset($this->closedList[$x][$y]) || $this->closedList[$x][$y] === false )
    			return;

			$cx = $this->closedList[$x][$y]['x'];
			$y = $this->closedList[$x][$y]['y'];
			$x = $cx;
		}
    }
    
    private function getLowest()
    {
        $lowestF = -1;
    	$lowestX = -1;
    	$lowestY = -1;
        $dat = array();
    	
   		foreach ( $this->openList as $x => $ar )
   		{
   			foreach ( $ar as $y => $data )
			{				
				if ( $data['g'] + $data['h'] <= $lowestF || $lowestF == -1 )
				{
					$lowestF = $data['g'] + $data['h'];
					$dat = $data;
					$lowestX = $x;
					$lowestY = $y;
				}
  			}
		}
		
		return array('x' => $lowestX, 'y' => $lowestY, 'data' => $dat);
    }
    
    private function addToOpenList($x, $y, $g, $h, $parentX, $parentY)
    {
   		$this->openList[$x][$y] = array('g' => $g, 'h' => $h, 'x' => $parentX, 'y' => $parentY);
    }
    private function removeFromOpenList($x, $y)
    {
    	unset($this->openList[$x][$y]);
    }
    
    private function addToClosedList($x, $y, $parent)
    {
    	$this->closedList[$x][$y] = $parent;
    }
    private function removeFromClosedList($x, $y)
    {
    	unset($this->closedList[$x][$y]);
    }
    
    private function moveToClosedList($x, $y)
    {
        $parent = $this->openList[$x][$y];
	
    	$this->removeFromOpenList($x, $y);
    	$this->addToClosedList($x, $y, $parent);
    }
    
    public function isObstacle($x, $y)
    {
    	return !isset($this->map[$x][$y]) || (bool)$this->map[$x][$y];
    }
    
    public function isClosed($x, $y)
    {
    	return isset($this->closedList[$x][$y]);
    }
}