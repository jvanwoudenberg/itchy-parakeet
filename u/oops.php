<?php
include "../functions.php";

include("checklogin.php");

?>

<!doctype html>
<html class="no-js" lang="">
    <?php include("head.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="page-container">
        <?php include("header.php"); ?>
            <div class="container">
                <div class="row row-offcanvas row-offcanvas-left">
                    <?php include("navigation.php");?>
                    <div class="col-xs-13 col-sm-10"> 
                        <div class="jumbotron">
                            <h2>Oops!</h2>
                            <p>Hier gaat iets fout. Het lijkt erop alsof je een pagina probeert te bezoeken die niet voor jou bedoelt is. Weet je zeker dat je niets fout doet en het aan ons ligt? Stuur dan even een mailtje naar: <?php echo $mailtolink?>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("default-js.html"); ?>
        </body>
</html>