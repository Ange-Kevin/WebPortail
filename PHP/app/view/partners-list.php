<?php
	if (!isset($_GET['type']) || $_GET['type'] != 'awaiting') {
		$_GET['type'] = 'activated';
	}

	$type = htmlentities($_GET['type']);
?>
<div class="col-md-12">
	<div class="page-header">
		<h1>
			Liste des partenaires
		</h1>
	</div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Logo</th>
				<th>Pays</th>
				<th>Date</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<?php
			foreach (Partner::getPartnersList() as $partner) {
				echo '<tr>';
					echo '<td>' . $partner->name . '</td>';
					echo '<td>' . $partner->logo . '</td>';
					echo '<td>' . $partner->country . '</td>';
					echo '<td>' . $partner->register_date . '</td>';	
					echo '<td><a href="index.php?page=admin/partner-edit&amp;id=' . $partner->id . '" data-toggle="tooltip" title="Modifier"><i class="fa fa-pencil"></i></a></td>';
					echo '<td><a href="#" title="Supprimer" data-action="delete" data-toggle="tooltip" title="Supprimer"><i class="fa fa-trash"></i></a></td>';
				echo '</tr>';
			}
		?>
	</table>
</div>

<script>
	$('[data-action="delete"]').click(function(e) {
		e.preventDefault();

		eAjax(
			'public/webservice/admin/partner-delete.php',
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
