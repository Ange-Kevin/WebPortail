<?php
	if (isset($_POST['register'])) {
		$recaptchaClass = new Recaptcha('6Lc2cBoTAAAAAFUipDOigbn4PrJIScG6bwUqWbTQ');

		$my_file = basename($_FILES['cv']['name']);
		$max_file_size = 6000000;
		$file_size = filesize($_FILES['cv']['tmp_name']);
		$file_ext = strrchr($_FILES['cv']['name'], '.'); 

		if (isset($_POST['first_name']) && !empty($_POST['first_name']) && preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['first_name']) &&
    		isset($_POST['last_name']) && !empty($_POST['last_name']) && preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['last_name']) &&
    		isset($_POST['email']) && !empty($_POST['email']) && preg_match("#^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']) &&
    		isset($_POST['email-confirm']) && $_POST['email-confirm'] == $_POST['email'] &&
    		Student::checkEmailExist($_POST['email'])==false &&
    	   	isset($_POST['password']) && !empty($_POST['password']) && preg_match("#^[a-zA-Z\@._-]{8}#", $_POST['password']) &&
    	   	isset($_POST['password-confirm']) && $_POST['password-confirm'] == $_POST['password'] &&
    	   	isset($_POST['country'] ) && !empty($_POST['country']) &&
    	   	isset($_POST['skill']) && !empty($_POST['skill']) &&
    	   	isset($_FILES['cv']) && $file_ext == '.pdf' && $file_size < $max_file_size &&
    	   	isset($_POST['portfolio']) &&
    	   	preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $_POST['portfolio']) && 
    	   	isset($_POST['accept_terms']) &&
    	   	isset($_POST['g-recaptcha-response'])
    	   ) {
    	        	    
			try {

				PDOConnexion::setParameters('stages', 'root', 'root');
				$db = PDOConnexion::getInstance();
				$sql = "
					INSERT INTO student(first_name, last_name, country, skill, email, password, portfolio, admin, available, activated, register_date)
					VALUES (:first_name, :last_name, :country, :skill, :email, :password, :portfolio, false, false, false, NOW())
				";
				$sth = $db->prepare($sql);
				$sth->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Student');
				$sth->execute(array(
					':first_name' => $_POST['first_name'],
					':last_name' => $_POST['last_name'],
					':country' => $_POST['country'],
					':skill' => $_POST['skill'],
					':email' => $_POST['email'],
					':password' => Bcrypt::hashPassword($_POST['password']),
					':portfolio' => $_POST['portfolio']
				));

				$id_stud = Student::getStudentIDByEmail($email);

          		$folder = "uploads/cv";
          		$file = $folder . '/' . $id_stud->id . '.pdf';
         		move_uploaded_file($_FILES['cv']['tmp_name'], $file);

         		PDOConnexion::setParameters('stages', 'root', 'root');
				$dbh = PDOConnexion::getInstance();
				$req = "UPDATE student SET cv = :cv WHERE id = :id";
				$st = $dbh->prepare($req);
				$st->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Student');
				$st->execute(array(
					':cv' => $file,
					':id' => $id_stud->id
				));


				App::redirect('index.php?page=home');
			}

			catch(PDOException$e) {
				echo '<p>Erreur : ' . $e->getMessage() . '</p>';
				die();
			}
		}
		
		else {
			if ((!isset($_POST['first_name']) || empty($_POST['first_name'])) || 
			(!isset($_POST['last_name']) || empty($_POST['last_name'])) ||
			(!isset($_POST['email']) || empty($_POST['email'])) ||
			(!isset($_POST['email-confirm']) || empty($_POST['email-confirm'])) ||
			(!isset($_POST['password']) || empty($_POST['password'])) ||
			(!isset($_POST['password-confirm']) || empty($_POST['password-confirm'])) ||
			(!isset($_POST['country']) || empty($_POST['country'])) ||
			(!isset($_POST['skill']) || empty($_POST['skill'])) ||
			(!isset($_FILES['cv'])) ||
			(!isset($_POST['portfolio']) || empty($_POST['portfolio']))) {
				App::error('Vous devez remplir tous les champs obligatoires');
			}

			if (!preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['first_name'])){
				App::error("Veuillez entrer un prénom approprié");
			}

			if (!preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['last_name'])){
				App::error("Veuillez entrer un nom approprié");
			}

			if (!preg_match("#^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])){
				App::error("Veuillez entrer un email approprié");
			}

			if (Student::checkEmailExist($_POST['email'])==true){
				App::error("Cette adresse email est déjà utilisée");
			}

			if (!preg_match("#^[a-zA-Z\@._-]{8}#", $_POST['password'])){
				App::error("Veuillez entrer un mot de passe approprié");
			}

			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $_POST['portfolio'])){
				App::error("Veuillez entrer une adresse web appropriée");
			}

			if ($_POST['email']!=$_POST['email-confirm']){
				App::error("L'adresse email doit correspondre");
			}

			if ($_POST['password']!=$_POST['password-confirm']){
				App::error("Le mot de passe doit correspondre");
			}

			if ($file_ext != '.pdf'){
				App::error("Votre CV doit être au format PDF");
			}

			if ($file_size > $max_file_size){
				App::error("Votre CV est trop lourd, choisissez un autre fichier");
			}

			if (!isset($_POST['accept_terms'])){
				App::error("Vous devez accepter les conditions d'utilisation");
			}

			if (!isset($_POST['g-recaptcha-response'])){
				App::error("Vous devez confirmer que vous n'êtes pas un robot");
			}
		}
	}

