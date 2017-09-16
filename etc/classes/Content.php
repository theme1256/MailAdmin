<?php
	/**
	*
	* Beskrivelse af hvad classen skal kunne
	*
	*/
	class Content{
		private $pdo;
		private $lang;

		function __construct($pdo, $lang){
			$this->pdo = $pdo;
			$this->lang = strtolower($lang);
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
		public function out($id){
			try{
				$q = $this->pdo->prepare("SELECT {$this->lang} FROM ma_content WHERE contentID LIKE (:id)");
				$q->bindParam(":id", $id);
				$q->execute();
				$r = $q->fetch(PDO::FETCH_ASSOC);
				return $r[$this->lang];
			} catch(PDOException $e){
				return $e->getMessage();
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
		public function siteName(){
			global $site, $d, $m;
			$r = $site['name'];
			if(isset($d)){
				if(isset($m))
					$r .= " - " . $m . "@" . $d;
				else
					$r .= " - " . $d;
			} else{
				$r .= " - " . ucfirst($this->curPageName());
			}
			return $r;
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
			} else{
				$u = $_SESSION['userID'];
				if($d != NULL){
					// Tjekker adgang til de domæne
					$q = $this->pdo->prepare("SELECT * FROM ma_access WHERE userID LIKE (:u) AND domain LIKE (:d)");
					$q->bindParam(":u", $u);
					$q->bindParam(":d", $d);
					$q->execute();
					if($q->rowCount() > 0)
						return true;
					else
						return false;
				} elseif(strpos($this->curPageName(), "admin")){
					if($u == 1)
						return true;
					else
						return false;
				} else{
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
		public function clean($felt){
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

		// Password-stuff
		public function hash($p){
			$cost = 10;
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			$salt = sprintf("$2a$%02d$", $cost) . $salt;
			return password_hash($p, PASSWORD_BCRYPT, ['cost' => $cost, 'salt' => $salt]);
		}
	}
?>