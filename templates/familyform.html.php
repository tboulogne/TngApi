<!--UPDATE FAMILY -->
			
			<?php
				
				$tngcontent = Upavadi_tngcontent::instance()->init();
				
				 //get and hold current user
				$currentperson = $tngcontent->getCurrentPersonId($person['personID']);
				$person = $tngcontent->getPerson($currentperson);
				$currentuser = ($person['firstname']. $person['lastname']);
				
				?>
			
				<a href="?personId=<?php echo $person['personID']; ?>"><span style="color:#D77600; font-size:14pt">			
				<?php echo "Welcome ". $currentuser; ?></span>
				</a><br>
	
				<?php
//get person details
				$person = $tngcontent->getPerson($personId);
				$person_birthdate = $person['birthdate'];
				$person_birthdatetr = ($person['birthdatetr']);
				$person_birthplace = $person['birthplace'];
				$person_deathdate = $person['deathdate'];
				$person_deathdatetr = ($person['deathdatetr']);
				$person_deathplace = $person['deathplace'];
				$person_name = $person['firstname'];
				$person_surname = $person['lastname'];
							
//get Person gotra
				$personRow = $tngcontent->getgotra($person['personID']);
				$person_gotra = $personRow['info'];
				
//get Description of Event type
				$EventRow = $tngcontent->getEventDisplay($event['display']);	
				$EventDisplay = $EventRow['display'];
	

				
// title for page	
				?>
				<span float="left" style= "font-type:bold; font-size:12pt">			
				<?php echo "Update Details for the Family of ". $person_name. $person_surname; ?></span>
				
				<?php

//get month of the events
				$currentmonth = date("m");
								
				if ($birthdatetr == '0000-00-00') {
				$birthmonth = null;
				} else {
				$birthmonth = substr($birthdatetr, -5, 2);
				}
				
	
				If ($currentmonth == $birthmonth) { $bornClass = 'born-highlight'; 
				} else { $bornClass="";
				}
				
				if ($deathdatetr == "0000-00-00") {
				$deathmonth = null;
				} else {
				$deathmonth = substr($deathdatetr, -5, 2);
				}
				
		
				If ($currentmonth == $birthmonth) { $bornClass = 'born-highlight';
				} else { $bornClass="";
				}
//Person dates and places		
				if ($person['birthdate'] == '') {
			$person_birthdate = "Unknown";
			}
			else {
			$person_birthdate == $person['birthdate'];
			}
			
				if ($person['living'] == '0' AND $person['deathdatetr'] !== '0000-00-00') 
					{
					$person_deathdate = " died: " . $person['deathdate'];
					} else {
					$person_deathdate = " died: date unknown";
					}
					if ($person['living'] == '1') {
					$person_deathdate = "  (Living)";
				}
			if ($person['birthplace'] == '') {
			$person_birthplace = "Unknown";
			}
			else {
			$person_birthplace == $person['birthplace'];
			}
			if ($person['deathplace'] == '' and $person['living'] == '0') {
			$person_deathplace = "Unknown";
			}
			else {
			$person_deathplace == $person['deathplace'];
			}	
				
//get familyuser
				if ($person['sex'] == 'M') {
					$sortBy = 'husborder';
				} else if ($person['sex'] == 'F') {
					$sortBy = 'wifeorder';
				} else {
					$sortBy = null;
				}
			
			$families = $tngcontent->getfamilyuser($person['personID'], $sortBy);
				
			?>		




<!--------Jquery smart wizard --------- 
<script type="text/javascript" src="<?php echo plugins_url('js/jquery-2.0.0.min.js', dirname(__FILE__)); ?>"></script>
--------->
<script type="text/javascript" src="<?php echo plugins_url('js/jquery.smartWizard.js', dirname(__FILE__)); ?>"></script>

<script type="text/javascript">
  $(document).ready(function() {
      // Initialize Smart Wizard
        $('#wizard-update').smartWizard({
	// Properties
    keyNavigation: false, // Enable/Disable key navigation(left and right keys are used if enabled)
    onFinish: function () {
		$('#edit-family-form').submit();
			}
		});
   });  
