<?php

//Line for the live server. Comment out if testing on local setup.

 if (($_SERVER['REQUEST_METHOD'] == 'POST')):
	if (isset($_POST['myFirstName'])) { $fname = $_POST['myFirstName']; } else { $fname = ''; }
	if (isset($_POST['myLastName'])) { $lname = $_POST['myLastName']; } else { $lname = ''; }
	if (isset($_POST['myWarriorID'])) { $myWarriorID = $_POST['myWarriorID']; } else { $myWarriorID =''; }
	if (isset($_POST['myEmail'])) { $myEmail = $_POST['myEmail']; } else { $myEmail = ''; }
	
	//Empty field checks
	$formerrors = false;
	if ($myWarriorID === ''):
		$err_msg = 'You must input a Warrior ID';
		echo $err_msg;
		exit(); 
		//requests should terminate immediately if someone enters something wrong
	endif;
	if ($myEmail === ''):
		$err_msg = 'Please input an email';
		echo $err_msg;
		exit();
	endif;
	if ($fname === '' || $lname === '') :
		$err_msg = 'Sorry, you must input your full name';
		echo $err_msg;
		exit();
	endif;
	
	
	//Filters
	if(!filter_var($myEmail, FILTER_VALIDATE_EMAIL)):
        	$err_msg = 'Please input a valid email address.';
		echo $err_msg;
		exit();
    	endif;
    	if ( !(preg_match('/^00[\d]{7}$/', $myWarriorID)) ) :
    		$err_msg = 'Sorry, you must input a valid Warrior ID.';
    		echo $myWarriorID;
		echo $err_msg;
		exit();
	endif; // WarriorID doesn't match  

    
	if ( !(preg_match('/[A-Za-z]+/', $fname)) ) :
		$err_msg = 'Error: Invalid input in First Name field.';
		echo $err_msg;
		exit();
	endif; // pattern doesn't match

	if ( !(preg_match('/[A-Za-z]+/', $lname)) ) :
		$err_msg = 'Error: Invalid input in Last Name field.';
		echo $err_msg;
		exit();
	endif; // pattern doesn't match
  
  //Only get here if all fields are correct
  $formdata = array (
    'fname' => $fname,
    'lname' => $lname,
    'myEmail' => $myEmail,
    'myWarriorID' => $myWarriorID,
  );

if(!formerrors):
	include 'info.php';
	$host = 'localhost';
	$dbname = 'CSStanhack';
	$forminfolink = mysqli_connect($host, $user, $password, $dbname);
	$forminfoquery = "INSERT INTO Participants (
	  StudentID,
	  Email,
	  FirstName,
	  LastName
	) 
	VALUES (
	  '".$myWarriorID."',
	  '".$myEmail."'
	  '".$fname."',
	  '".$lname."',
	)";
	if ($forminforesult = mysqli_query($dbname, $forminfoquery)):
	  $form_msg = 'Thank you, ".$fname.," for registering for the hackathon!';
	else:
	  $form_msg = "There was an error submission. Perhaps you already registered?";
	endif; //write to database
	mysqli_close($forminfolink);
	echo $form_msg;
endif;
endif;
?>

