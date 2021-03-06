<?php
if(App::isAdmin()){
	if (!isset($_GET['type']) || $_GET['type'] != 'awaiting') {
		$_GET['type'] = 'activated';
	}

	$type = htmlentities($_GET['type']);
?>
<div class="col-md-12">
	<div class="page-header">
		<h1>
			Liste des entreprises
			<?php
				if ($type == 'awaiting') {
					echo '<small>En attente d\'activation</small>';
					echo '<a href="index.php?page=admin/companies-list&amp;type=activated" class="btn btn-primary pull-right">Voir les entreprises confirmées <span class="badge">' . count(Company::getActivatedCompanies(true)) . '</span></a>';
				}

				else {
					echo '<small>Confirmées</small>';
					echo '<a href="index.php?page=admin/companies-list&amp;type=awaiting" class="btn btn-primary pull-right">En attente de confirmation <span class="badge">' . count(Company::getActivatedCompanies(false)) . '</span></a>';
				}
			?>
		</h1>
	</div>
	
	<?php
		if ($type == 'activated') :
			if (Company::getActivatedCompanies(true)) :
	?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Email</th>
				<th>Pays</th>
				<th>Ville</th>
				<th>Description</th>
				<th>Site Internet</th>
				<th>Date d'inscription</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<?php
			foreach (Company::getActivatedCompanies() as $company) {
				echo '<tr data-id=' . $company->id . '>';
					echo '<td>' . $company->name . '</td>';
					echo '<td><a href="mailto:' . $company->email . '">' . $company->email . '</a></td>';
					echo '<td>' . $company->country . '</td>';
					echo '<td>' . $company->city . '</td>';
					echo '<td>' . $company->description . '</td>';
					echo '<td><a href="' . $company->website . '" target="_blank">' . $company->website . '</a></td>';
					echo '<td>' . $company->register_date . '</td>';	
					echo '<td><a href="index.php?page=admin/company-edit&amp;id=' . $company->id . '" data-toggle="tooltip" title="Modifier"><i class="fa fa-pencil"></i></a></td>';
					echo '<td><a href="#" title="Supprimer" data-action="delete" data-toggle="tooltip" title="Supprimer"><i class="fa fa-trash"></i></a></td>';
				echo '</tr>';
			}
		?>
	</table>
	<a href="index.php?page=admin/add-company&amp;type=<?php echo $type; ?>" class="btn btn-primary">Ajouter une entreprise</a>
	<?php
			else:
				echo 'Aucune entreprise n\'a été confirmée.';
			endif;
		else:
			if (Company::getActivatedCompanies(false)) :
	?>
		<table class="table table-striped">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Email</th>
				<th>Pays</th>
				<th>Ville</th>
				<th>Description</th>
				<th>Site Internet</th>
				<th>Date d'inscription</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
			<?php
				foreach (Company::getActivatedCompanies(false) as $company) {
					echo '<tr data-id="' . $company->id . '">';
						echo '<td>' . $company->name . '</td>';
						echo '<td><a href="mailto:' . $company->email . '">' . $company->email . '</a></td>';
						echo '<td>' . $company->country . '</td>';
						echo '<td>' . $company->city . '</td>';
						echo '<td>' . $company->description . '</td>';
						echo '<td><a href="' . $company->website . '" target="_blank">' . $company->website . '</a></td>';
						echo '<td>' . $company->register_date . '</td>';
						echo '<td><a href="index.php?page=admin/company-activate&amp;id=' . $company->id . '"><i class="fa fa-check" data-toggle="tooltip" title="Activer"></i></a></td>';
						echo '<td><a href="index.php?page=admin/company-edit&amp;id=' . $company->id . '"><i class="fa fa-pencil"></i></a></td>';
						echo '<td><a href="#" title="Supprimer" data-action="delete" data-toggle="tooltip" title="Supprimer"><i class="fa fa-trash"></i></a></td>';

					echo '</tr>';
				}
			?>
		</table>
	<?php
			else:
				echo 'Aucune entreprise n\'est en attente de confirmation.';
			endif;
		endif;
	}
	else{
		App::getHeader(404);
	}
	?>
</div>

<script>
	$('[data-action="delete"]').click(function(e) {
		e.preventDefault();

		eAjax(
			'public/webservice/admin/company-delete.php',
			{'delete': true, 'id': $(this).parent().parent().data('id')},
			'deleteRow'
		);
	});

	var eAjaxData = '';

	function eAjax(url, parameters, callback) {
	    if (!confirm('Êtes-vous sûr ?')) {
	        return false;
	    }

	    $.post(url, parameters, function(data) {
	        eAjaxData = data;
	        var func = callback + "()";
	        eval(func);
	    }, "json");
	}

	function deleteRow() {
	    if (eAjaxData.status == 'true') {
	        $('[data-id="' + eAjaxData.id + '"]').fadeTo('slow', 0.01).slideUp('slow');
	    }
	    
	    else {
	        alert(eAjaxData.status);
	    }
	}
</script>
