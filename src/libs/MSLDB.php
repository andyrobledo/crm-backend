<?php

/*
Clase para manejo de DB de MSL
Refer:

	http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers

	Ejemplo de crear conexion
		$dbserver = json_decode('{"database":"crm","username":"root","password":"supersecreto"}');
		db_setup("mysql:host=localhost;dbname={$dbserver->database}",$dbserver->username,$dbserver->password);


	Ejemplo de getrow
		$rfc = 'AAA010101AAA';
		$contacto = db_getrow('select id,nombre,rfc,telefono from contactos where rfc=?', [$rfc]);

	Ejemplo de getall
		$buscar = 'perez';
		$contactos = db_getall('select * from contactos where nombre like ?',["%$buscar%"])


	Ejemplo de transacciones, insert y execute

		function afectarDB($d){
			try {
				// Iniciar transaccion
				db_beginTransaction();
				// Insertar register en facturas
				db_insert('facturas',$factura);
				// Tomar el id generado 
				$id = db_lastInsertId();
				// Guardar XML de la factura
				db_insert('xmls', ['id'=>$id, 'xml'=>$xml]);
				// Actualizar saldo de cliente
				db_execute('update cobranza set saldo=saldo+? whrere rfc=?', [$factura->total,$factura->rfc] );
				// Actualizar base de datos
				db_commit();
			} catch (Exception $e) {
				db_rollBack();	
				throw $e;			
			}
		}


*/

class MSLDB {

	public static $connections = array();
	public static $activeConnection = '';

	public $pdo;

	public static function addConnection($connectionName,$conn){
		self::$connections[$connectionName] = $conn;
		self::$activeConnection = $connectionName;
	}

	public static function getActiveConnection(){
		return self::$connections[self::$activeConnection];
	}

	public function setup($dsn,$user,$pswd,array $options=null) {
		$this->pdo = new PDO ($dsn,$user,$pswd,$options);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);	
	}


	/*
	Regresa un array vacio si no encuentro ningun register
	Regresa un array con los datos del select indicado
	*/
	public function getall($query, array $params=null) {
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}


	/*
	Regresa un objeto con el registo indicado en el query
	*/
	public function getrow($query, array $params=null) {
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($params);
		return $stmt->fetch();
	}


	/*
	Ejecuta un comando sql
	*/
	public function execute($query, array $params=null) {
		$stmt = $this->pdo->prepare($query);
		return $stmt->execute($params);
	}


	/*
		Ejecuta un insert en la tabla dada, puede recibir un arreglo o un objeto
	*/
	public function insert($table,$rec) {
		// s1 tendra field1,field2,field3
		// s2 tendra :field1,:field2,:field3
		// d dentra array(':field1'=>value1,':field2'=>value2)
		$s1 = '';
		$s2 = '';
		$d = array();
		foreach ($rec as $fieldname => $value) {
			$s1 .= ',' .$fieldname;
			$s2 .= ',:'.$fieldname;
			$d[':'.$fieldname] = $value;
		}
		// Quitar , inicial
		$s1 = substr($s1, 1);
		$s2 = substr($s2, 1);
		// Ejecutar insert
		$sql = "insert into $table ($s1) values ($s2)";
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute($d);
	}



	/*
		Ejecuta un update en la tabla dada, puede recibir un arreglo o un objeto,
		ejemplo: 
			$db->update('customer',$customer,'where id=?", [$id]);
			db_update('table', $data, $id);
		$wherecond puede tener 3 opciones:
			1) Un valor, se usara para formar el where id='$valor'
			2) Un arreglo de nombres, se usara el nombre del elemento para formar where id='$id' and fecha='$fecha'
			3) Un string, para tomarlo directamente con parte del where, 'where id=30'
	*/
	public function update($table,$rec,$wherecond) {
		// formar la cadena de campos a actualizar
		//    field1=:field1, field2=:field2, field3=:field3
		// asi como el arreglo de datos $d que se pasara como parametro al execute
		$d = [];
		$s = '';
		foreach ($rec as $fieldname => $value) {
			$s .= ','.$fieldname . '=:'. $fieldname;
			$d[':'.$fieldname] = $value;			
		}
		$s = substr($s,1);
		if (is_array($wherecond)){
			// Si wherecond es un array, hay que formar wherecond en forma de string 
			// where fieldcond1=:fieldcond1 and fieldcond2=:fieldcond2
			// TODO
			throw new Exception('MSLDB No implementado', 500);
		} elseif (strtolower(substr($wherecond,5))==='where') {
			// caso 3: viene indicada la condicion where
			// ya viene listo wherecond
		} else {
			// caso 1: es un valor directo, la tabla tiene el campo id
			// $wherecond es un valor
			$d[':id'] = $wherecond;
			$wherecond = 'where id=:id';
		}

		$stmt = $this->pdo->prepare("update $table set $s $wherecond");
		return $stmt->execute($d);
	}


	public function beginTransaction() {
		return $this->pdo->beginTransaction();
	}

	public function commit() {
		return $this->pdo->commit();
	}

	public function rollBack() {
		return $this->pdo->rollBack();
	}

}




/*
	Agrega una conexion 
	$dbserver = json_decode('{"database":"crm","username":"root","password":"supersecreto"}');
	db_setup("mysql:host=localhost;dbname={$dbserver->database}",$dbserver->username,$dbserver->password);
*/
function db_setup($dsn,$user,$pswd,array $options=null,$connectionName='default') {
	$db = new MSLDB();
	$db->setup($dsn,$user,$pswd,$options);
	MSLDB::addConnection($connectionName,$db);
}



/*
	Regresa un array vacio si no encuentro ningun register
	Regresa un array con los datos del select indicado

	$buscar = 'perez';
	$contactos = db_getall('select * from contactos where nombre like ?',["%$buscar%"])

*/

function db_getall($query, array $params=null) {
return MSLDB::getActiveConnection()->getall($query, $params); }


/*
	Regresa un objeto con el registo indicado en el query
		$contacto = db_getrow('select id,nombre,rfc,telefono from contactos where rfc=?', [$rfc]);
*/
function db_getrow($query, array $params=null) {
return MSLDB::getActiveConnection()->getrow($query, $params); }


function db_execute($query, array $params=null) {
return MSLDB::getActiveConnection()->execute($query, $params); }

function db_insert($table,$rec){
return MSLDB::getActiveConnection()->insert($table,$rec); }

function db_update($table,$rec,$wherecond){
return MSLDB::getActiveConnection()->update($table,$rec,$wherecond); }

function db_lastInsertId() {
return MSLDB::getActiveConnection()->pdo->lastInsertId(); }



function db_beginTransaction() {
return MSLDB::getActiveConnection()->pdo->beginTransaction(); }

function db_commit() {
return MSLDB::getActiveConnection()->pdo->commit(); }

function db_rollBack() {
return MSLDB::getActiveConnection()->pdo->rollBack(); }
