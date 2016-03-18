<?php
	$student = Student::getStudentById($_GET['id']);
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="row" style="margin-bottom: 15px;">
				<div class="col-md-12">
					<h1><?php echo $student->first_name . ' ' . $student->last_name; ?></h1>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9">
					<form class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">Pays</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $student->country; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Domaine</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo Skill::getSkillById($student->skill)->name; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $student->email; ?></p>
							</div>
						</div>

						<div class="form-group">
							<label for="profile-cv" class="col-sm-2 control-label">CV</label>
							<div class="col-sm-10">
								<a class="btn btn-primary" name="CV" href="/uploads/cv/<?php echo $student->id; ?>.pdf">Télécharger</a>
							</div>
						</div>

						<div class="form-group">
							<label for="profile-portfolio" class="col-sm-2 control-label">Portfolio</label>
							<div class="col-sm-10">
								<p class="form-control-static"><a href="<?php echo $student->portfolio; ?>"><?php echo $student->portfolio; ?></a></p>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Date d'inscription</label>
							<div class="col-sm-10">
								<p class="form-control-static"><?php echo $student->register_date; ?></p>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>