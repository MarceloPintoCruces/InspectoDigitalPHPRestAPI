<?php

	include('DataBaseHandler.php');
	/**
	* Class: Recorrido
	*
	*/
	class Recorrido extends DataBaseHandler
	{
		private $id;
		private $area_recorrido;
		private $inicio_visual;
		private $fin_visual;
		private $nombre_recorrido;

		private $TABLE_NAME = 'recorrido';

		private $db;
		
		function __construct()
		{
			$this->db = DataBaseHandler::getInstance()->connect();
		}

		/**
		*Getters
		*/

		public function getId()
		{
			return $this->id;
		}
		public function getAreaRecorrido()
		{
			return $this->area_recorrido;
		}		
		public function getInicioVisual()
		{
			return $this->inicio_visual;
		}
		public function getFinVisual()
		{
			return $this->fin_visual;
		}
		public function getNombreRecorrido()
		{
			return $this->nombre_recorrido;
		}		

		/**
		*Setters
		*/

		public function setAreaRecorrido($area_recorrido)
		{
			$this->area_recorrido = $area_recorrido;
		}
		public function setInicioVisual($inicio_visual)
		{
			$this->inicio_visual = $inicio_visual;
		}
		public function setFinVisual($fin_visual)
		{
			$this->fin_visual = $fin_visual;
		}
		public function setNombreRecorrido($nombre_recorrido)
		{
			$this->nombre_recorrido = $nombre_recorrido;
		}		

		/**
		*Metodos de Modelo
		*/

		public function guardar()
		{
			
			$query = "INSERT INTO recorrido(area_recorrido,inicio_visual,fin_visual,nombre_recorrido)
					  VALUES('$this->area_recorrido','$this->inicio_visual','$this->fin_visual','$this->nombre_recorrido')";
			$var = $this->db->Execute($query); 
		}

		public function actualizar($id)
		{
			$data['area_recorrido'] = $this->area_recorrido;
			$data['inicio_visual'] = $this->inicio_visual;
			$data['fin_visual'] = $this->fin_visual;
			$data['nombre_recorrido'] = $this->nombre_recorrido;

			$db->AutoExecute($this->getTableName(), $data, 'UPDATE', 'id_recorrido = '.$id);
		}

		public function eliminar($id)
		{
			$data['estado'] = 0;

			$db->AutoExecute($this->getTableName(), $data, 'UPDATE', 'id_recorrido = '.$id);
		}			

		static public function buscarPorId($id)
		{
			$data = $this->db->Execute('SELECT id, area_recorrido, inicio_visual, fin_visual, nombre_recorrido FROM '.$this->TABLE_NAME.' WHERE id = '.$id);

			while($data and !$data->EOF){

				$this->id = $data->fields[0];
				$this->area_recorrido = $data->fields[1];
				$this->inicio_visual = $data->fields[2];
				$this->fin_visual = $data->fields[3];
				$this->nombre_recorrido = $data->fields[4];

			}
		}

		static public function buscarPorAtributos($atributos)
		{
			$i = 0;
			$query = "SELECT id, area_recorrido, inicio_visual, fin_visual, nombre_recorrido FROM ".$this->TABLE_NAME." WHERE ";
			foreach ($atributos as $key => $value) {
				$query.= $key." = ".$value;
				if($i<count($atributos)-1){
					$query.= "AND";
				}
			}

			$data = $this->db->Execute($query);

			while($data and !$data->EOF){
				$this->id = $data->fields[0];
				$this->area_recorrido = $data->fields[1];
				$this->inicio_visual = $data->fields[2];
				$this->fin_visual = $data->fields[3];
				$this->nombre_recorrido = $data->fields[4];

			}			
		}

		public function crearDesdeEstandar($id_recorrido_estandar)
		{
			$data = $this->db->Execute('SELECT * FROM recorrido_estandar WHERE id_rec_estandar ='.$id_recorrido_estandar);
			//while($data and !$data->EOF){
				$this->area_recorrido = $data->fields[1];
				$this->inicio_visual = $data->fields[2];
				$this->fin_visual = $data->fields[3];
				$this->nombre_recorrido = $data->fields[4];



			//}				
		}

		public function instanciarRecorridoPorBus($id_recorrido, $id_bus)
		{
			$query = 'INSERT INTO recorrido_bus VALUES('.$id_recorrido.','.$id_bus.')';

			if($this->db->Execute($query)){
				return true;
			}else{
				return false;
			}
		}


		/**
		* Métodos de Utilidades
		*/

		public function getListaRecorridosEstandar()
		{

			$data = $this->db->Execute('SELECT id_rec_estandar, nombre_rec_estandar FROM recorrido_estandar');
			

			$lista = '<select name = "recorrido">';

				//while($data and !$data->EOF){

					$lista .= '<option value = '.$data->fields[0].'>'.$data->fields[1].'</option>';

				//}

			$lista.='</select>';

			return $lista;
			
		}

		public function getForm($id_bus)
		{
			$form = '
						<form class="well span8" action="funciones/modulos/recorridos.php" method="POST">
						    <table style="border-style: none; width: 100%;">
						        <tr>
						            <td style="text-align: center;" colspan="4"><h2>Registro de Recorridos</h2></td>
						        </tr>
						        <tr style="height: 50px;">
						            <td style="width: 20%;"><strong>Seleccione Recorrido para su Bus</strong></td>
						            <td style="width: 25%;">
						                <div class="controls">'.
						                    $this->getListaRecorridosEstandar()
						                .'</div>
						            </td>
						        </tr>
				
						        <tr>
						            <td colspan="5" style="text-align: center;">
						                <div class="control-group">
						                <label class="control-label" for="button1id"></label>
						                <div class="controls">
						                    <input type="hidden" name="accion" value="nuevo" />
						                  <input type="submit" name="guardar" class="btn btn-success" value="Grabar" style="height: 34px; width: 90px;">
						                  <button name="cancelar" class="btn btn-danger" onclick="javascript: history.back()"><i class="icon-remove"></i> Cancelar</button>
						                </div>
						              </div>
						            </td>
						        </tr>
						    </table>
						    <input type = hidden name = "id_bus" value ="'.$id_bus.'" />
						</form>
			';

			return $form;
		}

		public function getTableName()
		{
			return $this->TABLE_NAME;
		}
		
		public function toString()
		{
			return "<br>Area Recorrido:".$this->area_recorrido."<br>Inicio Visual:".$this->inicio_visual.
			"<br>Fin Visual:".$this->fin_visual."<br>Nombre Recorrido:".$this->nombre_recorrido;
		}


	}


?>