?>
<style type="text/css">
	form .row {
		margin-bottom: 20px;
	}
</style>

<div class="container">
	<div class="page-header">
		<h1>
			Inscription
			<small>en tant qu'étudiant</small>
		</h1>
	</div>

	<div class="row">
		<div class="col-md-8">
			<form name="login" method="POST" action="index.php?page=register-student" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-6">
						<label for="signup-firstname">Prénom*</label>
						<input type="text" name="first_name" class="form-control" required="required" id="signup-firstname" placeholder="Prénom" data-validation="length" data-validation-length="2-20"  data-validation-error-msg="Entrez votre prenom !">
					</div>

					<div class="col-md-6">
						<label for="signup-lastname">Nom*</label>
						<input type="text" name="last_name" class="form-control" required="required" id="signup-lastname" placeholder="Nom" data-validation="length" data-validation-length="2-30"  data-validation-error-msg="Entrez votre nom !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-email">Adresse email*</label>
						<input type="text" name="email" class="form-control" required="required" id="signup-email" placeholder="Adresse email" data-validation="email"  data-validation-error-msg="Adresse mail invalide !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-email-confirm">Confirmez votre adresse email*</label>
						<input type="text" name="email-confirm" class="form-control" required="required" id="signup-email-confirm" placeholder="Confirmez votre adresse email" data-validation="confirmation" data-validation-confirm="email" data-validation-error-msg="L'adresse ne correspond pas à celle saisie plus haut !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-password">Mot de passe*</label>
						<input type="password" name="password" class="form-control" required="required" id="signup-password" placeholder="Mot de passe (8 caractères minimum)" data-validation="length" data-validation-length="min8" data-validation-error-msg="Le mot de passe doit contenir 8 charactères alphanumérique au minimum !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-password-confirm">Confirmez votre mot de passe*</label>
						<input type="password" name="password-confirm" class="form-control" required="required" id="signup-password-confirm" placeholder="Confirmez votre mot de passe" data-validation="confirmation" data-validation-confirm="password" data-validation-error-msg="Le mot de passe ne correspond pas à celui saisi plus haut !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<label for="signup-country">Pays*</label>
						<select name="country" id="signup-country" required="required" class="form-control">
							<option value="" disabled selected>Choisissez votre pays</option>
							<option value="France">France</option>
							<option value="Irlande">Irlande</option>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<label for="signup-skill">Domaine de compétences*</label>
						<select name="skill" id="signup-skill" required="required" class="form-control">
							<option value="" disabled selected>Choisissez votre domaine de compétences</option>
							<?php
                                PDOConnexion::setParameters('stages', 'root', 'root');
								foreach (Skill::getSkillsList() as $skill) {
									echo '<option value="' . $skill->id . '">' . $skill->name . '</option>';
								}
							?>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<label for="signup-cv">CV*</label>
						<input type="file" name="cv" id="signup-cv" placeholder="Insérer votre CV" required="required" data-validation-error-msg="Vous devez insérer un CV !">
					</div>

					<div class="col-md-6">
						<label for="signup-portfolio">Portfolio</label>
						<input type="text" name="portfolio" class="form-control" id="signup-portfolio" placeholder="Portfolio" data-validation="length" data-validation-length="16-128" data-validation-error-msg="Vous devez entrer un site web !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-terms">Conditions d'utilisation</label>
						<textarea class="form-control">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse at quam eget dolor mattis dapibus sit amet in ante. Nunc mattis euismod dapibus. Nunc ullamcorper finibus metus dapibus tristique. Maecenas malesuada bibendum metus, quis lacinia nisl consectetur in. In nec ex ac nulla sagittis tempus sed eu sem. Maecenas ac tortor eu justo volutpat tincidunt. Etiam commodo magna consequat risus pharetra, at interdum velit congue. Morbi sed leo congue, placerat libero ut, pretium lorem. Fusce maximus nisl eu lectus lacinia malesuada. Ut placerat eget odio eget commodo. Duis venenatis tincidunt blandit. Ut vitae hendrerit massa. Donec elementum semper dolor quis varius. Nam sollicitudin lacus vel elit porttitor mollis.</textarea>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="accept_terms" required="required" id="signup-terms" data-validation="required" data-validation-error-msg="Vous devez accepter les conditions d'utilisations !">
								J'ai lu et j'accepte les <a href="#">conditions d'utilisations</a> et la <a href="#">politique de confidentialité</a> de webportal
							</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label>Code de vérification</label>
						<div class="g-recaptcha" data-sitekey="6Lc2cBoTAAAAAF9TTSA9CRMbg-jss8X-CmDy_eXI"></div>
						<p class="help-block">Vérifiez que vous êtes un humain</p>
					</div>
				</div>

				<input type="submit" name="register" class="btn btn-primary" value="S'inscrire">
			</form>
		</div>
	</div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/jquery.form-validator.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>
