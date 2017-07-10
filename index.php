<?php
try  
{
	$bdd = new PDO('mysql:host=localhost;dbname=annuaire;charset=utf8', 'root', 'azerty');
}
catch (Exception $e)
{
	die('Erreur : ' . $e->getMessage());
}

$mname=$_POST["nomA"];
$mfirstname=$_POST["prenomA"];
$mjob=$_POST["metierA"];
$mbirth=$_POST["dateA"];
$madress=$_POST["adresseA"];
$mphone=$_POST["telA"];
$id=$_POST["id"];

$name=$_POST["nom"];
$firstname=$_POST["prenom"];
$job=$_POST["metier"];
$birth=$_POST["date"];
$adress=$_POST["adresse"];
$phone=$_POST["tel"];

if (isset($_POST['add'])) {
	$req = $bdd->prepare('INSERT INTO user VALUES(NULL,:nom, :prenom, :metier, :adresse, :anniv , :phone)');
	if ($req->execute(array(
		'nom' => $name,
		'prenom' => $firstname,
		'metier' => $job,
		'adresse' => $adress,
		'anniv' => $birth,
		'phone' => $phone
		))) {
		echo "contact ajouté avec succès.";
}else{
	$mname=$name;
	$mfirstname=$firstname;
	$mjob=$job;
	$mbirth=$birth;
	$madress=$adress;
	$mphone=$phone;
	echo "Une erreur est survenue veuillez réessayé dans quelques minutes";
}
$req->execute(array(
	'nom' => $name,
	'prenom' => $firstname,
	'metier' => $job,
	'adresse' => $adress,
	'anniv' => $birth,
	'phone' => $phone
	));
foreach ($_POST["cheklist"] as $value) {

	$reqbis = $bdd->prepare('INSERT INTO appartenir VALUES(LAST_INSERT_ID(), :groupe)');
	$reqbis->execute(array(
		'groupe' => $value
		));
}
}
if (isset($_POST['update'])) {
	try{
		$upd="UPDATE user SET nom='".$name."',prenom='".$firstname. "',metier='".$job. "', adresse='".$adress. "',anniv='".$birth. "',phone='".$phone. "' WHERE id=".$id;
		if ($bdd->exec($upd)) {
			echo "Modification effectué avec succès";
		}else{
			echo "Une erreur est survenue";
		}
		
		$bdd->exec("DELETE FROM appartenir WHERE fk_user=".$id."");
		foreach ($_POST["cheklist"] as $key => $value) {

			$updsuite="INSERT INTO appartenir VALUES(".$id.",".$value.")";
			if($bdd->exec($updsuite)){
				echo "succes";
			}
		}
	}catch(Exception $e){
		echo $e->getMessage();
	}
}
if (isset($_POST['supp'])) {
	$del="DELETE FROM user WHERE id= ".$_POST["id"];
	if ($bdd->exec($del)) {
		echo "Suppression effectué";
	}else{
		echo "Une erreur est survenue";
	}
}
if (isset($_POST["newgroup"])){
	$req = $bdd->prepare('INSERT INTO groupe VALUES(NULL, :nomg)');
	$req->execute(array(
		'nomg' => $_POST["newgroup"]
		));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Annuaire</title>
	<link rel="stylesheet" href="marco.css">
</head>
<body>
	<div class="form">
		<div class="untiers">
			<div>
				<h3>Formulaire de contact</h3>
			</div>
			<form action="" method="post">
				<fieldset>
					<div style="display: flex;">
						<ul>
							<li><label for="nom">Nom</label></li>
							<li><label for="prenom">Prénom</label></li>
							<li><label for="metier">Métier</label></li>
							<li><label for="date">Date de Naissance</label></li>
							<li><label for="adresse">Adresse</label></li>
							<li><label for="tel">Téléphone</label></li>
						</ul>
						<ul>
							<li><input type="text" id="nom" name="nom" placeholder="Exemple Nemare" value="<?php echo $mname; ?>"	></li>
							<li><input type="text" id="prenom" name="prenom" value="<?php echo $mfirstname; ?>" placeholder="Exemple Jean"></li>
							<li><input type="text" id="metier" name="metier" value="<?php echo $mjob; ?>" placeholder="Entrer le metier"></li>
							<li><input class="a" type="date" id="date" name="date" value="<?php echo $mbirth; ?>"/></li>
							<li><input type="text" id="adresse" name="adresse"  value="<?php echo $madress; ?>"placeholder="Entrer l'adresse"></li>
							<li><input type="tel" id="tel" name="tel" value="<?php echo $mphone; ?>" placeholder="0102030405"></li>
						</ul>
					</div>
					<div >
						<label for="groupe">Ajout Groupe</label>
						<?php
						if (isset($_POST['id'])) {
							echo "<h3> Veuillez rajouter le(s) groupe(s)</h3>";
						} 
						?>
						<div id="groupe">
							<?php 
							$groupe = $bdd->query("SELECT * FROM groupe");
							while ($donnees=$groupe->fetch()) {
								echo '
								<label for="'.$donnees["nomg"].'">'.$donnees["nomg"].': </label>
								<input type="checkbox" id="'.$donnees["nomg"].'" name="cheklist[]" value="'.$donnees["idg"].'">';
							}
							?>
						</div>
						<div style="display: none;">
							<input type="text" name="id" value="<?php echo $id; ?>">
							<input type="text" name="idug" value="<?php echo $midug; ?>">
						</div>
					</div>
					<div >
						<?php 
						if ( empty($id)) {						
							echo '<input type="submit" value="Ajouter" name="add">';
						}
						if ($_POST["id"]) {
							echo	'<input type="submit" value="Supprimer" name="supp">
							<input type="submit" value="Modifier" name="update">';
						}
						?>
					</div>
				</fieldset>
			</form>
		</div>
		<div class="deuxtiers">
				<fieldset>
					<h5>Créer un nouveau groupe</h5>
					<form action="" method="post">
						<label for="newgroup">Nouveau groupe: </label>
						<input type="text" id="newgroup" name="newgroup">
						<div>
							<input type="submit"/>
						</div>
					</form>
				</fieldset>
		</div>
		<div class="troistiers">
				<fieldset>
					<h5>Rechercher les Contacts</h5>
					<form action="" method="post">
						<label for="all">Tous les contact</label>
						<input type="radio" id="all" name="test" value="all"/>
						<?php 
						$groupe = $bdd->query("SELECT * FROM groupe");
						while ($donnees=$groupe->fetch()) {
							echo '
							<label for="a'.$donnees["nomg"].'">'.$donnees["nomg"].': </label>
							<input type="radio" id="a'.$donnees["nomg"].'" name="test" value="'.$donnees["idg"].'" />';
						}
						?>
						<div>			
							<button type="submit" name="aff">Rechercher</button>
						</div>
					</form>
				</fieldset>
		</div>
	</div>
	<?php
	if (isset($_POST["aff"])) {
		if ($_POST['test'] == "all") {
			$reponse= $bdd->query("SELECT * FROM user ");
		}else{
			$reponse= $bdd->query("SELECT * FROM user INNER JOIN appartenir AS a ON user.id= a.fk_user WHERE fk_group=".$_POST['test']." ");
		}
		echo
		"<table>
		<thead>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Métier</th>
			<th>Date de Naissance</th>
			<th>Adresse</th>
			<th>Téléphone</th>
			<th>Modifier</th>
		</thead>
		<tbody>";
			while ($donnees=$reponse->fetch()) {
				echo 
				'<tr><form action="" method="post"><td ><input type="text" name="nomA" value="'.$donnees["nom"].'" readonly></td>
				<td><input type="text" name="prenomA"value="'.$donnees["prenom"].'" readonly></td>
				<td><input type="text" name="metierA" value="'.$donnees["metier"].'" readonly></td>
				<td><input type="date" name="dateA" value="'.$donnees["anniv"].'" readonly></td>
				<td><input type="text" name="adresseA" value="'.$donnees["adresse"].'" readonly></td>
				<td><input type="text" name="telA" value="'.$donnees["phone"].'" readonly></td>
				<td><input type="submit" value="Modifier"></td>
				<td style=display:none><input type="text" name="id" value="'.$donnees["id"].'"></form></tr>';
				}
				echo	"</tbody>
			</table>
		</div>";
	}
	?>
</body>
</html>