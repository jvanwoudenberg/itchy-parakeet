<?php
include "../functions.php";

include("checklogin.php");

if( ($user_permissions & PERMISSION_RAFFLE) != PERMISSION_RAFFLE ) {
    header('Location: oops.php');
}

$required_permissions = PERMISSION_RAFFLE;
$request_for = 'showraffle';

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
                <div class="col-xs-12 col-sm-9"> 
                <div style='margin-top: 5px;'>
                <?php include("generic_filter.php"); ?>
                <?php include("stats.php"); ?>
                <?php include("pagination.php"); ?>
                    <table class='table table-striped table-bordered table-hover table-condensed'>
                        <thead>
                            <tr class='header-row'><th>Inloten</th><th>Achternaam</th><th>Voornaam</th><th>Leeftijd</th><th>Woonplaats</th><th>Aantal ediites</th><th>Bekend door</th><th>Motivatie</th><th>Vraag</th><th>Eerste keus</th><th></th><th>Tweede keus</th><th></th><th>Voorgaande edities</th><th>Email</th>
                        </thead>
                        <tbody>
                    <?php
                    while($row = mysqli_fetch_array($sqlresult,MYSQLI_ASSOC))
                    {
                        echo "<tr>";
                        echo "<td><a class='btn btn-warning btn-sm btn-block uitloten'>Uitloten</a></td>"; //inloten knop
                        echo "<td>" . $row['lastname'] . "</td>";
                        echo "<td>" . $row['firstname'] . "</td>";
                        $age = (new DateTime($row['birthdate']))->diff(new DateTime('now'))->y;
                        echo "<td>" . $age . "</td>"; //leeftidj berekenen
                        echo "<td>" . $row['city'] . "</td>";
                        echo "<td>" . $row['visits'] . "</td>";
                        echo "<td>" . $row['familiar'] . "</td>";
                        echo "<td>" . $row['motivation'] . "</td>";
                        echo "<td>" . $row['question'] . "</td>";
                        echo "<td>" . translate_contrib($row['contrib0_type']) . "</td>";
                        echo "<td>" . $row['contrib0_desc'] . "</td>";
                        echo "<td>" . translate_contrib($row['contrib1_type']) . "</td>";
                        echo "<td>" . $row['contrib1_desc'] . "</td>";
                        $editions_arr = explode(",",$row['editions']);
                        $editions_str = "";
                        foreach( $editions_arr as $edition ) {
                            $editions_str .= translate_edition($edition)."<br>";
                        }
                        echo "<td>" . $editions_str . "</td>";
                        echo "<td class='email'>" . $row['email'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                        </tbody>
                    </table>
                </div>
                <?php include("pagination.php"); ?>
            </div>
            </div>
                </div>
            </div>
        </div>
        <?php include("default-js.html"); ?>
        <script src="js/raffle.js"></script>
        <script src="js/generic_filter.js"></script>
    </body>
</html>
