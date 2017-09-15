<?php
	/**
	*
	* Beskrivelse af hvad classen skal kunne
	*
	*/
	class Content{
		private $pdo;

		function __construct($pdo){
			$this->pdo = $pdo;
		}

		/**
		*
		* Beskrivelse af hvad functionen gør
		*
		* @param   array  beskrivelse     #
		* 
		* @return  true / false
		*
		*/
		private function out($id){
			return false;
		}

		/**
		*
		* Tjekker om folk er logget ind og om de har adgang til det givne domæne
		*
		* @param   array  beskrivelse     #
		* 
		* @return  true / false
		*
		*/
		public function access($d = NULL){
			if(empty($_SESSION['login'])){
				return false;
			}
			else{
				$u = $_SESSION['login'];
				if($d != NULL){
					// Tjekker adgang til de domæne
					// $q = $this->pdo->prepare("SELECT * FROM con WHERE uID=$u AND $dID=$d");
					// $q->bindParam(":")
					// if() > 0){
					// 	return true;
					// }
					// else{
					// 	return false;
					// }
				}
				else{
					return true;
				}
			}
		}

		/**
		*
		* Beskrivelse af hvad functionen gør
		*
		* @param   array  beskrivelse     #
		* 
		* @return  true / false
		*
		*/
		public function rens($felt){
			$felt = stripslashes($felt);
			$felt = strip_tags($felt);
			$felt = addslashes($felt);
			return $felt;
		}

		public function curPageName(){
			$url = $_SERVER["SCRIPT_NAME"];
			if(DEBUG)
				error_log("url: ".$url);
			if(strpos($url, "scripts") == false){
				$var = substr($url, strrpos($url,"/")+1);
				$var = explode(".", $var);
				if(DEBUG)
					error_log("chopped url: ".$var[0]);
				return $var[0];
			}
			return $url;
		}

		public function activePage($page){
			if(is_array($page)){
				if(in_array($this->curPageName(), $page))
					return " class=\"active\"";
			} else{
				if($page == $this->curPageName())
					return " class=\"active\"";
			}
		}
		public function activeMenu($page){
			if(is_array($page)){
				if(in_array($this->curPageName(), $page))
					return " active";
			} else{
				if($page == $this->curPageName())
					return " active";
			}
		}

		public function statusBox($class = "status"){
			$o = "";
			if(isset($_SESSION['status'])){
				$o .= '<div class="alert alert-'.$_SESSION['msg'].'" role="alert">';
				$o .= $_SESSION['status'];
				$o .= '</div>';
				unset($_SESSION['status']);
				unset($_SESSION['msg']);
			} else{
				$o .= '<div class="'.$class.' alert" role="alert" style="display: none;"></div>';
			}
			return $o;
		}

		public function login(){
			return ($_SESSION['login'] && isset($_SESSION['tID']));
		}

		// Password-stuff
		public function hash($p){
			$cost = 10;
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			$salt = sprintf("$2a$%02d$", $cost) . $salt;
			return password_hash($p, PASSWORD_BCRYPT, ['cost' => $cost, 'salt' => $salt]);
		}
	}
?>