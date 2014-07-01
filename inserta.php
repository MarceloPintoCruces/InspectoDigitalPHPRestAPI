<?php
	

	//  ..::FUNCIONANDO CORRECTAMENTE!::..
	require_once('clases/DataBaseHandler.php');

	$json = '[{"lat":0,"lon":0,"identidad":"pasajero01","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero11","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero21","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero31","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero41","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero51","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero61","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero71","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero81","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"},{"lat":0,"lon":0,"identidad":"pasajero91","accion":"sube","fecha":"2014-07-01 12:24:51","patente":"dz5022"}]';

	$decodificado = json_decode($json);

	$i=1;
	foreach ($decodificado as $dato) {

		$lat         =   $dato->lat;
		$lon         =   $dato->lon;
		$identidad   =   $dato->identidad;
		$accion      =   $dato->accion;
		$fecha       =   $dato->fecha;
		$patente     =   $dato->patente;
		
		//INSERT!
		//ESTE INSERT ES EN POSTGRE LOCAL DANIEL-PC
		$db = DataBaseHandler::getInstance()->connect();
		$query = "INSERT INTO PASAJERO (latitud, longitud, identidad, fecha, accion, patente) VALUES ('$lat', '$lon', '$identidad', '$fecha', '$accion', '$patente') ";
		echo $query;
		$data = $db->Execute($query);
		$i++;
	}

	$metodo = $_SERVER['REQUEST_METHOD'];
	
	if($metodo=="GET"){
		header("HTTP/1.1 200 Funciona el GET" );
		$response['status']=200;
		$response['status_message']="Fue un GET";
		$response['data']="Todo bien";

		echo "GET";
		exit();
	}

	if($metodo=="POST"){
		header("HTTP/1.1 200 Funciona el POST" );
		$response['status']=200;
		$response['status_message']="Fue un post";
		$response['data']="Todo bien";
		echo "POST";


		$conection = mysql_connect('localhost','marcelop','jp0s45y4IR') or die("Error al Conectar a la BD");
		mysql_select_db('marcelop_inspectordigital',$conection);
		$variable = "Marceliño";
		$query = "INSERT INTO `test_json_data`(`JSON_DATA_FROM_PHP`) VALUES ('$variable')";

		mysql_query($query);

		exit();
	}

	if($metodo=="PUT"){
		header("HTTP/1.1 200 Funciona el PUT" );
		$response['status']=200;
		$response['status_message']="Fue un PUT";
		$response['data']="Todo bien";
		echo "PUT";
		exit();
	}

	if($metodo=="DELETE"){
		header("HTTP/1.1 200 Funciona el DELETE" );
		$response['status']=200;
		$response['status_message']="Fue un DELETE";
		$response['data']="Todo bien";
		echo "DELETE";
		exit();
	}
	



	//INSERTAREMOS UN PASAJERO A ADO.DB POR POSTGRESQL

	/*

	$db = DataBaseHandler::getInstance()->connect();

	$query = "INSERT INTO PASAJERO (latitud, longitud, fecha, accion, patente) VALUES (-39, -74, '2014-05-30 00:00:00', 'SUBE', 'xx1313') ";
	echo $query;
	$data = $db->Execute($query);

	*/


	/*
	if(!empty($_POST)){

		$data = file_get_contents('php://input');

		//deliver_response(200, "book not found", null );


	}

	*/


?>