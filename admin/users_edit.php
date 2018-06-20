<?php
    require_once("cart_conf/config_b.php");
    require_once("cart_conf/db_b.php");
    require_once("../funzioni/funzioniphp.php");
    require_once("../funzioni/funzioniphp_specifiche.php");

    include("resize.php");

    $id = str_replace("'","&#39;",$_POST["idfilter"]);

    $filter = str_replace("'","&#39;",$_POST["filter"]);
    $description = str_replace("'","&#39;",$_POST["description"]);
    $detailView = str_replace("'", "&#39;", $_POST["detailView"]);

    $percorso="../images/category/";
    $userfile_name = $_FILES["immagine"]["name"];
    $nome_file = dimminomefile($userfile_name);
    $tipofile = tipo_file($userfile_name);

    //devo controllare che il file sia .jpg o .jpeg
    if((trim($tipofile) <> "jpg") and (trim($tipofile) <> "jpeg") and (trim($tipofile) <> "png") and (trim($tipofile) <> "")){
        //cancello l'immagine appena uploadata
        @unlink($percorso.$userfile_name);

        echo "ATTENZIONE! L'immagine deve avere estensione '.jpg' o '.jpeg' o '.png'";
        exit;
    }
    else{

        if(isset($_FILES["immagine"]) && $_FILES["immagine"]["size"]>0){

            if (!move_uploaded_file($_FILES['immagine']['tmp_name'], $percorso . $nome_file . "." . $tipofile)){
                print "Problemi con l'upload del file! Il file non ? stato caricato.";
                exit;
            }
            else{
                $strFoto = $nome_file . "." . $tipofile;
                $resize = new resize;
                $resize->urlimage = $percorso . $nome_file . "." . $tipofile;
                $resize->fisso = 0;
                $resize->maxX = 424;
                $resize->latofisso = "X";
                $resize->folder = "../images/category/";
                //$resize->newName = "nuovonome.jpg";
                $resize->go();
            }

            if(isset($_POST["img"]) and ($_POST["img"] != $_FILES['immagine']['name'])){
                //richiamo la funzione per cancellare, eventualmente, la foto correlata
                cancella_immagine($con, "filter", "id_filter", $id, "img", "../images/category/");
            }

        }

        else{
            if (isset($_POST["idfilter"])){
                $strFoto = $_POST["img"];
            }
        }

    }

    $userfile_name_2 = $_FILES["immagine_2"]["name"];
    $nome_file_2 = dimminomefile($userfile_name_2);
    $tipofile_2 = tipo_file($userfile_name_2);

    //devo controllare che il file sia .jpg o .jpeg
    if((trim($tipofile_2) <> "jpg") and (trim($tipofile_2) <> "jpeg") and (trim($tipofile_2) <> "png") and (trim($tipofile_2) <> "")){
        //cancello l'immagine appena uploadata
        @unlink($percorso.$userfile_name_2);

        echo "ATTENZIONE! L'immagine deve avere estensione '.jpg' o '.jpeg' o '.png'";
        exit;
    }
    else{

        if(isset($_FILES["immagine_2"]) && $_FILES["immagine_2"]["size"]>0){

            if (!move_uploaded_file($_FILES['immagine_2']['tmp_name'], $percorso . $nome_file_2 . "." . $tipofile_2)){
                print "Problemi con l'upload del file! Il file non ? stato caricato.";
                exit;
            }
            else{
                $strFoto_2 = $nome_file_2 . "." . $tipofile_2;
                $resize = new resize;
                $resize->urlimage = $percorso . $nome_file_2 . "." . $tipofile_2;
                $resize->fisso = 0;
                $resize->maxX = 850;
                $resize->latofisso = "X";
                $resize->folder = "../images/category/";
                //$resize->newName = "nuovonome.jpg";
                $resize->go();
            }

            if(isset($_POST["img_2"]) and ($_POST["img_2"] != $_FILES['immagine_2']['name'])){
                //richiamo la funzione per cancellare, eventualmente, la foto correlata
                cancella_immagine($con, "filter", "id_filter", $id, "img_description", "../images/category/");
            }

        }

        else{
            if (isset($_POST["idfilter"])){
                $strFoto_2 = $_POST["img_2"];
            }
        }

    }

    //scrivo ed eseguo la query sul db
    $query="UPDATE filter SET name='" .$filter. "', description='" .$description . "', img='".$strFoto."', img_description='".$strFoto_2."', view_detail=".(int)$detailView." WHERE id_filter = ".$id." ;";

    if(mysqli_query($con, $query)){
        $resultQuery = "Operazione effettuata correttamente!<br /><br />";
    }
    else{
        $resultQuery = "Si sono verificati dei problemi nell'inserimento dei dati.<br /><br />";
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
                            <i class="fa fa-clock-o fa-fw"></i> Category Edit
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <ul class="timeline">

                                <?=$resultQuery?>

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
