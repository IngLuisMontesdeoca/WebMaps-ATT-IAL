<?PHP
    /********************************************************************************
    *                                                           					*
	*   @autor:        (LM) Luis  Montes   <luis.montes@webmaps.com.mx>             *       
	*   @version:       1.0                                                         *
    *   @created:       24/08/2010                              					*
    *   @copiright:     Copyright (c) 2010, WebMaps              					*
    *   @description:	Operaciones basicas sobre un poligono 						*
    *   @notes:         none                                     					*
    ********************************************************************************/

class polygon{
var $vertices;
var $area;
var $perimetro;
var $noVertices;

	function polygon($vertices){
		$this->vertices = $vertices;
		$this->noVertices = count($vertices);
	}

	function findInPolygon($punto){
		$pos = 0;
		$angTot = 0;
		while($pos<$this->noVertices){
			$v1 = $this->vertices[$pos];
			if($pos == $this->noVertices-1)
				$v2 = $this->vertices[0];
			else
				$v2 = $this->vertices[$pos+1];
			$a = $this->getPointsDistance($punto,$v2);
			$b = $this->getPointsDistance($v1,$v2);
			$c = $this->getPointsDistance($punto,$v1);
			$ang = (180 * (acos(	(pow($a,2)+pow($c,2)-pow($b,2))/(2*($a*$c))		)))/pi();
			$angTot += $ang;
			$pos++;
		}	
		if($angTot == 360)
			return true;
		else
			return false;
	}
	
	function findInPolygon2($punto){
		$pos = 0;
		$angTot = 0;
		while($pos < $this->noVertices){
			$v1 = $this->vertices[$pos];
			if($pos == $this->noVertices-1)
				$v2 = $this->vertices[0];
			else
				$v2 = $this->vertices[$pos+1];
			$vec1 = array('x'=>($v1['x']-$punto['x']),'y'=>($v1['y']-$punto['y']));
			$vec2 = array('x'=>($v2['x']-$punto['x']),'y'=>($v2['y']-$punto['y']));
			$prod = ($vec1['x']*$vec2['x'])+($vec1['y']*$vec2['y']);
			$mod1 = sqrt( pow($vec1['x'],2)  + pow($vec1['y'],2) );
			$mod2 = sqrt( pow($vec2['x'],2)  + pow($vec2['y'],2) );
			$prodVec = ($vec1['x']*$vec2['y'] )-(  $vec1['y']*$vec2['x']);
			$cosAng = $prod/($mod1*$mod2);
			$ang = 180 * (acos($cosAng))/pi();
			if($prodVec<0)
				$ang = ($ang) * (-1);
			$angTot += $ang;
			$pos++;
		}
		if( ($angTot >= 180) || ($angTot <= -180))
			return true;
		else
			return false;
	}
	
	function getArea(){
		$pos = 0; 
		$area = 0;
		while($pos<$this->noVertices-2){
			$v1 = $this->vertices[0];
			$v2 = $this->vertices[$pos+1];
			$v3 = $this->vertices[$pos+2];
			$area = ( (($v1['x'])*($v2['y']))+(($v2['x'])*($v3['y']))+(($v3['x'])*($v1['y']))-(($v1['y'])*($v2['x']))-(($v2['y'])*($v3['x']))-(($v3['y'])*($v1['x'])) ) * 0.5;
			$pos++;
			$this->area += $area;
		}
		$this->area = abs($this->area);
		//$this->area = number_format(((pow( (sqrt($this->area) * 1790.49330317) , 2))/1000000), 2, ".", ",");
		$this->area = number_format(((pow( (sqrt($this->area) * 1790.48) , 2))/1000000), 2, ".", ",");
		return $this->area;
	}

	function getPerimetro(){
		$pos = 0;
		$perimetro = 0;
		while($pos < $this->noVertices){
			$v1 = $this->vertices[$pos];
			if($pos == $this->noVertices-1)
				$v2 = $this->vertices[0];
			else
				$v2 = $this->vertices[$pos+1];
			$perimetro += $this->getPointsDistance($v1,$v2);
			$pos++;
		}
		$this->perimetro = number_format(($perimetro * 1790.48 / 1000), 2, ".", ",");
		return $this->perimetro;
	}

	function getPointsDistance($a,$b){
		return sqrt(  (pow(($a['x']-$b['x']),2)) + (pow(($a['y']-$b['y']),2))	);
	}

}

?>