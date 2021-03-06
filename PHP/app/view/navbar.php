<nav class="navbar navbar-inverse">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php?page=home"><img src="img/logo.png" alt="Logo" style="float: left; margin-right: 10px; width: 20px;"><?php echo Settings::getWebsiteName(); ?></a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li<?php App::isCurrentPage('home'); ?>><a href="index.php?page=home"><?php echo $language->translate('home'); ?></a></li>
				<li<?php App::isCurrentPage('faq'); ?>><a href="index.php?page=faq"><?php echo $language->translate('faq'); ?></a></li>
				<li<?php App::isCurrentPage('testimonials'); ?>><a href="index.php?page=testimonials"><?php echo $language->translate('testimonials'); ?></a></li>
				
				<li class="dropdown"
					<?php App::isCurrentPage('formations-france');
					     	App::isCurrentPage('formations-ireland'); ?>>
						  	<a id="formations-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								Formations des étudiants
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" aria-labelledby="student-dropdown">
								<li><a href="index.php?page=formations-france">France</a></li>
								<li><a href="index.php?page=formations-ireland">Irlande</a></li>
							</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if (App::isLogged()) : ?>
					<?php $member = App::getMember(); ?>
					<?php if (get_class($member) == 'Student') : ?>
						<li class="dropdown">
							<a id="student-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<?php echo $member->first_name; ?>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" aria-labelledby="student-dropdown">
								<li><a href="index.php?page=profile-student">Profil</a></li>
								<li><a href="index.php?page=internship-request">Ma recherche de stage <span data-toggle="tooltip" data-placement="bottom" title="Recherche <?php if(!$member->available){echo 'dés';} ?>activée" style="background: <?php if($member->available) {echo '#27ae60';} else {echo '#e74c3c';} ?>; display: inline-block; vertical-align: middle; margin-left: 10px; border-radius: 8px; height: 8px; width: 8px;"></span></a></li>
								<li><a href="index.php?page=find-internship">Rechercher un stage</a></li>
								<li><a href="index.php?page=find-company">Rechercher une entreprise</a></li>
								<?php if (App::isAdmin()) : ?>
									<li class="divider" role="separator"></li>
									<li><a href="index.php?page=admin/home">Administration</a></li>
								<?php endif; ?>
							</ul>
						</li>
					<?php else : ?>
						<li class="dropdown">
							<a id="company-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<?php echo $member->name; ?>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" aria-labelledby="company-dropdown">
								<li><a href="index.php?page=profile-company">Profil</a></li>
								<li><a href="index.php?page=my-internships">Stages proposés</a></li>
								<li><a href="index.php?page=find-student">Rechercher des étudiants</a></li>
							</ul>
						</li>
					<?php endif; ?>
					<li><a href="index.php?page=signout">Déconnexion</a></li>
				<?php else : ?>
					<li class="dropdown">
						<a id="login-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							Me connecter
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu" aria-labelledby="login-dropdown">
							<li><a href="index.php?page=login&amp;type=student">Étudiant</a></li>
							<li><a href="index.php?page=login&amp;type=company">Entreprise</a></li>
						</ul>
					</li>
				<?php endif; ?>
				
				<li class="dropdown">
					<a id="lang-dropdown" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<?php echo $language->getCurrentLanguage()['name']; ?>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" aria-labelledby="lang-dropdown">
						<li><a href="index.php?page=<?php echo $_GET['page']; ?>&amp;lang=<?php echo $language->getOtherLanguage()['code']; ?>"><?php echo $language->getOtherLanguage()['name']; ?></a></li>
					</ul>
				</li>
            </ul>
		</div>
	</div>
</nav>
