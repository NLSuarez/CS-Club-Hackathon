<?php
if (($_SERVER['REQUEST_METHOD'] == 'POST') && (!empty($_POST['action']))):

if (isset($_POST['myname'])) { $myname = $_POST['myname']; } else { $myname = ''; }
if (isset($_POST['mypassword'])) { $mypassword = $_POST['mypassword']; } else { $mypassword = ''; }
if (isset($_POST['mypasswordconf'])) { $mypasswordconf = $_POST['mypasswordconf']; } else { $mypasswordconf = ''; }
if (isset($_POST['mycomments'])) {
    $mycomments = filter_var($_POST['mycomments'], FILTER_SANITIZE_STRING ); }
    else { $mycomments = ''; }
if (isset($_POST['reference'])) { $reference = $_POST['reference']; } else { $reference = ''; }
if (isset($_POST['requesttype'])) { $requesttype = $_POST['requesttype']; } else { $requesttype = ''; }
if (isset($_POST['ajaxrequest'])) { $ajaxrequest = $_POST['ajaxrequest']; } else { $ajaxrequest = ''; }

	$formerrors = false;

	if ($myname === '') :
		$err_myname = '<div class="error">Sorry, your name is a required field</div>';
		$formerrors = true;
	endif; // input field empty

	if (strlen($mypassword) <= 6):
		$err_passlength = '<div class="error">Sorry, the password must be at least six characters</div>';
		if ( $ajaxrequest ) { echo "<script>$('#mypassword').after('<div class=\"error\">Sorry, the password must be at least six characters</div>');</script>"; }
		$formerrors = true;
	endif; //password not long enough


	if ($mypassword !== $mypasswordconf) :
		$err_mypassconf = '<div class="error">Sorry, passwords must match</div>';
		if ( $ajaxrequest ) { echo "<script>$('#mypasswordconf').after('<div class=\"error\">Sorry, passwords must match</div>');</script>"; }
		$formerrors = true;
	endif; //passwords don't match


	if ( !(preg_match('/[A-Za-z]+, [A-Za-z]+/', $myname)) ) :
		$err_patternmatch = '<div class="error">Sorry, the name must be in the format: Last, First</div>';
		$formerrors = true;
	endif; // pattern doesn't match


  $formdata = array (
    'myname' => $myname,
    'mypassword' => $mypassword,
    'mypasswordconf' => $mypasswordconf,
    'mycomments' => $mycomments,
    'reference' => $reference,
    'requesttype' => $requesttype
  );

  date_default_timezone_set('US/Eastern');
  $currtime = time();
  $datefordb = date('Y-m-d H:i:s', $currtime);
  $salty = dechex($currtime).$mypassword;
  $salted = hash('sha1', $salty);


	if (!($formerrors)) :
		include("log_formdb.php");

		$forminfolink = mysqli_connect($host, $user, $password, $dbname);
		$forminfoquery = "INSERT INTO form_info (
		  forminfo_id,
		  forminfo_ts,
		  myname,
		  mypassword,
		  mycomments,
		  reference,
		  requesttype
		) 
		VALUES (
		  '',
		  '".$datefordb."',
		  '".$myname."',
		  '".$salted."',
		  '".$mycomments."',
		  '".$reference."',
		  '".$requesttype."'
		)";

		if ($forminforesult = mysqli_query($forminfolink, $forminfoquery)):
		  $msg = "Your form data has been processed. Thanks.";
		  if ( $ajaxrequest ):
		    echo "<script>$('#myform').before('<div id=\"formmessage\"><p>",$msg,"</p></div>'); $('#myform').hide();</script>";
		  endif; // ajaxrequest
		else:
		  $msg = "Problem with database";
		  if ( $ajaxrequest ):
		    echo "<script>$('#myform').before('<div id=\"formmessage\"><p>",$msg,"</p></div>'); $('#myform').hide();</script>";
		  endif; // ajaxrequest
		endif; //write to database

		mysqli_close($forminfolink);

	endif; // check for form errors

endif;
 
?>
