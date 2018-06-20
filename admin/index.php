<?php
    session_start();
    require_once("cart_conf/config_b.php");
    require_once("cart_conf/db_b.php");
    require_once("../funzioni/funzioniphp.php");
    require_once("../funzioni/funzioniphp_specifiche.php");

    //controllo se si tratta di una cancellazione!
    if (isset($_GET["del"]) and isset($_POST["checkbox"]))
    {
        foreach($_POST["checkbox"] as $recorddacancellare)
        {
            //richiamo la funzione per cancellare, eventualmente, la foto correlata
            //cancella_immagine($con, "filter", "id_filter", $recorddacancellare, "img", "../images/category/");
            $querycanc = "delete from users where id = $recorddacancellare";
            $ris=mysqli_query($con, $querycanc) or die ("Query fallita!");

        }

    }

    if( isset( $_REQUEST['download'] )){
        $filename = "lista_invitati.xls"; // File Name
        // Download file
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        $user_query = mysql_query('select * from users');
        // Write data to file
        $flag = false;
        while ($row = mysql_fetch_assoc($user_query)) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            echo implode("\t", array_values($row)) . "\r\n";
        }
    }


?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Administrator Panel</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="css/plugins/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../index.php">Administrator Panel</a>
            </div>
            <!-- /.navbar-header -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <?php require_once("menu.php"); ?>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>

            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-clock-o fa-fw"></i> <strong>Lista delle categorie di prodotto inserite</strong>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form>
                                <input type="submit" name="download" value="Download" />
                            </form>
                            <ul class="timeline">

                                <form method="post" name="form" action="index.php?del=si">

                                <table cellpadding="0" cellspacing="0">

                                <?php

                                    $query="select * from users order by name DESC";
                                    $result=mysqli_query($con, $query);
                                    $num=mysqli_num_rows($result);


                                //*******Codice per la paginazione********************
                                    if(!isset($_SESSION["recorddipartenza_users"]) or isset($_GET["primoaccesso"])){
                                        $_SESSION["recorddipartenza_users"]=0;
                                    }

                                    if(isset($_GET["go"])){
                                        if($_GET["go"]=="inizio"){
                                            $_SESSION["recorddipartenza_users"]=0;
                                        }

                                        else if($_GET["go"]=="indietro"){
                                            $_SESSION["recorddipartenza_users"]=$_SESSION["recorddipartenza_users"]-10;

                                            if($_SESSION["recorddipartenza_users"]<0){
                                                $_SESSION["recorddipartenza_users"]=0;
                                            }
                                        }

                                        else if($_GET["go"]=="avanti"){
                                            $_SESSION["recorddipartenza_users"]=$_SESSION["recorddipartenza_users"]+10;
                                        }

                                        elseif($_GET["go"]=="fine"){
                                            $_SESSION["recorddipartenza_users"]=$num-10;
                                        }
                                    }
                                //***********fine codice per paginazione**********************+

                                    $query="select * from users order by name desc limit " .$_SESSION["recorddipartenza_users"]. ", 10";
                                    $result=mysqli_query($con, $query);
                                    $num_pagina=mysqli_num_rows($result);

                                    print "<tr>";
                                    print "<td align=\"center\" width='200px' id='bordo'><b>Nome</b></td>";
                                    print "<td align=\"center\" width='200px' id='bordo'><b>cognome</b></td>";
                                    print "<td align=\"center\" width='200px' id='bordo'><b>email</b></td>";
                                    print "<td align=\"center\" width='200px' id='bordo'><b>partecipanti</b></td>";
                                    print "<td align=\"center\" width='200px' id='bordo'><b>pernottamento</b></td>";
                                    print "<td align=\"center\" id='bordo'><b>Selezione</b></td>";
                                    print "</tr>";


                                    while($row=mysqli_fetch_array($result)){
                                        $detail_view = $row["vacancy"] === '1'?'Si':'No';

                                        print "<tr style=\"border-bottom:1px solid #ddd\">";
                                        print "<td align=\"center\" height=\"25\" id='bordo'>";
                                        print "<a href='users_detail.php?keyUsers=" .$row["id"]. "'>" .$row["name"]. "</a>";
                                        print "</td>";
                                        print "<td align=\"center\" height=\"25\" id='bordo'>" . $row["surname"]. "</td>";
                                        print "<td align=\"center\" height=\"25\" id='bordo'>" . $row["email"]. "</td>";
                                        print "<td align=\"center\" height=\"25\" id='bordo'>" . $row["number"]. "</td>";
                                        print "<td align=\"center\" height=\"25\" id='bordo'>" . $detail_view. "</td>";
                                        print "<td width=\"50px\" align=\"center\" height=\"25\" id='bordo'><input type=\"checkbox\" name=\"checkbox[]\" value=\"" .$row["id"]. "\"></td>";
                                        print "</tr>";
                                    }

                                    if($num > 0){
                                        print "<tr><td colspan=\"6\" align=\"center\"><br><input type=\"submit\" name=\"Submit\" value=\"Elimina\" onClick=\"javascript: res=confirm('Sei sicuro di voler cancellare gli articoli selezionati?'); if (res) return true; else return false;\" class=\"bottone\"></td></tr>";
                                    }

                                ?>

                                <tr>
                                    <td colspan="6" align="center">
                                        <font size="1">

                                <?php

                                //
                                // print dei record visualizzati
                                //

                                    $ultimorecordvisualizzato=$_SESSION["recorddipartenza_users"]+10;

                                    if($ultimorecordvisualizzato>=$num){
                                        $ultimorecordvisualizzato=$num;
                                    }

                                    if($num==0){
                                        $recordiniziale=0;
                                    }
                                    else{
                                        $recordiniziale=$_SESSION["recorddipartenza_users"]+1;
                                    }

                                    echo "<BR>Risultati " .$recordiniziale. " - " .$ultimorecordvisualizzato. " di " .$num. "<BR><BR>";

                                ?>

                                        </font>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" align="center">
                                        <font size="1">

                                <?php

                                    if($num > 10){
                                        if($recordiniziale==1 or $recordiniziale==0){
                                            echo "<<< Inizio&nbsp;&nbsp;&nbsp;&nbsp;<< Indietro&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                        }
                                        else{
                                            echo "<a href=\"index.php?go=inizio\" class='link'><<< Inizio</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?go=indietro\" class='link'><< Indietro</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                        }

                                        if($ultimorecordvisualizzato==$num){
                                            echo "Avanti >>&nbsp;&nbsp;&nbsp;&nbsp;Fine>>>";
                                        }
                                        else{
                                            echo "<a href=\"index.php?go=avanti\" class='link'>Avanti >></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?go=fine\" class='link'>Fine >>></a>";
                                        }
                                    }
                                ?>
                                        </font>
                                    </td>
                                </tr>
                        </table>

                                </form>
                            </ul>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="js/plugins/morris/raphael.min.js"></script>
    <script src="js/plugins/morris/morris.min.js"></script>
    <script src="js/plugins/morris/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

</body>

</html>
