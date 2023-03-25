<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title> <?php echo COMPANY ?> </title>
    <?php
        include "./vistas/inc/links.php";
    ?>
</head>
<body>

    <?php

        $peticionAjax = false;
        require_once "./controladores/vistasControlador.php"; 
        $Iv = new vistasControlador();

        $vistas = $Iv->obtener_vistas_controlador();

        if($vistas == "login" || $vistas == "404"){

            require_once "./vistas/contenidos/".$vistas."-view.php";

        }else{

            session_start(['name'=>'SPM']);

            $pagina = explode('/', $_GET['views']);

            // echo ' <script> console.log("'.$_SESSION['nombre_spm'].'") </script> ';
            // echo ' <script> console.log("'.$_SESSION['id_spm'].'") </script> ';

            require_once "./controladores/loginControlador.php";
            $lc = new loginControlador();
            
            if(!isset($_SESSION['token_spm']) || !isset($_SESSION['usuario_spm']) || !isset($_SESSION['privilegio_spm']) || !isset($_SESSION['id_spm'])){
                echo $lc->forzar_cierre_sesion_controlador();
                exit();
            }

        ?>
            <!-- Main container -->
            <main class="full-box main-container">
                <!-- Nav lateral -->
                <?php
                    include "./vistas/inc/navLateral.php";
                ?>
                <!-- Page content -->
                <section class="full-box page-content">
                    <?php
                        include "./vistas/inc/navbar.php";

                        include $vistas;

                    ?>
                </section>
            </main>
    <?php

            include "./vistas/inc/LogOut.php";

        }

        include "./vistas/inc/scripts.php";
    ?>
</body>

</html>