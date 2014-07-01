<?php
    
    require_once('PointLocation.class.php');
	/**
	* Class: GeometricHandler
	* Author: Marcelo Pinto Cruces
	*/
	class GeometricHandler extends PointLocation
	{
		
		function __construct()
		{
			
		}

		public function pgPolygonToArray($pgpolygon)
		{

			$cleanply = str_replace("((", "(", $pgpolygon);
			$cleanply = str_replace("))", ")", $cleanply);


			$data = explode("),(", $cleanply);

			for ($i=0; $i < count($data); $i++) {
				$data[$i] =  str_replace(")", "", $data[$i]);
				$data[$i] =  str_replace("(", "", $data[$i]);
				$otherdata[$i] = explode(",", $data[$i]);
			}

			for ($i=0; $i < count($otherdata); $i++) { 
				$polyarray[$i]['x'] = (double)$otherdata[$i][0];
				$polyarray[$i]['y'] = (double)$otherdata[$i][1];
			}

			return $polyarray;


		}

		public function pgPointToArray($pgpoint)
		{
			$cleanpoint = str_replace("(", "", $pgpoint);
			$cleanpoint = str_replace(")", "", $cleanpoint);
			
			$data = explode(",", $cleanpoint);

			$pointarray[0]['x'] = (double)$data[0];
			$pointarray[0]['y'] = (double)$data[1];

			return $pointarray;	
		}

		public function latLongToPointArray($lat,$lon)
		{
			$pointarray[0]['x'] = (double)$lat;
			$pointarray[0]['y'] = (double)$lon;

			return $pointarray;
		}

		public function arrayToPointLocationPolygon($polyarray)
		{
			$pl_polygon;

			for ($i=0; $i < count($polyarray); $i++) { 
				$pl_polygon[$i] = $polyarray[$i]['x']." ".$polyarray[$i]['y'];
			}

			return $pl_polygon;
		}

		public function arrayToPointLocationPoint($pointarray)
		{
			$pl_point;

			$pl_point = $pointarray[0]['x']." ".$pointarray[0]['y'];

			return $pl_point;
		}
	}

?>