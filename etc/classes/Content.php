<?php
	/**
	 *
	 * Handles all the repetitive actions that has to do with the userinterface or content on the site
	 *
	 * @param 	PDO 	A connection to the vmail table in the database
	 * @param 	String 	The 2-letter language selection
	 * 
	 */
	class Content{
		private $pdo;
		private $lang;

		function __construct(PDO $pdo, String $lang){
			$this->pdo = $pdo;
			$this->lang = strtolower($lang);
		}

		/**
		 *
		 * Returns a text from the database
		 *
		 * @param 	int 	ID on the line from the table, that is to be returned
		 * @param 	String 	Optional, if the selected language is not to be used
		 * 
		 * @return 	String 	The text or the message from the PDOException, if one is thrown
		 *
		 */
		public function out(int $id, String $lang = null){
			if(is_null($lang))
				$lang = $this->lang;
			try{
				$q = $this->pdo->prepare("SELECT {$lang} FROM ma_content WHERE contentID LIKE (:id)");
				$q->bindParam(":id", $id);
				$q->execute();
				$r = $q->fetch(PDO::FETCH_ASSOC);
				return $r[$lang];
			} catch(PDOException $e){
				return $e->getMessage();
			}
		}

		/**
		 *
		 * Returns the name of the site.
		 * Is used for the <title> tag
		 *
		 * @return 	String 	The site name
		 *
		 */
		public function siteName(){
			global $site, $d, $m;
			$r = $site['name'];
			if(isset($d)){
				if(isset($m))
					$r .= " - " . $m;
				else
					$r .= " - " . $d;
			} else{
				$r .= " - " . ucfirst($this->curPageName());
			}
			return $r;
		}

		/**
		 *
		 * Checks if a user is logged in and if it has access to the current site
		 *
		 * @param 	String 	A domain, if none is givenm it checks if site is "admin", after checking login status
		 * 
		 * @return 	Bool 	Whether or not the user has access
		 *
		 */
		public function access(String $d = NULL){
			if(empty($_SESSION['login'])){
				return false;
			} else{
				$u = $_SESSION['userID'];
				if($d != NULL){
					// Tjekker adgang til de domæne
					$q = $this->pdo->prepare("SELECT userID FROM ma_access WHERE userID LIKE (:u) AND domain LIKE (:d)");
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
		 * Cleans a string for use, if the intentions could be mallicious
		 *
		 * @param 	String
		 * 
		 * @return 	String
		 *
		 */
		public function clean(String $felt){
			$felt = stripslashes($felt);
			$felt = strip_tags($felt);
			$felt = addslashes($felt);
			return $felt;
		}

		/**
		 *
		 * Returns the current url, without the .php at the end
		 *
		 * @return 	String
		 *
		 */
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

		/**
		 *
		 * Returns " class=\"active\"" if the current page is in the given array or equals the given string
		 *
		 * @param 	String or Array
		 * 
		 * @return 	String
		 *
		 */
		public function activePage($page){
			if(is_array($page)){
				if(in_array($this->curPageName(), $page))
					return " class=\"active\"";
			} else{
				if($page == $this->curPageName())
					return " class=\"active\"";
			}
		}

		/**
		 *
		 * Returns " active" if the current page is in the given array or equals the given string
		 *
		 * @param 	String or Array
		 * 
		 * @return 	String
		 *
		 */
		public function activeMenu($page){
			if(is_array($page)){
				if(in_array($this->curPageName(), $page))
					return " active";
			} else{
				if($page == $this->curPageName())
					return " active";
			}
		}

		/**
		 *
		 * Returns a bootstrap 3.3.7 statusbox, with content if content is found in the _SESSION
		 *
		 * @param 	String 	Optional, a class the box should have, default "status"
		 * 
		 * @return 	String 	The statusbox
		 *
		 */
		public function statusBox(String $class = "status"){
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

		/**
		 *
		 * Blowfish hashes a string
		 *
		 * @param 	String 	The string to be hashed
		 * 
		 * @return 	String 	The hashed string
		 *
		 */
		public function hash(String $p){
			$cost = 10;
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			$salt = sprintf("$2a$%02d$", $cost) . $salt;
			return password_hash($p, PASSWORD_BCRYPT, ['cost' => $cost, 'salt' => $salt]);
		}
	}
?>