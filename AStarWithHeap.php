<?php
class AStarWithHeap
{
	const HV_COST 					= 10;
	const D_COST					= 14;
	const ALLOW_DIAGONAL 			= true;
	const ALLOW_DIAGONAL_CORNERING 	= false;
	
    private $map = array(), $nodes = array();
	private $startX, $startY, $endX, $endY;
    public $shortestPath = array();
    
    private $heap;
    
    public function __construct($map, $startX, $startY, $endX, $endY)
    {
    	$this->heap = new AStarHeap();
    	
    	$this->map = $map;
    	
    	$this->startX = $startX;
    	$this->startY = $startY;
    	
    	$this->endX = $endX;
    	$this->endY = $endY;
        
        //$this->addToOpenList($startX, $startY, 0, $H, -1, -1);
    }
    
    public function setup()
    {
    	$node = new AStarNode($this->startX, $this->startY, 0, $this->distBetween($this->startX, $this->startY, $this->endX, $this->endY));
        $this->heap->insert($node);
    }
    
    public static function getEmptyMap($width, $height)
    {
    	$map = array();
    	
    	for ( $x = 0; $x < $width; $x++ )
    		for ( $y = 0; $y < $height; $y++ )
    			$map[$x][$y] = true;
 			
		return $map;
    }
    
    public static function getRandomMap($width, $height)
    {
    	$map = array();
    	
    	for ( $x = 0; $x < $width; $x++ )
    		for ( $y = 0; $y < $height; $y++ )
    			if ( (bool)rand(0, 2) )
					$map[$x][$y] = true;
 			
		return $map;
    }
    
    private function distBetween($startx, $starty, $x, $y)
    {
        $xDelta = abs($startx - $x);
        $yDelta = abs($starty - $y);
        
        $minDelta = min($xDelta, $yDelta);
        
        if ( self::ALLOW_DIAGONAL )
        	$g = $minDelta * self::D_COST;
        else
        	$minDelta = 0;
        	
		$g += ( $xDelta - $minDelta ) * self::HV_COST;
        $g += ( $yDelta - $minDelta ) * self::HV_COST;

   		return $g;
    }
    
    public function findShortestPath()
    {
    	$this->setup();
    	
        $found = false;
   		
	   	while ( $this->heap->valid() )
        {
      		$node = $this->heap->extract();
        	
			if ( $node->IsClosed )
        		break;
        		
            $node->close();

            if ( $node->x == $this->endX && $node->y == $this->endY )
            {
                $found = true;
                break;
            }
            
            // Foreach neighbour
            for ( $x1 = $node->x - 1; $x1 <= $node->x + 1; $x1++ )
        		for ( $y1 = $node->y - 1; $y1 <= $node->y + 1; $y1++ )
        		{
        			if ( $x1 == $node->x && $y1 == $node->y )
        				continue;
        			
        			if ( !isset($this->map[$x1][$y1]) )
       					continue;
        			
        			if ( !self::ALLOW_DIAGONAL_CORNERING && $y1 != $node->y && $x1 != $node->x )
    				{
    					if ( $x1 > $node->x && !isset($this->map[$node->x + 1][$node->y]) )
    						continue;
    					if ( $x1 < $node->x && !isset($this->map[$node->x - 1][$node->y]) )
    						continue;
    					if ( $y1 > $node->y && !isset($this->map[$node->x][$node->y + 1]) )
    						continue;
    					if ( $y1 < $node->y && !isset($this->map[$node->x][$node->y - 1]) )
    						continue;
    				}
        			
        			$node1 = $this->map[$x1][$y1];
    				
        			if ( is_bool($node1) )
        			{
        				$node1 = new AStarNode($x1, $y1, $node->g + $this->distBetween($node->x, $node->y, $x1, $y1), $this->distBetween($x1, $y1, $this->endX, $this->endY), $node);
        				$this->map[$x1][$y1] = $node1;
       					$this->heap->insert($node1);
		   			}
		   			elseif ( $node1->IsClosed )
       					continue;
					elseif ( ( $tentative_g_score = $node->g + $this->distBetween($node->x, $node->y, $x1, $y1) ) < $node1->g )
       					$node1->setInfo($tentative_g_score, $this->distBetween($x1, $y1, $this->endX, $this->endY), $node);
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
    	$node = $this->map[$x][$y];
		while ( true )
		{
    		$this->shortestPath[$node->x][$node->y] = true;

    		if ( is_null($node->parent) )
    			return;
			
			$node = $node->parent;
		}
    }
}

class AStarNode
{
	public $x, $y;
	public $g, $h, $f;
	
	public $IsClosed = false;
	public $parent = null;
	
	public function __construct($x, $y, $g, $h, AStarNode $parent = null)
	{
		$this->x = $x;
		$this->y = $y;
		
		$this->setInfo($g, $h, $parent);
	}
	
	public function setInfo($g, $h, AStarNode $parent = null)
	{
		$this->g = $g;
		$this->h = $h;
		
		$this->f = $g + $h;
		
		/*if ( !is_null($parent) )
		{
			$this->parentX = $parent->x;
			$this->parentY = $parent->y;
		}*/
		$this->parent = $parent;
	}
	
	public function close()
	{
		$this->IsClosed = true;
	}
}

class AStarHeap extends SplMinHeap
{
	public function compare($value1, $value2)
	{
		if ( $value1->IsClosed )
			return -1;
		elseif ( $value2->IsClosed )
			return 1;
		
		if ( $value1->f == $value2->f )
			return 0;
		
		if ( $value1->f > $value2->f )
			return -1;
		else
			return 1;
	}
}