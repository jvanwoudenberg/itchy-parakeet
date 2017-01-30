<?php
include "../functions.php";

include("checklogin.php");

if( $user_permissions & PERMISSION_PARTICIPANT != PERMISSION_PARTICIPANT ) {
    header('Location: oops.php');
}

date_default_timezone_set('Europe/Amsterdam');

if( strtotime('now') > strtotime('2017-02-16 10:00') ) {
    header('Location: voorjaar');
}

$signupround = 0;

$returnVal = "";
$is_save = FALSE;
$contrib0 = $contrib1 = $contrib0desc = $contrib1desc = $act0type = $act0desc = $act0need = $act1type = $act1desc = $act1need = $partner = $motivation = $familiar = $preparations = $terms0 = $terms1 = $terms2 = $terms3 = "";
$preparationsbox = false;
$editions = array();

if( $_SERVER["REQUEST_METHOD"] == "POST") {
    $returnVal = "";
    if( !empty($_POST["contrib0"])) {
        $contrib0 = test_input($_POST["contrib0"]);
    } else {
        //impossible
    }
    if( !empty($_POST["contrib0desc"])) {
        $contrib0desc = test_input($_POST["contrib0desc"]);
    } else {
        $contrib0desc = "";
    }

    if( !empty($_POST["contrib1"])) {
        $contrib1 = test_input($_POST["contrib1"]);
    } else {
        //impossible
    }
    if( !empty($_POST["contrib1desc"])) {
        $contrib1desc = test_input($_POST["contrib1desc"]);
    } else {
        $contrib1desc = "";
    }

    if( !empty($_POST["act0type"])) {
        $act0type = test_input($_POST["act0type"]);
    } else {
        $act0type = "";
    }

    if( !empty($_POST["act0desc"])) {
        $act0desc = test_input($_POST["act0desc"]);
    } else {
        $act0desc = "";
    }

    if( !empty($_POST["act0need"])) {
        $act0need = test_input($_POST["act0need"]);
    } else {
        $act0need = "";
    }

    if( !empty($_POST["act1type"])) {
        $act1type = test_input($_POST["act1type"]);
    } else {
        $act1type = "";
    }
    
    if( !empty($_POST["act1desc"])) {
        $act1desc = test_input($_POST["act1desc"]);
    } else {
        $act1desc = "";
    }

    if( !empty($_POST["act1need"])) {
        $act1need = test_input($_POST["act1need"]);
    } else {
        $act1need = "";
    }

    if( !empty($_POST["motivation"])) {
        $motivation = test_input($_POST["motivation"]);
    } else {
        $motivation = "";
    }

    if( !empty($_POST["familiar"])) {
        $familiar = test_input($_POST["familiar"]);
    } else {
        $familiar = "";
    }

    $db_contrib0 = $db_contrib0_desc = $db_contrib0_need = "";
    $db_contrib1 = $db_contrib1_desc = $db_contrib1_need = "";

    if( $contrib0 == "act" ) {
        $db_contrib0 = $act0type;
        $db_contrib0_desc = $act0desc;
        $db_contrib0_need = $act0need;
    } else {
        $db_contrib0 = $contrib0;
        $db_contrib0_desc = $contrib0desc;
    }

    if( $contrib1 == "act" ) {
        $db_contrib1 = $act1type;
        $db_contrib1_desc = $act1desc;
        $db_contrib1_need = $act1need;
    } else {
        $db_contrib1 = $contrib1;
        $db_contrib1_desc = $contrib1desc;
    }

    if( !empty($_POST["partner"])) {
        $partner = test_input($_POST["partner"]);
        if( !filter_var($partner, FILTER_VALIDATE_EMAIL)) {
            addError("Het email adres van je lieveling is niet geldig");
        }
    } else {
        $partner = "";
    }

    if( !empty($_POST["preparationsbox"])) {
        $preparationsbox = true;
        if( !empty($_POST["preparations"])) {
            $preparations = test_input($_POST["preparations"]);
        } else {
            $preparations = "J";
        }
    } else {
        $preparationsbox = false;
        $preparations = "N";
    }

    if( !empty($_POST["terms0"])) {
        $terms0 = test_input($_POST["terms0"]);
    } else {
        $terms0 = "";
    }
    if( !empty($_POST["terms1"])) {
        $terms1 = test_input($_POST["terms1"]);
    } else {
        $terms1 = "";
    }
    if( !empty($_POST["terms2"])) {
        $terms2 = test_input($_POST["terms2"]);
    } else {
        $terms2 = "";
    }

    if( !empty($_POST["terms3"])) {
        $terms3 = test_input($_POST["terms3"]);
    } else {
        $terms3 = "";
    }

    if( $terms0 == "" || $terms1 == "" || $terms2 == "" || $terms3 == "") {
        addError("Je moet alle voorwaarden accepteren");
    }

    if( $returnVal == "" ) {
        $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
        $query = sprintf("SELECT 1 FROM $current_table WHERE email = '%s' and complete = 1",
            $mysqli->real_escape_string($user_email));
        $sqlresult = $mysqli->query($query);
        if( $sqlresult === FALSE ) {
            addError("Helaas konden we je gegevens niet opslaan, probeer het later nog eens of mail naar: ".$mailtolink);
            email_error("Error looking for user in buyer: ".$mysqli->error);
        } else if( $sqlresult->num_rows != 0 ) {
            addError("Zo te zien heb je al een ticket en daarom kun je op dit moment niet jezelf inschrijven. Voor meer informatie kun je mailen naar: ".$mailtolink);
        } else {
            $query = sprintf("SELECT * FROM $current_table WHERE email = '%s'",
                $mysqli->real_escape_string($user_email));
            $sqlresult = $mysqli->query($query);
            $query = "";
            $signupdate = date( 'Y-m-d H:i:s');
            if( $sqlresult->num_rows == 0 ) {
                $query = sprintf("INSERT INTO `$current_table` (`email`, `partner`, `motivation`, `familiar`, `contrib0_type`, `contrib0_desc`, `contrib0_need`, `contrib1_type`, `contrib1_desc`, `contrib1_need`, `preparations`, `round`, `signupdate`, `terms0`, `terms1`, `terms2`, `terms3`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%s,'%s','%s','%s','%s','%s');",
                    $mysqli->real_escape_string($user_email),
                    $mysqli->real_escape_string($partner),
                    $mysqli->real_escape_string($motivation),
                    $mysqli->real_escape_string($familiar),
                    $mysqli->real_escape_string($db_contrib0),
                    $mysqli->real_escape_string($db_contrib0_desc),
                    $mysqli->real_escape_string($db_contrib0_need),
                    $mysqli->real_escape_string($db_contrib1),
                    $mysqli->real_escape_string($db_contrib1_desc),
                    $mysqli->real_escape_string($db_contrib1_need),
                    $mysqli->real_escape_string($preparations),
                    $mysqli->real_escape_string($signupround),
                    $mysqli->real_escape_string($signupdate),
                    $mysqli->real_escape_string($terms0),
                    $mysqli->real_escape_string($terms1),
                    $mysqli->real_escape_string($terms2),
                    $mysqli->real_escape_string($terms3));
            } else {
                $query = sprintf("UPDATE `$current_table` SET `partner` = '%s', `motivation` = '%s', `familiar` = '%s', `contrib0_type` = '%s', `contrib0_desc` = '%s', `contrib0_need` = '%s', `contrib1_type` = '%s', `contrib1_desc` = '%s', `contrib1_need` = '%s', `preparations` = '%s', `round` = %s, `signupdate` = '%s', `terms0` = '%s', `terms1` = '%s', `terms2` = '%s', `terms3` = '%s' WHERE `email` = '%s'",
                    $mysqli->real_escape_string($partner),
                    $mysqli->real_escape_string($motivation),
                    $mysqli->real_escape_string($familiar),
                    $mysqli->real_escape_string($db_contrib0),
                    $mysqli->real_escape_string($db_contrib0_desc),
                    $mysqli->real_escape_string($db_contrib0_need),
                    $mysqli->real_escape_string($db_contrib1),
                    $mysqli->real_escape_string($db_contrib1_desc),
                    $mysqli->real_escape_string($db_contrib1_need),
                    $mysqli->real_escape_string($preparations),
                    $mysqli->real_escape_string($signupround),
                    $mysqli->real_escape_string($signupdate),
                    $mysqli->real_escape_string($terms0),
                    $mysqli->real_escape_string($terms1),
                    $mysqli->real_escape_string($terms2),
                    $mysqli->real_escape_string($terms3),
                    $mysqli->real_escape_string($user_email));
            }
            $result = $mysqli->query($query);
            if( !$result ) {
                addError("We hebben niet je gegevens kunnen opslaan. Probeer het later nog eens of mail naar: ".$mailtolink);
                email_error("Error bij inschrijven: ".$mysqli->error."<br>".$query);
            } else {
                $returnVal = '<div class="alert alert-success" role="alert"><i class="glyphicon glyphicon-ok"></i></span> We hebben je inschrijving in goede orde ontvangen.</div>';
            }
            if( $db_error != "" ) {
                addError($db_error);
            }
        }
        $mysqli->close();
    } else {
        //try again..
    }
    if( $returnVal == "") {
        header('Location: success');
    } else {
    }
} else { //End POST
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $query = sprintf("SELECT * FROM `$current_table` WHERE `email` = '%s'",
        $mysqli->real_escape_string($user_email)
        );
    $result = $mysqli->query($query);
    if( $result && $result->num_rows == 1 ) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $act_types = array('workshop', 'game', 'lecture', 'schmink', 'other','perform','install');

        if( in_array($row['contrib0_type'], $act_types) ) {
            $contrib0 = 'act';
            $act0type = htmlspecialchars_decode($row['contrib0_type']);
            $act0desc = htmlspecialchars_decode($row['contrib0_desc']);
            $act0need = htmlspecialchars_decode($row['contrib0_need']);
        } else {
            $contrib0 = htmlspecialchars_decode($row['contrib0_type']);
            $contrib0desc = htmlspecialchars_decode($row['contrib0_desc']);
        }

        if( in_array($row['contrib1_type'], $act_types) ) {
            $contrib1 = 'act';
            $act1type = htmlspecialchars_decode($row['contrib1_type']);
            $act1desc = htmlspecialchars_decode($row['contrib1_desc']);
            $act1need = htmlspecialchars_decode($row['contrib1_need']);
        } else {
            $contrib1 = htmlspecialchars_decode($row['contrib1_type']);
            $contrib1desc = htmlspecialchars_decode($row['contrib1_desc']);
        }

        $partner = htmlspecialchars_decode($row['partner']);
        $motivation = htmlspecialchars_decode($row['motivation']);
        $familiar = htmlspecialchars_decode($row['familiar']);
        $preparations = htmlspecialchars_decode($row['preparations']);
        if( $preparations == "N" ) {
            $preparationsbox = FALSE;
            $preparations = "";
        } else {
            $preparationsbox = TRUE;
        }
        $returnVal = '<div class="alert alert-success" role="alert">We hebben al een inschrijving van je ontvangen. Als je wilt kun je die hier aanpassen.</div>';
        $is_save = TRUE;
    } else {
        //this is ok
    }
} 

