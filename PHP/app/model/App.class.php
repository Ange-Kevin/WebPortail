<?php
	class App {
		public static function isCurrentPage($page) {
			if ($_GET['page'] == $page) {
				echo ' class="active"';
			}
		}

		public static function isLogged() {
			if (isset($_SESSION['email']) && !empty($_SESSION['email'])) {
				return true;
			}

			return false;
		}

		public static function isStudent() {
			if (self::isLogged() && $_SESSION['type'] == 'student') {
				return true;
			}

			return false;
		}

		public static function isCompany() {
			if (self::isLogged() && $_SESSION['type'] == 'company') {
				return true;
			}

			return false;
		}

		public static function isAdmin() {
			if (self::isStudent()) {
				$student = Student::getStudentById($_SESSION['id']);

				if ($student->admin) {
					return true;
				}
				
				return false;
			}

			return false;
		}

		public static function getMember() {
			if (self::isLogged()) {
				if ($_SESSION['type'] == 'student') {
					return Student::getStudentById($_SESSION['id']);
				}

				if ($_SESSION['type'] == 'company') {
					return Company::getCompanyById($_SESSION['id']);
				}

				return false;
			}

			return false;
		}

		public static function login($email, $type) {
			PDOConnexion::setParameters('stages', 'root', 'root');
			$db = PDOConnexion::getInstance();

			if ($type == 'student') {
				$sql = "SELECT id, password, country, activated FROM student WHERE email = :email";
				$sth = $db->prepare($sql);
				$sth->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Student');
				$sth->execute(array(
					':email' => $email
				));
			}

			elseif ($type == 'company') {
				$sql = "SELECT id, password, country, activated FROM company WHERE email = :email";
				$sth = $db->prepare($sql);
				$sth->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Company');
				$sth->execute(array(
					':email' => $email
				));
			}

			else {
				App::redirect('index.php?page=home');
			}

			return $sth->fetch();
		}

		public static function notification($subject, $body) {
			if (Settings::isActivatedNotification()) {
				$to = Settings::getNotificationEmail();

				$message = '
					<html>
						<head>
							<title>' . $subject . '</title>
						</head>
						<body>' . $body . '</body>
					</html>
				';

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				$headers .= 'From: Internship Web Portal <internshipwebportal@unicaen.fr>' . "\r\n";

				mail($to, $subject, $message, $headers);
			}
		}

		public static function error($message) {
			echo '
				<div class="erreur">
					<div class="container">
						<i class="fa fa-times-circle"></i>
						' . $message . '
					</div>
				</div>
			';
		}

		public static function success($log) {
			echo '
				<div class="success">
					<div class="container">
						<i class="fa fa-check"></i>
						' . $log . '
					</div>
				</div>
			';
		}

		public static function getGravatar($email, $size = 80, $defaultImg = 'wavatar', $maximumRating = 'g', $img = false, $additional = array()) {
			$url = 'http://www.gravatar.com/avatar/';
			$url .= md5(strtolower(trim($email)));
			$url .= "?s=$size&d=$defaultImg&r=$maximumRating";
			
			if ($img) {
				$url = '<img src="' . $url . '"';
				foreach ($additional as $key => $val) {
					$url .= ' ' . $key . '="' . $val . '"';
				}
				$url .= ' />';
			}

			return $url;
		}

		public static function getHeader($code, $language, $msg) {
			require_once(APP . '/view/header.php');

			switch ($code) {
				case 404:
					header("HTTP/1.0 404 Not Found");
					require_once(APP . '/view/error.php');
				break;

				case 403:
					header("HTTP/1.0 403 Forbidden");
				break;
			}

			require_once(APP . '/view/footer.php');
			die();
		}

		public static function redirect($url) {
			header('Location: ' . $url, true, 302);
		}

		public static function dd($var) {
			echo '<pre>';
				var_dump($var);
			echo '</pre>';
			die();
		}

		public static function url($url) {
			$url = strip_tags($url);
			$url = strtolower($url);

			trim($url);
			$url = preg_replace('%[.,:\'"/\\\\[\]{}\%\-_!?]%simx', ' ', $url);
			$url = str_ireplace(' ', '-', $url);
			$url = str_ireplace('---', '-', $url);
			$url = str_ireplace('-|', '', $url);
			$url = str_ireplace('-&', '', $url);
			$url = self::removeAccents($url);

			return $url;
		}

		private static function removeAccents($str, $charset = 'utf-8') {
			$str = htmlentities($str, ENT_NOQUOTES, $charset);

			$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
			$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
			$str = preg_replace('#&[^;]+;#', '', $str);

			return $str;
		}
	}
?>
