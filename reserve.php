<?php
include_once "functions.php";

if( strtotime('now') > strtotime('2016-06-29 00:00') ) {
    header('Location: index');
}

$email = "";
$returnVal = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !empty($_POST["email"]) ) {
        $email = test_input($_POST["email"]);
    } else {
        $email = "";
        addError("Je moet wel je email adres opgeven.");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT 1 from $current_table where email = '%s' and complete = 1", $mysqli->real_escape_string($email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult->num_rows > 0 ) {
            addError("Het lijkt erop dat je al een ticket hebt. Stuur voor meer informatie een email naar".$mailtolink);
        }
        if( $returnVal == "" ) {
            $sqlresult = $mysqli->query(sprintf("SELECT 1 from $current_table where email = '%s'",$mysqli->real_escape_string($email)));
            if( $sqlresult->num_rows < 1 ) {
                addError("We hebben geen aanmelding van jou ontvangen dus kunnen we je helaas niet inschrijven voor de reservelijst");
            } else {
                $query = sprintf("UPDATE $current_table SET round = 2 WHERE email = '%s'",$mysqli->real_escape_string($email));
                if( !$mysqli->query($query) ) {
                    addError("Er is iets fout gegaan met het opslaan van je verzoek. Stuur voor meer infomatie een email naar:".$mailtolink);
                }
            }
        }
        $mysqli->close();
    }
    if( $returnVal == "" ) {
        $returnVal = '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ' . 
            "We hebben je aanmelding voor de reservelijst in goede orde ontvangen. Als er een ticket voor je vrijkomt ontvang je meer informatie daarover via email." . '</div>';
            $email = "";
    }
}

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familiar Forest Reserve aanmelden</title>
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
        <div class="container">
            <div class="default-text">
                <h1>Familiar Voorjaar 2017</h1>
                <p class="lead">
                    Lieve Lenteliefhebbers,
                </p>
                <p>De ervaring leert dat het nog wel eens voorkomt dat een deelnemer onverhoopt toch niet mee kan naar Familiar Forest. Wanneer iemand ervoor kiest zijn ticket terug te
                    willen verkopen zullen we proberen deze opnieuw te verkopen aan iemand die daar interesse in heeft en deze alsnog de mogelijkheid geven om deel te nemen aan Familiar Forest 2016</p>
                <p>Je kunt tot en met 27 juni aangeven of je hier interesse in hebt. Deelnemers met een ticket hebben vanaf die datum tot en met 5 augustus de mogelijkheid
                    hun ticket terug te verkopen aan Familiar Forest.<p>
                <p>Om je aan te melden voor deze extra ronde hoef je alleen maar je email adres in te vullen, je moet je dus al wel ingeschreven hebben
                    tijdens een voorgaande inschrijfronde.

                <p>De high fives zijn gratis, de knuffels oprecht en de liefde oneindig.<br>Familiar Forest</p>
            </div>
            <?=$returnVal?>
            <form class='form-small' method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                <div class="form-group row">
                    <label for="email" class="col-sm-2 form-control-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" id="email" placeholder="Email" value="<?php echo $email;?>" name="email">
                    </div>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Aanmelden <span class="glyphicon glyphicon-send" aria-hidden="true"></span></button>
            </form>

        </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script>
    $('#togglebutton').on('click', function(){
        $(this).children().closest('.glyphicon').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
    });
    </script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  </body>
</html>