function addError($value) {
    global $returnVal;
    $returnVal .= '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' . $value . '</div>';
}

?>
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
                <div class="form-intro-text">
                <h1>Inschrijven Familiar Voorjaar</h1>
                <p class="lead">
                    5, 6 en 7 mei 2017.
                </p>
                <p>
                    Vul dit formulier zo volledig mogelijk in. Ook nadat je het formulier hebt verstuurd, kun je nog tot 15 februari 2017 je antwoorden wijzigen. Pas op 15 februari 2017 maken wij je inschrijving definitief. Heb je hulp nodig of wil je meer informatie over het inschrijven? Dan kun je mailen naar: <?php echo $mailtolink ?>
                </p>
            </div>
            <?php echo $returnVal; ?>

            <form id="signup-form" method="post" action="<?php echo substr(htmlspecialchars($_SERVER["PHP_SELF"]),0,-4);?>" target="_top">
                
                <fieldset>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="motivation">Motivatie</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="motivation" id="motivation" cols="60" rows="4"><?php echo $motivation; ?></textarea>
                            <label for="motivation">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="familiar">Welke vraag heb je altijd al in een inschrijfformulier willen zien?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="familiar" id="familiar" cols="60" rows="4"><?php echo $familiar; ?></textarea>
                            <label for="familiar">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                        <label class="col-sm-2" for="partner">Lieveling<br>Email</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="email" name="partner" id="partner" placeholder="Lieveling" value="<?php echo $partner; ?>">
                            <div class="alert alert-success">
                                Je kunt voor Familiar Voorjaar wederom je beste vriend, vriendin, partner, kind of oma opgeven waarmee jij naar Familiar Forest wilt! <br>
                                Je lieveling moet het email adres invullen waarmee jij je registreert hebt en jij het emailadres waarmee jouw lieveling zich inschrijft. Als deze niet overeen komen, kunnen wij jullie niet aan elkaar linken. <strong>Let op: Als jullie van deze optie gebruik maken worden 
                                    jullie samen ingeloot <i>of beide uitgeloot</i></strong>
                            </div>
                        </div>
                    </div>
                </fieldset>
                        
                <fieldset>
                    <legend>Hoe wil jij bijdragen aan het Familiar Voorjaar?</legend>
                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Eerste keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib0" id="contrib0">
                                <option value="iv" <?= $contrib0 == 'iv' ? ' selected="selected"' : '';?>>Interieurverzorging</option>
                                <option value="bar" <?= $contrib0 == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                                <option value="keuken" <?= $contrib0 == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                                <option value="act" <?= $contrib0 == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib0 == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib0desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib0desc" id="contrib0desc" cols="60" rows="4"><?php echo $contrib0desc; ?></textarea>
                            <label id="contrib0counter" for="contrib0desc">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0type">Informatie over je act of performance</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="act0type" id="act0type">
                                <option value="workshop" <?= $act0type == 'workshop' ? ' selected="selected"' : '';?>>Workshop / Cursus</option>
                                <option value="game" <?= $act0type == 'game' ? ' selected="selected"' : '';?>>Ervaring / Game</option>
                                <option value="lecture" <?= $act0type == 'lecture' ? ' selected="selected"' : '';?>>Lezing</option>
                                <option value="schmink" <?= $act0type == 'schmink' ? ' selected="selected"' : '';?>>Schmink</option>
                                <option value="other" <?= $act0type == 'other' ? ' selected="selected"' : '';?>>Anders</option>
                                <option value="perform" <?= $act0type == 'perform' ? ' selected="selected"' : '';?>>Performance</option>
                                <option value="install" <?= $act0type == 'install' ? ' selected="selected"' : '';?>>Installatie / Beeld</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0desc">Omschrijving van je act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0desc" id="act0desc" cols="60" rows="4"><?php echo $act0desc; ?></textarea>
                            <label for="act0desc">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act0need">Wat heb je voor je act nodig?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act0need" id="act0need" cols="60" rows="4"><?php echo $act0need; ?></textarea>
                            <label for="act0need">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="contrib0" class="col-sm-2 form-control-label">Tweede keus</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="contrib1" id="contrib1">
                                <option value="iv" <?= $contrib1 == 'iv' ? ' selected="selected"' : '';?>>Interieurverzorging</option>
                                <option value="bar" <?= $contrib1 == 'bar' ? ' selected="selected"' : '';?>>Bar</option>
                                <option value="keuken" <?= $contrib1 == 'keuken' ? ' selected="selected"' : '';?>>Keuken</option>
                                <option value="act" <?= $contrib1 == 'act' ? ' selected="selected"' : '';?>>Act of Performance</option>
                                <option value="afb" <?= $contrib1 == 'afb' ? ' selected="selected"' : '';?>>Afbouw</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="contrib1desc">Vertel iets over je ervaring hierin</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="contrib1desc" id="contrib1desc" cols="60" rows="4"><?php echo $contrib1desc; ?></textarea>
                            <label id="contrib1counter" for="contrib1desc">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1type">Informatie over je act of performance</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="act1type" id="act1type">
                                <option value="workshop" <?= $act1type == 'workshop' ? ' selected="selected"' : '';?>>Workshop / Cursus</option>
                                <option value="game" <?= $act1type == 'game' ? ' selected="selected"' : '';?>>Ervaring / Game</option>
                                <option value="lecture" <?= $act1type == 'lecture' ? ' selected="selected"' : '';?>>Lezing</option>
                                <option value="schmink" <?= $act1type == 'schmink' ? ' selected="selected"' : '';?>>Schmink</option>
                                <option value="other" <?= $act1type == 'other' ? ' selected="selected"' : '';?>>Anders</option>
                                <option value="perform" <?= $act1type == 'perform' ? ' selected="selected"' : '';?>>Performance</option>
                                <option value="install" <?= $act1type == 'install' ? ' selected="selected"' : '';?>>Installatie / Beeld</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1desc">Omschrijving van je act</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1desc" id="act1desc" cols="60" rows="4"><?php echo $act1desc; ?></textarea>
                            <label for="act1desc">Max 1024 karakters</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="act1need">Wat heb je voor je act nodig?</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="act1need" id="act1need" cols="60" rows="4"><?php echo $act1need; ?></textarea>
                            <label for="act1need">Max 1024 karakters</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="preparations">Voorbereidingen</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label for="preparationsbox">
                                    <input class="checkbox" type="checkbox" id="preparationsbox" name="preparationsbox" <?php if($preparationsbox) echo( "checked"); ?>>
                                    Ik vind het leuk om te helpen in de voorbereidingen
                                </label>
                            </div>
                            <div class="alert alert-success" id="prepinfo">We zijn altijd op zoek naar enthoursiastelingen die ons willen helpen bij de voorbereidingen voor Famliar Forest. Lijkt het je leuk om ons hierbij te helpen?</div>
                            <div class="alert alert-success" id="prepintro">Te gek! Wat zou je leuk vinden om te doen?</div>
                            <textarea class="form-control" name="preparations" id="preparations" cols="60" rows="4"><?php echo $preparations; ?></textarea>
                            <label id="prepcounter" for="preparations">Max 1024 karakters</label>
                        </div>
                    </div>
                </fieldset>
                    
                <fieldset>
                    <legend>Voorwaarden</legend>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms3">Telefoon en Foto's</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Familiar Forest zorgt voor een professionele fotograaf. Om de sfeer te verhogen en het contact tussen deelnemers te stimuleren is het niet toegestaan een telefoon of camera mee te nemen op het terrein van Familiar Voorjaar.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms0" name="terms0" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms0" class="error" style="display:none;"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        
                        <label class="col-sm-2 form-control-label" for="terms1">Verzekering</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Familiar Voorjaar is een reis. De locatie verplicht de deelnemer om zich te kunnen identificeren en minimaal WA verzekerd te zijn.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms1" name="terms1" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms1" class="error" style="display:none;"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms2">Gezondheid</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Tijdens Familiar Voorjaar is de deelnemer verantwoordelijk voor zijn eigen gezondheid. Als deelnemer is het niet mogelijk Familiar Forest aansprakelijk te stellen voor materiële en immateriële schade. <i>Je kunt deze verzekeren door een reisverzekering af te sluiten</i></div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms2" name="terms2" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms2" class="error" style="display:none;"></label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="terms3">Kaart verkoop</label>
                        <div class="col-sm-10">
                            <div class="alert alert-warning">Aanmeldingen en toegangsbewijzen zijn persoonlijk en mogen niet zelf door de deelnemer worden doorverkocht.</div>
                            <div class="checkbox">
                                <label>
                                    <input class="checkbox" type="checkbox" id="terms3" name="terms3" value="J">
                                    Ik ga akkoord met deze voorwaarde
                                </label>
                            </div>
                            <label for="terms3" class="error" style="display:none;"></label>
                        </div>
                    </div>
                </fieldset>
                <button class="btn btn-lg btn-primary btn-block" type="submit">
                    <?php if( !$is_save ) { 
                        echo '<i class="fa fa-paper-plane"></i> Versturen';
                    } else {
                        echo '<i class="glyphicon glyphicon-floppy-disk"></i> Opslaan';
                    }
                    ?>
                </button>
            </form>
        </div>
    </div>
</div>
	</div>
        <?php include("form-js.html"); ?>
        <script src="js/signup.js"></script>
    </body>
</html>