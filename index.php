<?php
if(!empty($_POST)) {
	require_once("functions.php");

	$file_type = pathinfo($_FILES["import_file"]["name"], PATHINFO_EXTENSION);
	if($file_type != "csv") {
    	$error_message = "Sorry, only CSV files are allowed.";
	} else {
		$csvFile = file($_FILES["import_file"]["tmp_name"]);
		$rows = explode("\n", $csvFile[0]);
		if(count($rows == 1)) {
			$rows = explode("\r", $csvFile[0]);
		}
	    $data = [];
	    foreach ($rows as $row) {
	        $data[] = str_getcsv($row, ","); //parse the items in rows 
	    }
	    
	    $url = $_POST['hostname'] . "/rest/v10/oauth2/token";
	    $oauth2_token_arguments = array(
		    "grant_type" => "password",
		    "client_id" => "sugar",
		    "client_secret" => "",
		    "username" => $_POST['username'],
		    "password" => $_POST['password'],
		    "platform" => "import_tool"
		);
		$oauth2_token_response = call($url, '', 'POST', $oauth2_token_arguments);
		
		if(empty($oauth2_token_response->access_token)) {
			$error_message = "Invalid URL or Credentials.";
		} else {
			$url = $_POST['hostname'] . "/rest/v10/metadata";
			$metadata_response = call($url, $oauth2_token_response->access_token, 'GET');
			if(empty($metadata_response->modules->$_POST['module1'])) {
				$error_message = "Module 1 invalid.";
			} else {
				$link_name = "";
				foreach($metadata_response->modules->$_POST['module1']->fields as $field) {
					if($field->relationship == $_POST['relationship_name']) {
						$link_name = $field->name;
						break;
					}
				}
				if(empty($link_name)) {
					$error_message = "Relationship Name Invalid.";
				} else {
					foreach($data as $k=>$d) {
						$url = $_POST['hostname'] . "/rest/v10/" . $_POST['module1'] . '/' . $d[0] . '/link/' . $link_name . '/' . $d[1];
						$link_response = call($url, $oauth2_token_response->access_token, 'POST');
						if(empty($link_response->record)) {
							$error_message .= "Error creating relationship on row " . $k . ".<br />";
						}
					}
					if(count(data) > 0 && empty($error_message)) {
						$success_message = "Relationships successfully imported.";
					}
				}
			}
		}
	}
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>SugarCRM Many-to-Many Import Tool</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="css/main.css" />
		<link rel="stylesheet" href="css/jquery-ui.min.css" />
		<link rel="stylesheet" href="css/jquery-ui.theme.min.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
	</head>
	<body>
		<div id="page-wrapper">
			<div id="header-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">
							<header id="header">
								<h1><a href="/" id="logo">SugarCRM Many-to-Many Import Tool</a></h1>
								<nav id="nav">
									<!-- <a> tag on each line, class="current-page-item" for active -->
								</nav>
							</header>
						</div>
					</div>
				</div>
			</div>

			<!-- Content -->
			<div id="main">
		        <div class="container">
		            <div class="row main-row">
			            <div class="12u">
			              <section>
			                <h2>Settings</h2>

			                <form action="#" method="post" id="settingsForm" enctype="multipart/form-data">
			                <p>
			                	<span id="error_message"><?php if(!empty($error_message)) { echo $error_message . '<br />'; } ?></span>
			                	<span id="success_message"><?php if(!empty($success_message)) { echo $success_message . '<br />'; } ?></span>
			                	<span>
			                		Import file should contain two columns (module1 id, module2 id) and no header row.<br />
			                		"Module 1" should be the plural name of the module used in column 1 of the CSV file.<br />
			                		The Relationship Name can be found in Studio &gt; [Module] &gt; Relationships under the "Name" column.
			                	</span><br />
			                	<label for="hostname">Instance URL: </label>&nbsp;&nbsp;<input type="text" name="hostname" id="hostname" /><br />
			                	<label for="username">Username: </label>&nbsp;&nbsp;<input type="text" name="username" id="username" /><br />
			                	<label for="password">Password: </label>&nbsp;&nbsp;<input type="password" name="password" id="password" /><br />
			                	<label for="import_file">Import File: </label>&nbsp;&nbsp;<input type="file" name="import_file" id="import_file" /><br />
			                	<label for="module1">Module 1: </label>&nbsp;&nbsp;<input type="text" name="module1" id="module1" /><br />
			                	<!-- <label for="module2">Module 2: </label>&nbsp;&nbsp;<input type="text" name="module2" id="module2" /><br /> -->
			                	<label for="relationship_name">Relationship Name: </label>&nbsp;&nbsp;<input type="text" name="relationship_name" id="relationship_name" /><br />
			                </p>

			                <p><a href="#" class="button" onclick="$('#settingsForm').submit(); return false;">Import</a></p>
			                </form>
			              </section>
			            </div>
		            </div>
		        </div>
		    </div>
		</div>

		<!-- Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery-ui.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-viewport.min.js"></script>
		<script src="js/util.js"></script>
		<!--[if lte IE 8]><script src="js/ie/respond.min.js"></script><![endif]-->
		<script src="js/main.js"></script>

		<script>
		$(function() { 

		});
		</script>
	</body>
</html>