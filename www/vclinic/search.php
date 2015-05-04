<?php
	require_once('../../include/vclinic/usersession.php');

	$showerror= false;
	$error = '';

	define('VC_PATTERN_SEARCH', '/[^A-Za-z]/');

	if(!isset($_GET['query'])) {
		header('Location: '.VC_LOCATION);
		exit();
	}

	if(empty($_GET['query'])) {
		$showerror = true;
		$error = 'Search query cannot be blank.';
	}
	else {
		$query = "SELECT patient_id AS id, NULL AS type, fname, lname, gender, birthdate, occupation, email, phone, picture FROM vc_patient WHERE ";

		$original_search_query = $_GET['query'];

		$processed_search_query = preg_replace(VC_PATTERN_SEARCH, ' ', $original_search_query);
		$search_words = explode(' ', $processed_search_query);
		$final_search_words = array();
  		foreach ($search_words as $word) {
	    	if (!empty($word)) {
	      		array_push($final_search_words, $word);
	    	}
  		}

  		$where_list = array();
  		if(count($final_search_words) > 0) {
  			foreach($final_search_words as $word) {
  				array_push($where_list, "fname LIKE '%$word%' OR lname LIKE '%$word%'");
  			}

  			$where_clause = implode(' OR ', $where_list);
  			$query .= $where_clause." UNION ALL SELECT user_id AS id, type, fname, lname, gender, birthdate, NULL AS occupation, email, phone, picture FROM vc_user WHERE ".$where_clause." ORDER BY lname, fname";

  			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to database.');

  			$data = mysqli_query($dbc, $query);

  			if(mysqli_num_rows($data) < 1) {
  				$showerror = true;
  				$error = 'Your search returned no results.';
  			}
  		}
  		else {
  			$showerror = true;
  			$error = "No valid search terms were entered.";
  		}
	}

	$pagetitle = 'Search Results';
?>

<?php require_once(VC_INCLUDE.'startdocument.php'); ?>

<link rel="stylesheet" href="stylesheets/user.css">
<link rel="stylesheet" href="stylesheets/profile.css">
<link rel="stylesheet" href="stylesheets/search.css">

<?php require_once(VC_INCLUDE.'header.php'); ?>

<div id="banner">
	<h2><?php echo $pagetitle; ?></h2>
</div>
<?php require_once(VC_INCLUDE.'chat.php'); ?>
<div id="main-content">
	<?php
		if($showerror) 
			echo '<div id="showerror"><p class="error">'.$error.'</p></div>'."\n";
		else {
			while ($row = mysqli_fetch_array($data)) {
	?>
	<div class="result">
		<div class="result-picture">
			<img src="<?php echo VC_UPLOADPATH.$row['picture']; ?>" alt="Profile Picture" height="150" width="150">
		</div>
		<div class="result-details-right">
			<table>
				<tr>
					<th>Phone No.: </th>
					<td><?php if(empty($row['phone'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['phone']; ?></td>
				</tr>
				<tr>
					<th>Email: </th>
					<td><?php if(empty($row['email'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['email']; ?></td>
				</tr>
				<?php if(empty($row['type'])) { ?>
				<tr>
					<th>Occupation: </th>
					<td><?php if(empty($row['occupation'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['occupation']; ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="result-details">
			<?php if(empty($row['type'])) { ?>
			<h3 class="nowrap"><a title="<?php echo $row['fname'].' '.$row['lname']; ?>" href="patient.php?patient_id=<?php echo $row['id']; ?>"><?php echo $row['fname'].' '.$row['lname']; ?></a></h3>
			<?php } else { ?>
			<h3 class="nowrap"><a title="<?php echo $row['fname'].' '.$row['lname']; ?>" href="profile.php?user_id=<?php echo $row['id']; ?>"><?php echo $row['fname'].' '.$row['lname']; ?></a></h3>
			<?php } ?>
			<table>
				<tr>
					<th>Type: </th>
					<td><?php if(empty($row['type'])) echo 'Patient'; else if($row['type'] == VC_TECHNICIAN) echo 'Technician'; else echo 'Doctor'; ?></td>
				</tr>
				<tr>
					<th>Gender: </th>
					<td><?php if(empty($row['gender'])) echo '<span class="nulldata">Not set.</span>'; else if($row['gender'] == 'm') echo 'Male'; else echo 'Female'; ?></td>
					
				</tr>
				<tr>
					<th>Birth Date: </th>
					<td><?php if(empty($row['birthdate'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row['birthdate']; ?></td>
					
				</tr>
			</table>
		</div>	
	</div>
	<?php
			}
		}
	?>
</div>

<?php require_once(VC_INCLUDE.'footer.php'); ?>
