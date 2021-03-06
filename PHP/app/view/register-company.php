<?php
	if (!App::isLogged()) :
		if (isset($_POST['register'])) {
			$recaptchaClass = new Recaptcha('6Lc2cBoTAAAAAFUipDOigbn4PrJIScG6bwUqWbTQ');

			if (isset($_FILES['logo'])) {
				$my_file = basename($_FILES['logo']['name']);
				$max_file_size = 6000000;
				$file_size = filesize($_FILES['logo']['tmp_name']);
				$file_ext = strrchr($_FILES['logo']['name'], '.');
			}

			if (isset($_POST['name']) && !empty($_POST['name']) && preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['name']) &&
	    		isset($_POST['email']) && !empty($_POST['email']) && preg_match("#^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']) &&
	    		isset($_POST['email-confirm']) && $_POST['email-confirm'] == $_POST['email'] &&
	    		!Company::checkEmailExist($_POST['email']) &&
	    	   	isset($_POST['password']) && !empty($_POST['password']) && preg_match("#^[a-zA-Z\@._-]{8}#", $_POST['password']) &&
	    	   	isset($_POST['password-confirm']) && $_POST['password-confirm'] == $_POST['password'] &&
	    	   	isset($_POST['country'] ) && !empty($_POST['country']) &&
	    	   	isset($_POST['city']) && !empty($_POST['city']) && preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['city']) &&
	    	   	isset($_POST['description']) && preg_match("#^[a-zA-Z0-9._-]{2,128}#", $_POST['description']) &&
	   	   		preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $_POST['website']) && 
	    	   	isset($_POST['accept_terms']) &&
	    	   	isset($_POST['g-recaptcha-response'])
	    	   ) {

				try {
					$addCompany = Company::addCompany($_POST['name'], $_POST['email'], $_POST['country'], $_POST['city'], $_POST['description'], $_POST['password'], $_POST['website']);

					if ($addCompany) {
						if (isset($_FILES['logo'])) {
							if ($file_ext == '.jpg' || $file_ext == '.png') {
								if ($file_size < $max_file_size) {
									$companyId = Company::getCompanyIDByEmail($_POST['email']);

		          					$folder = 'uploads/companies';
		          					
		          					if ($file_ext == '.jpg') {
		          						$file = $folder . '/' . $companyId->id . '.jpg';
		          					}

		        			  		if ($file_ext == '.png') {
		        			  			$file = $folder . '/' . $companyId->id . '.png';
		        			  		}

		        			 		move_uploaded_file($_FILES['logo']['tmp_name'], $file);
		        			 		$editLogo = Company::editLogo($companyId->id, $file);

									if ($editLogo) {
										$msg->success('Votre inscription s\'est bien déroulée ! Votre compte doit maintenant être validé par l\'administrateur du site', 'index.php?page=home');
									}
								}

								else {
									$msg->error('Votre logo est trop lourd, choisissez un autre fichier', 'index.php?page=register-company');
								}
							}

							else {
								$msg->error('Le logo doit être au format JPG ou PNG', 'index.php?page=register-company');
							}
						}
					}

					else {
						$msg->error('Votre inscription a rencontré un problème. Veuillez réessayer.', 'index.php?page=register-company');
					}

					App::redirect('index.php?page=home');
				}
				catch(PDOException$e) {
					echo '<p>Erreur : ' . $e->getMessage() . '</p>';
					die();
				}
			}
			else {
				if ((!isset($_POST['name']) || empty($_POST['name'])) || 
				(!isset($_POST['city']) || empty($_POST['city'])) ||
				(!isset($_POST['email']) || empty($_POST['email'])) ||
				(!isset($_POST['email-confirm']) || empty($_POST['email-confirm'])) ||
				(!isset($_POST['password']) || empty($_POST['password'])) ||
				(!isset($_POST['password-confirm']) || empty($_POST['password-confirm'])) ||
				(!isset($_POST['country']) || empty($_POST['country'])) ||
				(!isset($_POST['description']))){
					$msg->error('Vous devez remplir tous les champs obligatoires', 'index.php?page=register-company');
				}

				if (!preg_match("#^[a-zA-Z._-]{2,32}#", $_POST['name'])) {
					$msg->error('Veuillez entrer un nom approprié', 'index.php?page=register-company');
				}

				if (!preg_match("#^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])) {
					$msg->error('Veuillez entrer un email approprié', 'index.php?page=register-company');
				}

				if (Company::checkEmailExist($_POST['email'])) {
					$msg->error('Cette adresse email est déjà utilisée', 'index.php?page=register-company');
				}

				if (!preg_match("#^[a-zA-Z\@._-]{8}#", $_POST['password'])) {
					$msg->error('Veuillez entrer un mot de passe approprié', 'index.php?page=register-company');
				}

				if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $_POST['website'])){
					$msg->error('Veuillez entrer une adresse web appropriée', 'index.php?page=register-company');
				}

				if ($_POST['email'] != $_POST['email-confirm']) {
					$msg->error('L\'adresse email doit correspondre', 'index.php?page=register-company');
				}

				if ($_POST['password'] != $_POST['password-confirm']) {
					$msg->error('Le mot de passe doit correspondre', 'index.php?page=register-company');
				}

				if (!isset($_POST['accept_terms'])) {
					$msg->error('Vous devez accepter les conditions d\'utilisation', 'index.php?page=register-company');
				}

				if (!isset($_POST['g-recaptcha-response'])) {
					$msg->error('Vous devez confirmer que vous n\'êtes pas un robot', 'index.php?page=register-company');
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
			<small>en tant qu'entreprise</small>
		</h1>
	</div>

	<div class="row">
		<div class="col-md-8">
			<form name="login" method="POST" action="index.php?page=register-company" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-12">
						<label for="signup-name">Nom*</label>
						<input type="text" name="name" class="form-control" required="required" id="signup-name" placeholder="Nom" data-validation="length" data-validation-length="2-30"  data-validation-error-msg="Entrez le nom de votre entreprise !">
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
					<div class="col-md-6">
						<label for="signup-city">Ville*</label>
						<input type="text" name="city" class="form-control" required="required" id="signup-city" placeholder="Ville" data-validation="city"  data-validation-error-msg="Ville invalide !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<label for="signup-logo">Logo</label>
						<input type="file" name="logo" id="signup-logo" placeholder="Insérer votre logo" required="required" data-validation-error-msg="Vous devez insérer un logo !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-description">Description*</label>
						<textarea name="description" id="signup-description" class="form-control" rows="3" placeholder="Description" data-validation-length="2-30"  data-validation-error-msg="Rédigez une description de l'entreprise !" required></textarea>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-website">Site web*</label>
						<input type="text" name="website" class="form-control" id="signup-website" placeholder="Site web" data-validation="length" data-validation-length="16-128" data-validation-error-msg="Vous devez entrer un site web !">
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-terms">Conditions d'utilisation</label>
						<textarea class="form-control">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse at quam eget dolor mattis dapibus sit amet in ante. Nunc mattis euismod dapibus. Nunc ullamcorper finibus metus dapibus tristique. Maecenas malesuada bibendum metus, quis lacinia nisl consectetur in. In nec ex ac nulla sagittis tempus sed eu sem. Maecenas ac tortor eu justo volutpat tincidunt. Etiam commodo magna consequat risus pharetra, at interdum velit congue. Morbi sed leo congue, placerat libero ut, pretium lorem. Fusce maximus nisl eu lectus lacinia malesuada. Ut placerat eget odio eget commodo. Duis venenatis tincidunt blandit. Ut vitae hendrerit massa. Donec elementum semper dolor quis varius. Nam sollicitudin lacus vel elit porttitor mollis.</textarea>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="accept_terms" required="required" id="signup-terms" data-validation="required" data-validation-error-msg="Vous devez accepter les conditions d'utilisations !">
								J'ai lu et j'accepte les <a href="#">conditions d'utilisations</a> et la <a href="#">politique de confidentialité</a> de PhoneDeals.
							</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<label for="signup-terms">Code de vérification</label>
						<div class="g-recaptcha" data-sitekey="<?php echo Settings::getRecaptchaKey('public'); ?>"></div>
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
<?php
	else:
		App::redirect('index.php?page=home');
	endif;
?>
