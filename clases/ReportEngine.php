<?php

	require_once('DataBaseHandler.php');
	require_once('GeometricHandler.php');
	/**
	* Class: ReportEngine
	*Author: Marcelo Pinto Cruces
	*/
	class ReportEngine extends GeometricHandler 
	{
		private $db;
		
		function __construct()
		{
			$this->db = DataBaseHandler::getInstance()->connect();
		}

		public function getListOfBusesFromEmpresa($id_empresa)
		{
			$query = "SELECT id_bus, patente FROM bus WHERE id_empresa = '$id_empresa'";
			$buses = array();
			$i = 0;

			$data = $this->db->Execute($query);

			while ($data && !$data->EOF) {
				$buses[$i]['id_bus'] = $data->fields[0];
				$buses[$i]['patente'] = $data->fields[1];
				$i++;
				$data->MoveNext();
			}

			return $buses;
		}

		public function getListOfRecorridoFromBus($id_bus)
		{
			$query = "SELECT id_rec_estandar FROM recorrido_bus WHERE id_bus = '$id_bus'";
			//AHORA ES UN ARREGLO DE RECORRIDOS
			$recorrido = array();

			$data = $this->db->Execute($query);
			
			$i=0;
			while ($data && !$data->EOF) {
				$recorrido[$i]=$data->fields[0];
				$data->MoveNext();
				$i++;
			}

			//$recorrido = $data->fields[0];

			return $recorrido;

		}

		public function getListOfTramosFromRecorrido($id_recorrido) 
		{
			
			$query = "SELECT area_tramo_estandar, id_tram_estandar, inicio_visual, nombre_tramo,valor_estimado FROM tramo_estandar WHERE id_rec_estandar = '$id_recorrido'";
			

			$tramos = array();
			$i = 0;

			$data = $this->db->Execute($query);



			while ($data && !$data->EOF) {
				$tramos[$i]['area_tramo'] = $data->fields[0];
				$tramos[$i]['id_tramo_estandar'] = $data->fields[1];
				$tramos[$i]['inicio_visual'] = $data->fields[2];
				$tramos[$i]['nombre_tramo'] = $data->fields[3];
				$tramos[$i]['valor_estimado'] = $data->fields[4];
				$i++;
				$data->MoveNext();
			}

			return $tramos;
		}

		public function getAllPassengersByPatente($patente)
		{
			$query = "SELECT accion, latitud, longitud FROM pasajero WHERE patente = '$patente'";
			$i = 0;

			$data = $this->db->Execute($query);

			while ($data && !$data->EOF) {

				$punto = $this->latLongToPointArray($data->fields[1], $data->fields[2]);
				$passengers[$i] = array('accion' => $data->fields[0],
										'point' => $punto);
				$i++;
				$data->MoveNext();
			}

			return $passengers;

		}

		public function getPatenteByIdBus($id_bus)
		{
			$query = 'SELECT patente FROM bus WHERE id_bus = '.$id_bus.'';
			$data = $this->db->Execute($query);
			return $data->fields[0];
		}

		public function getPassengersFromTramo($tramos,$patente)
		{
			$passengers = $this->getAllPassengersByPatente($patente); 
			//Se debe implementar otro método similar a este, pero que filtre por fechas específicas.
			$counters;
			$count = 0;
			$sube = 0;
			$baja = 0;
			$tramos_bag = array();

			$i = 0;
			$j = 0;


			foreach ($tramos as $key => $area_tramo) {

				$polygon = $this->pgPolygonToArray($area_tramo['area_tramo']);
				$poligono = $this->arrayToPointLocationPolygon($polygon);
				$i = 0;
				$count = 0;
				$sube = 0;
				$baja = 0;
				$valor_estimado = $area_tramo['valor_estimado'];


				foreach ($passengers as $key => $passenger) {

					$punto = $this->arrayToPointLocationPoint($passenger['point']);

					if($this->pointInPolygon($punto,$poligono)){ //PERTENECE AL TRAMO?
						$count++;
						if($passenger['accion']=="SUBE"){ //SUBE O BAJA
							$sube++;
						}else{
							$baja++;
						}
					}

					$i++;

				}

				$tramos_bag[$j]['id_tramo'] = $area_tramo['id_tramo_estandar'];
				$tramos_bag[$j]['inicio_visual'] = $area_tramo['inicio_visual'];
				$tramos_bag[$j]['nombre_tramo'] = $area_tramo['nombre_tramo'];
				$tramos_bag[$j]['suben'] = $sube;
				$tramos_bag[$j]['bajan'] = $baja;
				$tramos_bag[$j]['total'] = $count;
				$tramos_bag[$j]['valor_estimado'] = $sube*$valor_estimado;

				$j++;
 			}


			return $tramos_bag;
		}

		public function getOptionsMapFromTramos($tramos_evaluados)
		{

			/*
			echo "</br>";
			echo "Tramos Evaluados: ";
			echo "<pre>";
			var_dump($tramos_evaluados);
			echo "</pre>";
			*/

			//Yii::app()->end();

	        $punto = $this->pgPointToArray($tramos_evaluados[0]['inicio_visual']);
	        
		    $inicio_lat = $punto[0]['x'];
		    $inicio_lon = $punto[0]['y'];			

			$center = 'center: new google.maps.LatLng('.$inicio_lat.','.$inicio_lon.')';
			$i = 0;

			$place = 'var place = new Array();';
			$info = 'var info = new Array();';

			foreach ($tramos_evaluados as $key => $value) {
				$punto = $this->pgPointToArray($value['inicio_visual']);
				$inicio_lat = $punto[0]['x'];
				$inicio_lon = $punto[0]['y'];
				$place.='place['.$i.'] = new google.maps.LatLng('.$inicio_lat.', '.$inicio_lon.');';
				$info .='info['.$i.'] = "Subieron:'.$value['suben'].'<br> Bajaron:'.$value['bajan'].'<br>Valor Estimado: $'.$value['valor_estimado'].'";';
				$i++;
			}

			$options_map = array(
				'center' => $center,
				'place' => $place,
				'info' => $info,
				);

			return $options_map;
		}

		public function getLatLonOfRecorrido($id_recorrido)
		{
			$punto = array();

			$query = "SELECT inicio_visual, fin_visual 
						FROM recorrido_estandar 
						WHERE id_rec_estandar = '$id_recorrido'";

			$data = $this->db->Execute($query);

			$punto = array(
				'inicio_visual' => $data->fields[0],
				'fin_visual' => $data->fields[1]
				);

			return $punto;

		}




		/*
		*
		*
		* PARA LA GESTION DE PASAJEROS POR FECHAS 
		*
		*/


		public function getPassengersFromTramoDate($tramos,$patente, $inicio, $fin)
		{
			$passengers = $this->getAllPassengersByPatenteDate($patente, $inicio, $fin);
			
			if($passengers==null){
				//Pasajero fantasma
				$passengers = array(array('accion'=>'NADA', 'point'=>array(array('x'=>0.0, 'y'=>0.0))));
				/*
				echo "</br>";
				echo "PRUEBA PASAJERO PHANTOM";
				echo "<pre>";
				var_dump($test);
				echo "</pre>";
				echo "</br>";
				*/
				//	return null;
			}

			/*
			echo "</br>";
			echo "PASSENGERS ";
			echo "<pre>";
			var_dump($passengers);
			echo "</pre>";
			*/

			//Yii::app()->end();

			$counters;
			$count = 0;
			$sube = 0;
			$baja = 0;
			$tramos_bag = array();

			$i = 0;
			$j = 0;


			foreach ($tramos as $key => $area_tramo) {

				$polygon = $this->pgPolygonToArray($area_tramo['area_tramo']);
				$poligono = $this->arrayToPointLocationPolygon($polygon);
				$i = 0;
				$count = 0;
				$sube = 0;
				$baja = 0;
				$valor_estimado = $area_tramo['valor_estimado'];


				foreach ($passengers as $key => $passenger) {

					$punto = $this->arrayToPointLocationPoint($passenger['point']);

					if($this->pointInPolygon($punto,$poligono)){ //PERTENECE AL TRAMO?
						$count++;
						if($passenger['accion']=="SUBE"){ //SUBE O BAJA
							$sube++;
						}else{
							$baja++;
						}
					}

					$i++;

				}

				$tramos_bag[$j]['id_tramo'] = $area_tramo['id_tramo_estandar'];
				$tramos_bag[$j]['inicio_visual'] = $area_tramo['inicio_visual'];
				$tramos_bag[$j]['nombre_tramo'] = $area_tramo['nombre_tramo'];
				$tramos_bag[$j]['suben'] = $sube;
				$tramos_bag[$j]['bajan'] = $baja;
				$tramos_bag[$j]['total'] = $count;
				$tramos_bag[$j]['valor_estimado'] = $sube*$valor_estimado;

				$j++;
 			}

			return $tramos_bag;
		}

		public function getAllPassengersByPatenteDate($patente, $inicio, $fin)
		{
			//$query = "SELECT accion, latitud, longitud FROM pasajero WHERE patente = '$patente'";
			$query = "SELECT accion, latitud, longitud FROM pasajero WHERE patente = '$patente' AND fecha between '$inicio' AND '$fin' ";

			
			$i = 0;

			$data = $this->db->Execute($query);


			while ($data && !$data->EOF) {

				$punto = $this->latLongToPointArray($data->fields[1], $data->fields[2]);
				$passengers[$i] = array('accion' => $data->fields[0],
										'point' => $punto);
				$i++;
				$data->MoveNext();
			}

			if(!isset($passengers)){
				return null;
			}

			return $passengers;
		}
	}
?>