</script>
<style type="text/css" media="all">
</style>
<form id="edit-family-form" action = "<?php echo plugins_url('templates/processfamily-update.php', dirname(__FILE__)); ?>" method = "POST">
<input type="hidden" name="User" value="<?php echo $currentuser; ?>" />
<input type="hidden" name="personId" value="<?php echo $person['personID']; ?>" />
<div id="wizard-update" class="swMain">
  <ul>
    <li><a href="#step-1">
          <label class="stepNumber">1</label>
          <span class="stepDesc">
			Person<br />
             <small>Update </small>
          </span>
      </a></li>
	  
    <li><a href="#step-2">
          <label class="stepNumber">2</label>
          <span class="stepDesc">
             Parents<br />
             <small>Update </small>
          </span>
      </a></li>
    <li><a href="#step-3">
          <label class="stepNumber">3</label>
          <span class="stepDesc">
            Spouse(s)<br />
             <small>Update </small>
          </span>                   
       </a></li>
    <li><a href="#step-4">
          <label class="stepNumber">4</label>
          <span class="stepDesc">
             Children<br />
             <small>Update</small>
          </span>                   
      </a></li>
  </ul>
  <div id="step-1">   
      <h2 class="StepTitle">Update Details for <?php echo $person_name.$person_surname;?></h2> 
	  <span style="color:#D77600; font-size:10pt"></br><?php echo "Make changes below and then press NEXT. Do not Change or Refresh the page until you have submitted the changes by clicking on FINISH below"; ?>
       <!-- step content -->
	   <table>
	<tbody>
		<tr>
			<td valign="bottom" class="tdback"><?php echo "Name"; ?></td>
			<td class="tdfront"><span style="color:#777777">(Name - 2nd name or Father's Name)<br/></span><input type="text" name="name" value="<?php echo $person_name;?>" size="30"/></td>
			<td valign="bottom" class="tdback"><?php echo $EventDisplay; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" gotra="persongotra" value="<?php echo $person_gotra;?>" /></td></tr>

			<tr>	
			<td class="tdback"></td>
			<td class="tdfront"><span style="color:#777777">(Surname)<br/></span><input type="text" name="surname" value="<?php echo $person_surname;?>" size="30"/></td>
			<td class="tdback"></td><td class="tdfront"></td>
					
		<tr>	
			<td valign="bottom" class="tdback"><?php echo "Born"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/></span><input type="text" name="B.day" value="<?php echo $person_birthdate;?>"</td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			<?php 
			
			?>
			<td valign="bottom" class="tdfront"><input type="text" name="B.Place" value="<?php echo $person_birthplace;?>"</td>
		<tr>	
			<td valign="bottom"class="tdback"><?php echo "Died"; ?></td>
			<td  valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/></span><input type="text" name="D.day" value="<?php echo $person_deathdate;?>"></td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			
			<td valign="bottom" class="tdfront"><input type="text" name="D.Place" value="<?php echo $person_deathplace;?>" /></td></tr>
			
			
		</tr>
	</tbody>
</table>


  </div>
  <div id="step-2">
      <h2 class="StepTitle">Update Details of Parents for <?php echo $person_name.$Person_surname;?></h2> 
       <!-- step content -->
<?php
			$parents = '';
			$parents = $tngcontent->getFamilyById($person['famc']);

			if ($person['famc'] !== '' and $parents['wife'] !== '') {
			$mother = $tngcontent->getPerson($parents['wife']);
			}
			if ($person['famc'] !== ''and $parents['husband'] !== '') {
			$father = $tngcontent->getPerson($parents['husband']);
			}
			
			$parents_marrdate = $parents['marrdate'];
			
			$parents_marrplace == $parents['marrplace'];
			if ($parents_marrdate == "") {
			$parents_marrdate = "Unknown";
			}
			if ($parents_marrplace == '') {
			$parents_marrplace = "Unknown";
			}
			?>
	
			<?php 	
				//Father - get Birth date and place
				$father_firstname = $father['firstname'];
				$father_lastname = $father['lastname'];
				$father_name = $father['firstname']. $father['lastname'];
				
				if ($father_name !== '')
				{	
										
					if ($father['birthdate'] !== '') 
								{
								$father_birthdate = $father['birthdate'];
								} else {
								$father_birthdate = "Unknown";
								}
					if ($father['birthplace'] !== '') 
								{
								$father_birthplace = $father['birthplace'];
								} else {
								$father_birthplace = "Unknown";
								}			
					
				}
				
								
// Father - Gotra
				if ($father_name !== '')
				{
				$fatherRow = $tngcontent->getgotra($father['personID']);
				$father_gotra = $fatherRow['info'];
				} else {
				$father_gotra = "Unknown";
				}
				
//Father - get death date and place				
				if ($father_name !== '')
				{	
					if ($father['living'] == '0') 
						{
								if ($father['deathdate'] !== '') 
								{
								$father_deathdate = " died: ". $father['deathdate'];
								}
								if ($father['deathdate'] == '')  
								{
								$father_deathdate = " died: date unknown";
								}
								if ($father['deathplace'] == '')
								{
								$father_deathplace = "Unknown";
								} else {
								$father_deathplace = $father['deathplace'];
						}
					}
				}
					if ($father['living'] == '1')
							{
							$father_deathdate = "  (Living)";
							$father_deathplace = "(Living)" ;
							}
							
				

				
				//Mother - get Birth date and place
				$mother_firstname = $mother['firstname'];
				$mother_lastname = $mother['lastname'];
				$mothername = $mother['firstname']. $mother['lastname'];
				
				if ($mother_name !== '')
				{	
										
					if ($mother['birthdate'] !== '') 
								{
								$mother_birthdate = $mother['birthdate'];
								} else {
								$mother_birthdate = "Unknown";
								}
					if ($mother['birthplace'] !== '') 
								{
								$mother_birthplace = $mother['birthplace'];
								} else {
								$mother_birthplace = "Unknown";
								}			
					
				}
				
				//Mother - get Gotra
				if ($mother_name !== '')
				{
				$motherRow = $tngcontent->getgotra($mother['personID']);
				$mother_gotra = $motherRow['info'];	
				
				} else {
				$mother_gotra = "Unknown";
				}
				
				//Mother - get death date and place
				
				if ($mother_name !== '')
				{	
					if ($mother['living'] == '0') 
							{
								if ($mother['deathdate'] !== '') 
								{
								$mother_deathdate = " died: ". $mother['deathdate'];
								}
								if ($mother['deathdate'] == '')  
								{
								$mother_deathdate = " died: date unknown";
								}
								if ($mother['deathplace'] == '')
								{
								$mother_deathplace = "Unknown";
								} else {
								$mother_deathplace = $mother['deathplace'];
								}
							}
					if ($mother['living'] == '1')
							{
							$mother_deathdate = "  (Living)";
							$mother_deathplace = "  (Living)";
							}
				}
					
			if ($mother['birthplace'] == '') {
				$mother['birthplace'] = "Unknown";
			}
			
			if ($mother['deathplace'] == '') {
			$mother['deathplace'] = "Unknown";
			}
		
		?>
		
	
				
<table>
		
	<tbody>		
		
		
		<tr>
		
			<td valign="bottom" class="tdback">Father</td>
			<td class="tdfront"><span style="color:#777777">(Name - 2nd name or Father's Name)<br/></span><input type="text" name="fathername" value="<?php echo $father_firstname;?>"></td>
			<td valign="bottom" class="tdback"><?php echo $EventDisplay; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="fatherGotra" value="<?php echo $father_gotra;?>" /></td></tr>
		<tr>	
			<td class="tdback"></td>
			<td class="tdfront"><span style="color:#777777">(Surname)<br/></span><input type="text" name="fathersurname" value="<?php echo $father_lastname;?>" size="30"/></td>
			<td class="tdback"></td><td class="tdfront"></td>
		<tr>	
			<td valign="bottom" class="tdback"><?php echo "Born"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/></span><input type="text" name="fatherB.day" value="<?php echo $father_birthdate;?>" size="10"/></td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			
			<td valign="bottom"class="tdfront"><input type="text" name="fatherB.Place" value="<?php echo $father_birthplace;?>" /></td>
		<tr>	 
			<td valign="bottom" class="tdback"><?php echo "Died"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/></span><input type="text" name="fatherD.day" value="<?php echo $father_deathdate;?>" /></td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="fatherD.Place" value="<?php echo $father_deathplace;?>" /></td</tr>
			
			
		</tr>
		
		<td valign="bottom" class="tdback">Mother</td>
			<td class="tdfront"><span style="color:#777777">(Name - 2nd name or Father's Name)<br/></span><input type="text" name="mothername" value="<?php echo $mother_firstname;?>" size="30"/></td>
			<td valign="bottom" class="tdback"><?php echo $EventDisplay; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="motherGotra" value="<?php echo $mother_gotra;?>" /></td></tr>
		<tr>	
			<td class="tdback"></td>
			<td class="tdfront"><span style="color:#777777">(Surname)<br/></span><input type="text" name="mothersurname" value="<?php echo $mother_lastname;?>" size="30"/></td>
			<td class="tdback"></td><td class="tdfront"></td>
		<tr>	
			<td valign="bottom" class="tdback"><?php echo "Born"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/></span><input type="text" name="motherB.day" value="<?php echo $mother_birthdate;?>" size="10"/></td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			
			<td valign="bottom" class="tdfront"><input type="text" name="motherB.Place" value="<?php echo $mother_birthplace;?>" /></td>
		<tr>	
			<td valign="bottom" class="tdback"><?php echo "Died"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/><s/pan><input type="text" name="motherD.day" value="<?php echo $mother_deathdate;?>" /></td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="motherD.Place" value="<?php echo $mother_deathplace;?>" /></td></tr>
		</tr>
		<tr>
		<td class="tdback"><?php echo "Married" ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/><s/pan><input type="text" name="parentmarr.day" value="<?php echo $parents_marrdate;?>" /></td>
			
			<td class="tdback"><?php echo "Place"; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="parentmarr.Place" value="<?php echo $parents_marrplace;?>" /></td>
		
		</tr>
	</tbody>


</table>


	   
  </div>                      
  <div id="step-3">
      <h2 class="StepTitle">Update Details of Spouse(s) for <?php echo $person_name.$person_surname;?></h2>   
       <!-- step content -->
  		<?php
			// Spouses
			foreach ($families as $family):
				
				$spousemarrdate = $family['marrdate'];
				$spousemarrplace = $family['marrplace'];
				$order = null;
				if ($sortBy && count($families) > 1) {
					$order = $family[$sortBy];
				}
			
				if ($person['personID'] == $family['wife']) {
				
				
				$spouse = $tngcontent->getPerson($family['husband']);
				} else {
					$spouse = $tngcontent->getPerson($family['wife']);
					
				}
				
				if ($spouse['living'] == '0' AND $spouse['deathdate'] !== '') 
					{
					$spousedeathdate = " died: " . $spouse['deathdate'];
					} else {
					$spousedeathdate = " died: date unknown";
					}
					if ($spouse['living'] == '1') {
					$spousedeathdate = "  (Living)";
				}
				if ($marrdatetr == "0000-00-00") {
				$spousemarrdate = "unknown";
				} else {
				$spousemarrdate = $family['marrdate'];
				}
			
				$spouseRow = $tngcontent->getgotra($spouse['personID']);
				$spousegotra = $spouseRow['info'];
				$spouseName = $spouse['firstname'] . $spouse['lastname'];
								
				$children = $tngcontent->getchildren($family['familyID']);
	
		// if wife name is not in database
		if ($family['wife'] == "" and $family['husband'] !== "") {
		$spouse['firstname'] = "";
		$spouse['lastname'] = "";
		$spouse['birthdate'] = "";
		$spouse['birthplace'] = "";
		$spouse['deathdate'] = "";
		$spouse['deathplace'] = "";
		$spouse['marrdate'] = "";
		$spouse['marrplace'] = "";
		$spousegotra = "";
		
		}
		// if HUsband name is not in database
		if ($family['wife']!== "" and $family['husband'] == "") {
		$spouse['firstname'] = "";
		$spouse['lastname'] = "";
		$spouse['birthdate'] = "";
		$spouse['birthplace'] = "";
		$spouse['deathdate'] = "";
		$spouse['deathplace'] = "";
		$spouse['marrdate'] = "";
		$spouse['marrplace'] = "";
		$spousegotra = "";
		}
		
		?>
<table>
		
	<tbody>		
		<tr>
		<td colspan="0"><span style="color:#D77600; font-size:12pt">			
				<?php echo "Spouse ". $order; ?></span></td>
		
		</tr>
		
	</tbody>

				
		<tr>
			<td class="tdback"><?php echo "Spouse ". $order; ?></td>
			<td class="tdfront"><span style="color:#777777">(Spouse Name-2nd name or Father's Name)<br/></span><input type="text" name="spousename" value="<?php echo $spouse['firstname'];?>"></td>
			<td valign="bottom" class="tdback"><?php echo $EventDisplay; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="spouseGotra" value="<?php echo $spousegotra;?>" /></td>
		<tr>	
			<td class="tdback"></td>
			<td class="tdfront"><span style="color:#777777">(Surname)<br/></span><input type="text" name="spousesurname" value="<?php echo $spouse['lastname'];?>" size="30"/></td>
			<td class="tdback"></td><td class="tdfront"></td>
		</tr>
		<tr>		
			<td valign="bottom" class="tdback"><?php echo "Born"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/></span><input type="text" name="spouseB.day" value="<?php echo $spouse['birthdate'];?>"</td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="spouseB.place" value="<?php echo $spouse['birthplace'];?>" size="10"/></td>
			
		</tr>
		<tr>	
			<td valign="bottom" class="tdback"><?php echo "Died"; ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/><s/pan><input type="text" name="spouseD.day" value="<?php echo $spousedeathdate;?>" /></td>
			<td valign="bottom" class="tdback"><?php echo "Place"; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="spouseD.Place" value="<?php echo $spouse['deathplace'];?>" /></td</tr>
			
			
		<tr>
			
		</tr>
		<tr>
		<td class="tdback"><?php echo "Married" ?></td>
			<td valign="bottom" class="tdfront"><span style="color:#777777">(dd mmm yyyy)<br/><s/pan><input type="text" name="spousemarr.day" value="<?php echo $spousemarrdate;?>" /></td>
			
			<td class="tdback"><?php echo "Place"; ?></td>
			<td valign="bottom" class="tdfront"><input type="text" name="spousemarr.place" value="<?php echo $spousemarrplace;?>" /></td>
			
					
		</tr>
		</table>
		<?php
		endforeach; ?> 
  
  
  
  
  </div>
  <div id="step-4">
      <h2 class="StepTitle">Update Details of Children </h2>   
       <!-- step content --> 


<?php
			// Family
			foreach ($families as $family):
				
				$spousemarrdate = $family['marrdate'];
				$spousemarrplace = $family['marrplace'];
				$order = 1;
				if ($sortBy && count($families) > 1) {
					$order = $family[$sortBy];
				}

				if ($person['personID'] == $family['wife']) {
				
				$spouse = $tngcontent->getPerson($family['husband']);
				} else {
					$spouse = $tngcontent->getPerson($family['wife']);
				}
				
				if ($spouse['living'] == '0' AND $spouse['deathdate'] !== '') 
					{
					$deathdate = " died: " . $spouse['deathdate'];
					} else {
					$deathdate = " died: date unknown";
					}
					if ($spouse['living'] == '1') {
					$deathdate = "  (Living)";
				}
			
				$spouseRow = $tngcontent->getgotra($spouse['personID']);
				$spousegotra = $spouseRow['info'];
				$spouseName = $spouse['firstname'] . $spouse['lastname'];
								
				$children = $tngcontent->getchildren($family['familyID']);
			
		// if wife name is not in database
		if ($family['husband'] == "" and $family['wife'] !== "") {
		$spouse['firstname'] = "";
		$spouse['lastname'] = "";
		}
		if ($family['wife'] == "" and $family['husband'] !== "") {
		$spouse['firstname'] = "";
		$spouse['lastname'] = "";
		
		}
		?>
<table>
		
			
		<tr>
		<td><?php echo "Spouse ". $order?></td>
		<td colspan="0"><span style="color:#D77600; font-size:14pt">			
				<?php echo $spouse['firstname'].$spouse['lastname']; ?></span></td>
		
	
		</tr>
		<tr><td colspan="2">
		<button class="js-addChild-edit" data-id="<?php echo $order; ?>">add child</button>
		
<br/>
		<table id="children_<?php echo $order; ?>">	
	<thead>
		<tr>	 
		<td>First Name</td>	
		<td>Last Name</td>
		<td>Sex</td>
		<td>Date Born</br>dd/mmm/yyyy</td>
		<td>Place Born</td>
		<td>Date Died</br>dd/mmm/yyyy</td>
		<td>Place Died</td>
		<td>Living</td>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ($children as $index => $child):
	
		$classes = array('child');
		$childPerson = $tngcontent->getPerson($child['personID']);
		$childFirstName = $childPerson['firstname'];
		$childLastName = $childPerson['lastname'];
		$childName = $childPerson['firstname']. $childPerson['lastname'];
		$childbirthdate = $childPerson['birthdate'];
		$childbirthplace = $childPerson['birthplace'];
		$childdeathdate = $childPerson['deathdate'];
		$childdeathplace = $childPerson['deathplace'];
		$childorder = $child['ordernum'];
		$childliving = $childPerson['living'];
		$childsex = $childPerson['sex'];
		
		if ($child['haskids']) {
			$classes[] = 'haskids';
		}

		// init sex selector
		$Msex = $Fsex = $Usex = "";
		
		if( $childsex == 'M' ) $Msex = "selected=\"selected\"";
		elseif( $childsex['sex'] == 'F' ) $Fsex = "selected=\"selected\"";
		else $Usex = "selected=\"selected\"";
?>
		<tr class="child">
		<input type="hidden" name="child[<?php echo $order; ?>][<?php echo $index; ?>][id]" value="<?php echo $child['personID'] ?>" size="12"/>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][firstname]" value="<?php echo $childFirstName;?>" size="12"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][surname]" value="<?php echo $childLastName;?>" size="12"/></td>	
		<td> <select name="child[<?php echo $order; ?>][<?php echo $index; ?>][sex]" size"3">
		
		<option value="M" <?php echo $Msex; ?>>M</option>
		<option value="F" <?php echo $Fsex; ?>>F</option>
		
		</select>
		</td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][dateborn]" value="<?php echo $childbirthdate;?>" size="10"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][placeborn]" value="<?php echo $childbirthplace;?>" size="10"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][datedied]" value="<?php echo $childdeathdate;?>" size="10"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][placedied]" value="<?php echo $childdeathplace;?>" size="10"/></td>
		<td><input type="checkbox" name="child[<?php echo $order; ?>][<?php echo $index; ?>][living]" value="1" <?php echo ($childPerson['living']?'checked':null);?> /></td>
		</tr>
		
		<?php	
	
		endforeach;
		$index += 1;
		?>
			<tr class="child">
		<input type="hidden" name="child[<?php echo $order; ?>][<?php echo $index; ?>][id]" value="" size="12"/>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][firstname]" value="" size="12"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][surname]" value="" size="12"/></td>	
		<td> <select name="child[<?php echo $order; ?>][<?php echo $index; ?>][sex]" size"3">
		
		<option value="M">M</option>
		<option value="F">F</option>
		
		</select>
		</td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][dateborn]" value="" size="10"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][placeborn]" value="" size="10"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][datedied]" value="" size="10"/></td>
		<td><input type="text" name="child[<?php echo $order; ?>][<?php echo $index; ?>][placedied]" value="" size="10"/></td>
		<td><input type="checkbox" name="child[<?php echo $order; ?>][<?php echo $index; ?>][living]" value="1" checked /></td>
		</tr>
	</table>	

	</td></tr>
