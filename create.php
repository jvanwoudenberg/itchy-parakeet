<?php session_start();
include_once "functions.php";
date_default_timezone_set('Europe/Amsterdam');
$returnVal = "";
$firstname = $lastname = $birthdate = $gender = $email = $phone = $city = $editions_str = $nr_editions = ""; 
$editions = array();
$is_update = false;

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["firstname"]) ) {
        $firstname = test_input($_POST["firstname"]);
    } else {
        $firstname = "";
        addError("Je hebt je voornaam niet opgegeven.");
    }
    if( !empty($_POST["lastname"]) ) {
        $lastname = test_input($_POST["lastname"]);
    } else {
        $lastname = "";
        addError("Je hebt je achternaam niet opgegeven.");
    }
    if( !empty($_POST["city"]) ) {
        $city = test_input($_POST["city"]);
    } else {
        $city = "";
        addError("Je hebt je woonplaats niet opgegeven.");
    }
    if( !empty($_POST["birthdate"]) ) {
        $birthdate = test_input($_POST["birthdate"]);
        $date = DateTime::createFromFormat('d/m/Y', $birthdate);
        if( $date == FALSE ) {
            if( ($timestamp = strtotime($birthdate)) == FALSE ) {
                addError("De opgegeven geboortedatum klopt niet.");
            } else {
                $birthdate = date( 'Y-m-d H:i:s', $timestamp );
            }
        } else {
            $birthdate = $date->format('Y-m-d H:i:s');
        }
    } else {
        addError("Je hebt je geboortedatum niet opgegeven");
    }
    if( !empty($_POST["gender"]) ) {
        $gender = test_input($_POST["gender"]);
    } else {
        $gender = "";
        addError("Je hebt je geslacht niet opgegeven.");
    }
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
        if( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            addError("Het email adres wat je hebt opgegeven lijkt niet te kloppen.");
        }
    } else {
        $email = "";
        addError("Je hebt geen email adres opgegeven");
    }
    if( !empty($_POST["phone"]) ) {
        $phone = test_input($_POST["phone"]);
    } else {
        $phone = "";
        addError("Je hebt geen telefoonnummer opgegeven");
    }
    $nr_editions = 0;
    $editions = isset($_POST['editions']) ? $_POST['editions'] : array();
    foreach($editions as $edition) {
        $editions_str .= test_input($edition) . ",";
        $nr_editions += 1;
    }

    if( !empty($_POST["password"]) ) {
        $password = test_input($_POST["password"]);
    } else {
        $password = "";
        addError("Je hebt je wachtwoord niet opgegeven.");
    }
    if( !empty($_POST["repeat"]) ) {
        $repeat = test_input($_POST["repeat"]);
    } else {
        $repeat = "";
        addError("Je hebt je herhaling niet opgegeven.");
    }

    if( $repeat != $password ) {
        addError( "De opgegeven wachtwoorden komen niet overeen");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if( $is_update ) {
            //TODO update person
        } else {
            //TODO insert person
        }
        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        $user_add_query = sprintf(
            "INSERT INTO `users` (`username`, `password`, `permissions`) VALUES ('%s', '%s','%s','%s')",
            $mysqli->real_escape_string($username),
            $mysqli->real_escape_string($pw_hash),
            $mysqli->real_escape_string(PERMISSION_PARTICIPANT)
        );
        if( $db_error != "" ) {
            addError($db_error);
        }
        $mysqli->close();
    } else {
        //try again..
    }
    if( $returnVal == "") {
        $_SESSION['success_email'] = $email;
        header('Location: success');
    } else {
    }
} //End POST
function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}
?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Inschrijven</title>
        <meta name="description" content="">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <link rel="icon" href="favicon.ico">
        <!-- Place favicon.ico in the root directory -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" type="text/css" media="all"
            href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"/>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div id="header" class="text-center">
            <div class="container">
                <div class="row">
                    <div class="col-xs-3">

                    </div>
                    <div class="col-xs-6">
                        <h1 class="">Stichting Familiar Forest</h1>
                    </div>
                    <div class="col-xs-3">
                        <a class='login-button' href="login">Inloggen</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="form-intro-text">
                <h2>Aanmaken Familiar Forest account</h2>
                <a class='btn btn-lg btn-primary' href='activate' role='button'><i class="glyphicon glyphicon-check"></i> Ik heb mezelf ingeschreven voor Familiar Forest 2016</a>
            </div>
                
            <?php echo $returnVal; ?>
            <form id="create-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 form-control-label">Wachtwoord</label>
                    <div class="col-sm-10">
                        <input type="password" id="password" class="form-control" placeholder="Paswoord"         name="password">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="passwordrepeat" class="col-sm-2 form-control-label">Wachtwoord Herhalen</label>
                    <div class="col-sm-10">
                        <input type="password" id="repeat" class="form-control" placeholder="Paswoord Herhalen" name="repeat">
                    </div>
                </div>

                <fieldset>
                <legend>Jouw gegevens</legend>
                <div class="form-group row">
                    <label for="firstname" class="col-sm-2 form-control-label">Voornaam*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="firstname" placeholder="Voornaam" value="<?php echo $firstname;?>" name="firstname">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="lastname" class="col-sm-2 form-control-label">Achternaam*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="lastname" placeholder="Achternaam" value="<?php echo $lastname;?>" name="lastname">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="city" class="col-sm-2 form-control-label">Woonplaats*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="city" placeholder="Woonplaats" value="<?php echo $city;?>" name="city">
                    </div>
                </div>
            
                <div class="form-group row">
                    <label for="birthdate" class="col-sm-2 form-control-label">Geboortedatum*<br>(dd/mm/yyyy)</label>
                    <div class="col-sm-10">
                        <input class="form-control ignore datepicker" type="text" id="birthdate" value="<?php echo $birthdate;?>" name="birthdate" placeholder="dd/mm/yyyy">
                        <div>
                            <label for="birthdate" class="error" style="display:none;"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2">Geslacht*</label>
                    <div class="col-sm-10">
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="male" value="male" <?php if($gender == "male") echo( "checked"); ?>>
                                Jongeman
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="gender" id="female" value="female" <?php if($gender == "female") echo( "checked"); ?> >
                                Jongedame
                            </label>
                        </div>
                        <label for="gender" class="error" style="display:none;"></label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-sm-2 form-control-label">Telefoonnummer*</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="phone" placeholder="Telefoonnummer" value="<?php echo $phone;?>" name="phone">
                    </div>
                </div>
                </fieldset>
                <fieldset>
                    <legend>Bij welke edities was je aanwezig?</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Voorgaande edities</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2010" value="fff2010" <?php if(in_array("fff2010", $editions)) echo( "checked"); ?> >
                                    Familiar Forest 2010
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2011" value="fff2011" <?php if(in_array("fff2011", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2011
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="ffcastle" value="ffcastle" <?php if(in_array("ffcastle", $editions)) echo( "checked"); ?>>
                                    Familiar Castle                                
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2012" value="fwf2012" <?php if(in_array("fwf2012", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2012
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fh2012" value="fh2012" <?php if(in_array("fh2012", $editions)) echo( "checked"); ?>>
                                    Familiar Hemelvaartsnacht 2012
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2012" value="fff2012" <?php if(in_array("fff2012", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2012
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fh2013" value="fh2013" <?php if(in_array("fh2013", $editions)) echo( "checked"); ?>>
                                    Familiar Hemelvaartsnacht 2013
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2013" value="fwf2013" <?php if(in_array("fwf2013", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2013
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2013" value="fff2013" <?php if(in_array("fff2013", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2013
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2014" value="fwf2014" <?php if(in_array("fwf2014", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2014
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2014" value="fff2014" <?php if(in_array("fff2014", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2014
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fwf2015" value="fwf2015" <?php if(in_array("fwf2015", $editions)) echo( "checked"); ?>>
                                    Familiar Winter 2015
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2015" value="fff2015" <?php if(in_array("fff2015", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2015
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="editions[]" id="fff2016" value="fff2016" <?php if(in_array("fff2016", $editions)) echo( "checked"); ?>>
                                    Familiar Forest 2016
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Versturen</button>
            </form>
        </div>
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
        <scirpt src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/localization/messages_nl.js"></script>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/vendor/bootstrap-datepicker.js"></script>
        <script src="js/vendor/bootstrap-datepicker.nl.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/create.js"></script>
    </body>
</html>