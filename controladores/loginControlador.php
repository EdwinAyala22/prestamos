<?php

    if($peticionAjax){
        require_once "../modelos/loginModelo.php";
    }else{
        require_once "./modelos/loginModelo.php";
    }

    class loginControlador extends loginModelo{

        /*----- Controlador para iniciar sesión -----*/
        public function iniciar_sesion_controlador(){
            $usuario = mainModel::limpiar_cadena($_POST['usuario_log']);
            $clave = mainModel::limpiar_cadena($_POST['clave_log']);

            /*== comprobar campos vacíos ==*/
            if($usuario == "" || $clave == ""){
                echo '
                    <script>
                        Swal.fire({
                            title: "¡Ocurrió un error inesperado!",
                            text: "No has llenado los campos requeridos",
                            type: "error",
                            confirmButtonText: "Aceptar"
                        });
                    </script>
                ';
                exit();
            }

            /*== Verificando integridad de los datos ==*/
            if(mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)){
                    echo '
                        <script>
                            Swal.fire({
                                title: "¡Ocurrió un error inesperado!",
                                text: "El usuario o contraseña no coinciden con el formato solicitado",
                                type: "error",
                                confirmButtonText: "Aceptar"
                            });
                        </script>
                    ';
                    exit();
            }
            if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
                echo '
                    <script>
                        Swal.fire({
                            title: "¡Ocurrió un error inesperado!",
                            text: "El usuario o contraseña no coinciden con el formato solicitado",
                            type: "error",
                            confirmButtonText: "Aceptar"
                        });
                    </script>
                ';
                exit();
            }

            $clave = mainModel::encryption($clave);
            $datos_login = [
                "Usuario" => $usuario,
                "Clave" => $clave
            ];
            
            $datos_cuenta = loginModelo::iniciar_sesion_modelo($datos_login);

            if($datos_cuenta->rowCount()==1){
                
                $row = $datos_cuenta->fetch();

                session_start(['name'=>'SPM']);
                $_SESSION['id_spm'] = $row['usuario_id'];
                $_SESSION['nombre_spm'] = $row['usuario_nombre'];
                $_SESSION['apellido_spm']= $row['usuario_apellido'];
                $_SESSION['usuario_spm']= $row['usuario_usuario'];
                $_SESSION['privilegio_spm']= $row['usuario_privilegio'];
                $_SESSION['token_spm']= md5(uniqid(mt_rand(), true));

                return header("Location: ".SERVER_URL."home/");

            }else{
                echo '
                    <script>
                        Swal.fire({
                            title: "¡Ocurrió un error inesperado!",
                            text: "El usuario o clave son incorrectos",
                            type: "error",
                            confirmButtonText: "Aceptar"
                        });
                    </script>
                ';
            }
        } /*----- Fin controlador -----*/

        /*----- Controlador para forzar cierre sesión -----*/
        public function forzar_cierre_sesion_controlador(){

            session_unset();
            session_destroy();
            if(headers_sent()){
                return "
                    <script>
                        window.location.href='".SERVER_URL."login/';
                    </script>
                ";
            }else{
                header("Location: ".SERVER_URL."login/");
            }

        } /*----- Fin controlador -----*/
        

    }