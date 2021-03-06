<?php

	class client
	{ 
		public $clientid = null;
		public $firstname = null;
		public $lastname = null;
		public $handle = null;
		public $email = null;
		public $mobile = null;
		public $sex = null;
		public $address = null;
		public $password = null;
		public $created = null;
		public $updated = null;

		public function __construct( $data=array() ) 
		{
			if ( isset( $data['clientid'] ) ) $this->clientid = (int) $data['clientid'];
			if ( isset( $data['firstname'] ) ) $this->firstname =  $data['firstname'];
			if ( isset( $data['lastname'] ) ) $this->lastname =  $data['lastname'];
			if ( isset( $data['handle'] ) ) $this->handle = $data['handle'];
			if ( isset( $data['email'] ) ) $this->email = $data['email'];
			if ( isset( $data['mobile'] ) ) $this->mobile = $data['mobile'];
			if ( isset( $data['sex'] ) ) $this->sex = $data['sex'];
			if ( isset( $data['address'] ) ) $this->address = $data['address'];
			if ( isset( $data['password'] ) ) $this->password = md5($data['password']);
			if ( isset( $data['created'] ) ) $this->created = (int) $data['created'];
			if ( isset( $data['updated'] ) ) $this->updated = (int) $data['updated'];
		}

		public function storeFormValues ( $params ) 
		{
			$this->__construct( $params );

			if ( isset($params['created']) ) {
				$created = explode ( '-', $params['created'] );

				if ( count($created) == 3 ) {
					list ( $y, $m, $d ) = $created;
					$this->created = mktime ( 0, 0, 0, $m, $d, $y );
				}
			}
		}

		public static function getById( $clientid ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT *, UNIX_TIMESTAMP(created) AS created FROM clients WHERE clientid = :clientid";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":clientid", $clientid, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) return new client( $row );
		}

		public static function signinuser( $handle, $password ) 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT * FROM clients WHERE handle = :handle AND password = :password";
			$st = $conn->prepare( $sql );
			$st->bindValue( ":handle", $handle, PDO::PARAM_INT );
			$st->bindValue( ":password", $password, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;
			if ( $row ) {
				$_SESSION['loggedin_level'] = $row['level'];
				$_SESSION['loggedin_fullname'] = $row['firstname'] . ' ' . $row['lastname'];
				$_SESSION['loggedin_user'] = $row['clientid'];
				return true;
			}	else return false;
		}

		public static function getList() 
		{
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "SELECT * FROM clients ORDER BY email ASC";

			$st = $conn->prepare( $sql );
			$st->execute();
			$list = array();

			while ( $row = $st->fetch() ) {
				$client = new client( $row );
				$list[] = $client;
			}

			$conn = null;
			return $list;
		}

		public function insert() 
		{
			if ( !is_null( $this->clientid ) ) trigger_error ( "client::insert(): Attempt to insert an client object that already has its ID property set (to $this->clientid).", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "INSERT INTO clients ( firstname, lastname, handle, email, created, mobile, address, sex, password ) VALUES ( :firstname, :lastname, :handle, :email, :created, :mobile, :address, :sex, :password)";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":firstname", $this->firstname, PDO::PARAM_STR );
			$st->bindValue( ":lastname", $this->lastname, PDO::PARAM_STR );
			$st->bindValue( ":handle", $this->handle, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":address", $this->address, PDO::PARAM_STR );
			$st->bindValue( ":sex", $this->sex, PDO::PARAM_STR );
			$st->bindValue( ":password", $this->password, PDO::PARAM_STR );
			$st->bindValue( ":created", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->execute();
			$this->clientid = $conn->lastInsertId();
			$conn = null;
			return $this->clientid;
		}

		public function update() 
		{
			if ( is_null( $this->clientid ) ) trigger_error ( "client::update(): Attempt to update an client object that does not have its ID property set.", E_USER_ERROR );
		   
			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$sql = "UPDATE clients SET firstname=:firstname, lastname=:lastname, handle=:handle, email=:email, mobile=:mobile, address=:address, sex=:sex, updated=:updated WHERE clientid=:clientid";
			$st = $conn->prepare ( $sql );
			$st->bindValue( ":firstname", $this->firstname, PDO::PARAM_STR );
			$st->bindValue( ":lastname", $this->lastname, PDO::PARAM_STR );
			$st->bindValue( ":handle", $this->handle, PDO::PARAM_STR );
			$st->bindValue( ":email", $this->email, PDO::PARAM_STR );
			$st->bindValue( ":mobile", $this->mobile, PDO::PARAM_STR );
			$st->bindValue( ":address", $this->address, PDO::PARAM_STR );
			$st->bindValue( ":sex", $this->sex, PDO::PARAM_STR );
			$st->bindValue( ":updated", date('Y-m-d H:i:s'), PDO::PARAM_INT );
			$st->bindValue( ":clientid", $this->clientid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

		public function delete() 
		{

			if ( is_null( $this->clientid ) ) trigger_error ( "client::delete(): Attempt to delete an client object that does not have its ID property set.", E_USER_ERROR );

			$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
			$st = $conn->prepare ( "DELETE FROM clients WHERE clientid = :clientid LIMIT 1" );
			$st->bindValue( ":clientid", $this->clientid, PDO::PARAM_INT );
			$st->execute();
			$conn = null;
		}

	}
