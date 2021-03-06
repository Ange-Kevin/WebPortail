<div class="container">
	<div class="row">
		<?php
			$id = htmlentities($_GET['id']);

			if (!isset($id) || empty($id)) {
				App::redirect('index.php?page=find-internship');
			}
			
			else {
				$internship = Internship::getInternshipById($id);
		?>
			<div class="col-sm-12">
				<?php $company = Company::getCompanyById($id); ?>
				<?php $skill = Skill::getSkillById($id); ?>
				<h1 style="margin-bottom: 0;"><?php echo $internship->name; ?></h1>
				<span style="color: #bfbfbf; display: block; font-size: 18px;"><?php echo $skill->name; ?></span>

				<h3>Entreprise</h3>
				<p><?php echo $company->name; ?></p>

				<h3>Description</h3>
				<p><?php echo $internship->description; ?></p>

				<h3>Lieu du stage</h3>
				<p><?php echo $internship->address . '<br/>' . $internship->city . ', ' . $internship->zip_code; ?></p>

				<button type="submit" class="btn btn-primary btn-lg" style="margin-top:30px;">Contacter</button>
			</div>
		<?php
			}
		?>
	</div>
</div>
