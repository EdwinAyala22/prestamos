<?php

    if($peticionAjax){
        require_once "../modelos/usuarioModelo.php";
    }else{
        require_once "./modelos/usuarioModelo.php";
    }

    class usuarioControlador extends usuarioModelo{

        /*----- Controlador para agregar usuario -----*/
        public function agregar_usuario_controlador(){
            $dni = mainModel::limpiar_cadena($_POST['usuario_dni_reg']);
            $nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_reg']);
            $apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_reg']);
            $telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_reg']);
            $direccion = mainModel::limpiar_cadena($_POST['usuario_direccion_reg']);

            $usuario = mainModel::limpiar_cadena($_POST['usuario_usuario_reg']);
            $email = mainModel::limpiar_cadena($_POST['usuario_email_reg']);
            $clave1 = mainModel::limpiar_cadena($_POST['usuario_clave_1_reg']);
            $clave2 = mainModel::limpiar_cadena($_POST['usuario_clave_2_reg']);

            $privilegio = mainModel::limpiar_cadena($_POST['usuario_privilegio_reg']);

            /*== Comprobar campos vacíos ==*/
            if($dni=="" || $nombre=="" || $apellido=="" || $usuario=="" || $clave1=="" || $clave2==""){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No has llenado todos los campos  obligatorios",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Verificando integridad de los datos ==*/
            if(mainModel::verificar_datos("[0-9-]{10,20}",$dni)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El DNI no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$nombre)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El nombre coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$apellido)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El apellido coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if($telefono!=""){
                if(mainModel::verificar_datos("[0-9()+]{8,20}",$telefono)){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "¡Ocurrió un error inesperado!",
                        "Texto" => "El teléfono coincide con el formato solicitado",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            }

            if($direccion!=""){
                if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}",$direccion)){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "¡Ocurrió un error inesperado!",
                        "Texto" => "La dirección coincide con el formato solicitado",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            }

            if(mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El nombre de usuario coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave2)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "Las contraseñas no coinciden con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando DNI ==*/
            $check_dni = mainModel::ejecutar_consulta_simple("SELECT usuario_dni FROM usuario WHERE usuario_dni = '$dni';");
            if($check_dni->rowCount()>0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El DNI digitado ya existe",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando Nombre Usuario ==*/
            $check_user = mainModel::ejecutar_consulta_simple("SELECT usuario_usuario FROM usuario WHERE usuario_usuario = '$usuario';");
            if($check_user->rowCount()>0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El nombre de usuario digitado ya existe",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando Email ==*/
            if(!empty($email)){
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $check_email = mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email = '$email';");
                    if($check_email->rowCount()>0){
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "¡Ocurrió un error inesperado!",
                            "Texto" => "El email digitado ya existe",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                }else{
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "¡Ocurrió un error inesperado!",
                        "Texto" => "El email digitado no es válido",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            }

            /*== Comprobando Contraseñas ==*/
            if($clave1!=$clave2){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "Las contraseñas digitadas no son iguales",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }else{
                $pass = mainModel::encryption($clave1);
            }

            /*== Comprobando Privilegio ==*/
            if($privilegio < 1 || $privilegio > 3){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El privilegio seleccionado no es válido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $datos_usuario_reg = [
                "DNI" => $dni,
                "Nombre" => $nombre,
                "Apellido" => $apellido,
                "Telefono" => $telefono,
                "Direccion" => $direccion,
                "Email" => $email,
                "Usuario" => $usuario,
                "Clave" => $pass,
                "Estado" => "Activa",
                "Privilegio" => $privilegio
            ];

            $agregar_usuario = usuarioModelo::agregar_usuario_modelo($datos_usuario_reg);

            if($agregar_usuario->rowCount()==1){
                $alerta = [
                    "Alerta" => "limpiar",
                    "Titulo" => "!Usuario registrado!",
                    "Texto" => "Los datos del usuario han sido registrado con éxito",
                    "Tipo" => "success"
                ];
            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No se ha podido realizar el registro del usuario",
                    "Tipo" => "error"
                ];
            }

            echo json_encode($alerta);

        } //Fin controlador para agregar usuario

    }
