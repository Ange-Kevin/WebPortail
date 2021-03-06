<?php
	if (App::isCompany()) :
		$company = Company::getCompanyById($_SESSION['id']);

		if (isset($_POST['delete'])){
			if (isset($_POST['password']) && $_POST['password'] == $_POST['password-confirm']) {
				if (Bcrypt::checkPassword($_POST['password'], $student->password)) {
					Company::deleteCompany($_SESSION['id']);

					session_unset();
					$msg->success('Votre compte à bien été supprimé', 'index.php?page=home');
				}

				else {
					echo $msg->error('Le mot de passe entré est incorrect, veuillez réessayer', 'index.php?page=profile-company');
				}
			}

			else {
				echo $msg->error('Les deux mots de passe ne correspondent pas', 'index.php?page=profile-company');
			}
		}

		if (isset($_POST['edit'])) :
			if ($_POST['password'] == $_POST['password-confirm']) {
				if (Bcrypt::checkPassword($_POST['password'], $company->password)) {
					if (isset($_POST['new-password']) && $_POST['new-password']!=''){
						if (preg_match("#^[a-zA-Z\@._-]{2,32}#", $_POST['new-password'])){
							$new_password = Bcrypt::hashPassword($_POST['new-password']);
							Company::changePassword($new_password, $company->id);							
							$msg->success('Votre mot de passe a bien été modifié', 'index.php?page=profile-company');
						}

						else {
							$msg->error('Veuillez entrer un nouveau mot de passe approprié', 'index.php?page=profile-company');
						}	
					}

					PDOConnexion::setParameters('stages', 'root', 'root');
					$db = PDOConnexion::getInstance();
					$sql = "
						UPDATE company
						SET description = :description,
							website = :website
						WHERE id = :id
					";
					$sth = $db->prepare($sql);
					$sth->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Company');
					$sth->execute(array(
						':id' => $company->id,
						':description' => $_POST['description'],
						':website' => $_POST['website']
					));

					if ($sth) {
						$msg->success('Vos informations ont bien été modifiées', 'index.php?page=profile-company');
					}
				}

				else {
					echo $msg->error('Le mot de passe entré est incorrect, veuillez réessayer.', 'index.php?page=profile-company');
				}
			}

			else {
				echo $msg->error('Les deux mots de passes ne correspondent pas', 'index.php?page=profile-company');
			}
		else:
?>
	<header id="header">
		<div class="section-title">
			<h1>Profil</h1>
		</div>
	</header>

	<div id="main-content" class="section-content">
		<div class="container">
			<div class="row">
				<div class="col-md-9">
					<form action="index.php?page=profile-company" method="POST" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">Nom</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $company->name; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $company->email; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Pays</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $company->country; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Ville</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $company->city; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Description</label> 
							<div class="col-sm-10">
								<textarea class="form-control" id="description" name="description" placeholder="Décrivez votre entreprise en quelques lignes"><?php echo $company->description; ?></textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="profile-portfolio" class="col-sm-2 control-label">Site internet</label>
							<div class="col-sm-10">
								<input type="url" class="form-control" id="website" name="website" value="<?php echo $company->website; ?>" placeholder="Lien du site web">
							</div>
						</div>

						<div class="form-group">
							<label for="profile-new-password" class="col-sm-2 control-label">Nouveau mot de passe</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="profile-new-password" name="new-password" placeholder="Si vous désirez changer de mot de passe">
							</div>
						</div>

						<div class="form-group">
							<label for="profile-password" class="col-sm-2 control-label">Mot de passe*</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="profile-password" name="password" placeholder="Mot de passe">
							</div>
						</div>

						<div class="form-group">
							<label for="profile-password-confirm" class="col-sm-2 control-label">Confirmer*</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="profile-password-confirm" name="password-confirm" placeholder="Confirmer le mot de passe">
								<p class="help-block">Entrez votre mot de passe pour confirmer votre identité et valider les changements</p>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary" name="edit">
									<i class="fa fa-pencil"></i>
									Mettre à jour
								</button>
								<button class="btn btn-danger" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse" name="delete">
									<i class="fa fa-trash"></i>
									Supprimer votre profil
								</button>
							</div>

						</div>

						<div class="collapse" id="collapse">
						  <div class="form-group">
						    La suppression de votre profil est irreversible, vous ne pourrez plus avoir accès à vos données et votre compte sera supprimé.
						    <button type="submit" class="btn btn-default btn-sm" name="delete" style="color:#d9534f"><i class="fa fa-trash"></i> Supprimer</button>
						  </div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
		endif;
	else :
		App::getHeader(404);
	endif;
?>