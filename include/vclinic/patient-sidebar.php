<?php 
	$query = "SELECT vp.gender, vp.birthdate, vp.occupation, va.line1, va.line2, va.city, va.district, vas.code, va.pincode, vp.email, vp.phone, vp.picture FROM vc_patient AS vp LEFT JOIN vc_address AS va USING (address_id) LEFT JOIN vc_address_state AS vas USING (state_id) WHERE vp.patient_id=".$patient_id;
	$data_sidebar = mysqli_query($dbc, $query);

	if(mysqli_num_rows($data_sidebar) != 1) {
		echo '<p class="error">Some error occured.</p>';
		exit();
	}

	$row_sidebar = mysqli_fetch_array($data_sidebar);
?>
<div id="sidebar">
	<?php echo '<img src="'.VC_LOCATION.''.VC_UPLOADPATH.$row_sidebar['picture'].'" alt="Profile Picture">'."\n"; ?>
	<div id="details">
		<table>
			<tr>
				<th>Gender: </th>
				<td><?php if(empty($row_sidebar['gender'])) echo '<span class="nulldata">Not set.</span>'; else if($row_sidebar['gender'] == 'm') echo 'Male'; else echo 'Female'; ?></td>
			</tr>
			<tr>
				<th>Birth Date: </th>
				<td><?php if(empty($row_sidebar['birthdate'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row_sidebar['birthdate']; ?></td>
			</tr>
			<tr>
				<th>Phone No.: </th>
				<td><?php if(empty($row_sidebar['phone'])) echo '<span class="nulldata">Not Set.</span>'; else echo $row_sidebar['phone']; ?></td>
			</tr>
		</table>
		<div id="details-inner-1">
			<?php
				echo '<span class="heading">Email: </span>';
				if(empty($row_sidebar['email'])) 
					echo '<span class="nulldata lastline">Not set.</span>'; 
				else 
					echo '<span class="lastline">'.$row_sidebar['email'].'</span>';

				echo '<span class="heading">Occupation: </span>';
				if(empty($row_sidebar['occupation'])) 
					echo '<span class="nulldata lastline">Not set.</span>'; 
				else 
					echo '<span class="lastline">'.$row_sidebar['occupation'].'</span>';

				echo '<span class="heading">Address: </span>'."\n";
				if(empty($row_sidebar['line1']) && empty($row_sidebar['line2']) && empty($row_sidebar['city']) && empty($row_sidebar['district']) && empty($row_sidebar['code']) && empty($row_sidebar['pincode']))
					echo '<span class="nulldata">Not set.</span>'."\n";
				else {
					if(!empty($row_sidebar['line1']))
						echo $row_sidebar['line1'].'<br>';
					if(!empty($row_sidebar['line2']))
						echo $row_sidebar['line2'].'<br>';
					if(!empty($row_sidebar['city']))
						echo $row_sidebar['city'].'<br>';
					if(!empty($row_sidebar['district']))
						echo $row_sidebar['district'].'<br>';
					echo '<span class="lastline">';
					if(!empty($row_sidebar['code']))
						echo $row_sidebar['code'].' ';
					if(!empty($row_sidebar['pincode']))
						echo $row_sidebar['pincode'];
					echo '</span>'."\n";
				} 
			?>
		</div>
		<?php
			if($_SESSION['type'] == VC_TECHNICIAN) {
		?>		
		<div id="details-inner-2">
			<a title="Edit Details" href="<?php echo VC_LOCATION.'technician/editpatient.php?patient_id='.$_GET['patient_id']; ?>">Edit Details</a>
		</div>
		<?php
			}
		?>
	</div>
</div>