<script>
initChildren(<?php echo $order; ?>);
</script>
<?php
	endforeach;
?>
	</tbody>
	</table>   
  </div>
</div>
</form>
<div style="clear:both"></div>
<script>
function initChildren(order) {
	var clone;
	function cloneRow()  { // create clone of empty child line for use during session
		var rows=$('#children_' + order).find('tr.child');
		var idx=rows.length;
		if (idx) {
			clone=rows[idx-1].cloneNode(true);
		}			
	}
	cloneRow();
	$('.js-addChild-edit[data-id="' + order + '"]').click(addRow);
	function addRow(evt) {
		evt.stopPropagation();
		evt.preventDefault();
		var newclone = clone.cloneNode(true);
		var rows=$('#children_' + order).find('tr.child');
		var idx=rows.length;
		if( idx > 0 ) { 
			var field=rows.eq(idx-1).find('input').first();
			var firstname=field[0].value;
			if( !firstname ) {
				alert("Please fill in the new row before adding more");
				return;
			}
		}
		var inputs=newclone.getElementsByTagName('input'), inp, i=0;
		while(inp=inputs[i++]) {
			inp.name=inp.name.replace(/\]\[\d\]/g, '][' + idx + ']');
			inp.value = null;
		}
		var selects=newclone.getElementsByTagName('select'), sel, i=0;
		while(sel=selects[i++]) {
			sel.name=sel.name.replace(/\]\[\d\]/g, '][' + idx + ']');
			sel.selectedItem = 0;
		}
	var tbo=document.getElementById('children_' + order).getElementsByTagName('tbody')[0];
	tbo.appendChild(newclone);
	}
	function deleteLastRow() {
		var tbo=document.getElementById('children_' + order).getElementsByTagName('tbody')[0];
		var rows = tbo.getElementsByTagName('tr');
		tbo.removeChild(rows[rows.length-1] );    
		if(rows.length < 1) {
			addRow();
		}
	}
}
</script>
