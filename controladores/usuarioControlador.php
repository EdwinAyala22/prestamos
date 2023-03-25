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
                    "Texto" => "El nombre no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$apellido)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El apellido no coincide con el formato solicitado",
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
                        "Texto" => "El teléfono no coincide con el formato solicitado",
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
                        "Texto" => "La dirección no coincide con el formato solicitado",
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
                    "Texto" => "El nombre de usuario no coincide con el formato solicitado",
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

        /*----- Controlador para paginar usuarios -----*/
        public function paginador_usuario_controlador($pagina,$num_registros,$privilegio,$id,$url,$busqueda){

            $pagina = mainModel::limpiar_cadena($pagina);
            $num_registros = mainModel::limpiar_cadena($num_registros);
            $privilegio = mainModel::limpiar_cadena($privilegio);
            $id = mainModel::limpiar_cadena($id);

            $url = mainModel::limpiar_cadena($url);
            $url = SERVER_URL.$url."/";

            $busqueda = mainModel::limpiar_cadena($busqueda);
            $tabla = "";

            $pagina = (isset($pagina) && $pagina > 0 ) ? (int) $pagina : 1 ;
            $inicio = ($pagina > 0 ) ? (($pagina*$num_registros) - $num_registros ) : 0 ;

            if(isset($busqueda) && $busqueda != "" ){
                $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE ((usuario_id != $id AND usuario_id != 1) 
                            AND (usuario_dni LIKE '%$busqueda%' OR usuario_nombre LIKE '%$busqueda%' 
                            OR usuario_apellido LIKE '%$busqueda%' OR usuario_telefono LIKE '%$busqueda%' 
                            OR usuario_email LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%')) 
                            ORDER BY usuario_nombre ASC LIMIT $inicio,$num_registros;";
            }else{
                $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE 
                            usuario_id != $id AND usuario_id !=1 ORDER BY 
                            usuario_nombre ASC LIMIT $inicio,$num_registros";
            }

            $conexion = mainModel::conectar();

            $datos = $conexion->query($consulta);
            $datos = $datos->fetchAll();
            
            $total = $conexion->query("SELECT FOUND_ROWS()");
            $total = (int) $total->fetchColumn();

            $num_paginas = ceil($total/$num_registros);

            $tabla .= '
            <div class="table-responsive">
                <table class="table table-dark table-sm">
                    <thead>
                        <tr class="text-center roboto-medium">
                            <th>#</th>
                            <th>DNI</th>
                            <th>NOMBRE</th>
                            <th>TELÉFONO</th>
                            <th>USUARIO</th>
                            <th>EMAIL</th>
                            <th>ACTUALIZAR</th>
                            <th>ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            if($total >= 1 && $pagina <= $num_paginas){

                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;
                foreach($datos as $rows){

                    $telefono_v = ($rows['usuario_telefono'] == '' ) ? 'N/A' : $rows['usuario_telefono'] ;
                    $correo_v = ($rows['usuario_email'] == '' ) ? 'N/A' : $rows['usuario_email'] ;

                    $tabla .= '
                    <tr class="text-center">
                        <td>'.$contador.'</td>
                        <td>'.$rows['usuario_dni'].'</td>
                        <td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
                        <td>'.$telefono_v.'</td>
                        <td>'.$rows['usuario_usuario'].'</td>
                        <td>'.$correo_v.'</td>
                        <td>
                            <a href="'.SERVER_URL.'user-update/'.mainModel::encryption($rows['usuario_id']).'/" class="btn btn-success">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </td>
                        <td>
                            <form class="FormularioAjax" action="'.SERVER_URL.'ajax/usuarioAjax.php" method="POST" data-form="delete" autocomplete="off">
                                <input type="hidden" name="usuario_id_del" value="'.mainModel::encryption($rows['usuario_id']).'">
                                <button type="submit" class="btn btn-warning">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
				    </tr>
                    ';
                    $contador++;
                }

                $reg_final = $contador - 1;

            }else{
                
                if($total >= 1){
                    $tabla .= '
                        <tr class="text-center">
                            <td colspan = "9" >
                                <a href = "'.$url.'" class = "btn btn-raised btn-primary btn-sm">
                                    Haga click aquí para recargar el listado
                                </a>
                            </td>
                        </tr>
                    ';
                }else{
                    $tabla .= '
                        <tr class="text-center">
                            <td colspan = "9" > No hay registros. </td>
                        </tr>
                    ';
                }

            }

            $tabla .= '
                    </tbody>
                </table>
            </div>
            ';

            if($total >= 1 && $pagina <= $num_paginas){
                $tabla .= '
                <p class="text-right"> Mostrando usuario '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.' </p>
                ';

                $tabla .= mainModel::paginador_tablas($pagina,$num_paginas,$url,7);
            }
            return $tabla;

        }//Fin controlador para paginar usuario

        /*----- Controlador para agregar usuario -----*/
        public function eliminar_usuario_controlador(){
            /* Recibiendo id del usuario  */
            $id = mainModel::decryption($_POST['usuario_id_del']);
            $id = mainModel::limpiar_cadena($id);

            /* Comprobando el usuario */
            if($id == 1){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "Este usuario no se puede eliminar",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /* Comprobando el usuario  en BD*/
            $check_usuario_id = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM 
                                usuario WHERE usuario_id = $id;");
            if($check_usuario_id->rowCount() <= 0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El usuario a eliminar no existe",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /* Comprobando prestamos*/
            $check_prestamos_id = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM 
                                prestamo WHERE usuario_id = $id LIMIT 1;");
            if($check_prestamos_id->rowCount() > 0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No se puede eliminar este usuraio, porque tiene prestamos asociados. 
                    Recomendamos deshabilitar el usuario si ya no será utilizado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /* Comprobando privilegios*/
            session_start(['name'=>'SPM']);
            if($_SESSION['privilegio_spm'] != 1 ){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No tienes permiso para eliminar usuarios",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            $eliminar_usuario = usuarioModelo::eliminar_usuario_modelo($id);

            if($eliminar_usuario->rowCount() == 1){
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "¡Usuario eliminado!",
                    "Texto" => "El usuario ha sido eliminado completamente",
                    "Tipo" => "success"
                ];
            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No se logró eliminar el usuario, intente nuevamente",
                    "Tipo" => "error"
                ];
            }

            echo json_encode($alerta);

        }//Fin controlador para elimianr usuario

        /*----- Controlador datos usuario -----*/
        public function datos_usuario_controlador($tipo,$id){
            /*   */
            $tipo = mainModel::limpiar_cadena($tipo);
            $id = mainModel::decryption($id);
            $id = mainModel::limpiar_cadena($id);

            return usuarioModelo::datos_usuario_modelo($tipo,$id);

        }//Fin controlador datos usuario

        /*----- Controlador actualizar usuario -----*/
        public function actualizar_usuario_controlador(){
            /* Recibiendo id */
            $id = mainModel::decryption($_POST['usuario_id_up']);
            $id = mainModel::limpiar_cadena($id);

            /* Comprobar el usuario en la bd */
            $check_user = mainModel::ejecutar_consulta_simple("SELECT * FROM usuario WHERE usuario_id = $id;");
            if($check_user->rowCount()<=0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No se ha encontrado el usuario",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }else{
                $campos = $check_user->fetch();
            }

            $dni = mainModel::limpiar_cadena($_POST['usuario_dni_up']);
            $nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_up']);
            $apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_up']);
            $telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_up']);
            $direccion = mainModel::limpiar_cadena($_POST['usuario_direccion_up']);

            $usuario = mainModel::limpiar_cadena($_POST['usuario_usuario_up']);
            $email = mainModel::limpiar_cadena($_POST['usuario_email_up']);
            
            if(isset($_POST['usuario_estado_up'])){
                $estado = mainModel::limpiar_cadena($_POST['usuario_estado_up']);
            }else{
                $estado = $campos['usuario_estado'];
            }

            if(isset($_POST['usuario_privilegio_up'])){
                $privilegio = mainModel::limpiar_cadena($_POST['usuario_privilegio_up']);
            }else{
                $privilegio = $campos['usuario_privilegio'];
            }

            $admin_usuario = mainModel::limpiar_cadena($_POST['usuario_admin']);
            $admin_clave = mainModel::limpiar_cadena($_POST['clave_admin']);

            $tipo_cuenta = mainModel::limpiar_cadena($_POST['tipo_cuenta']);

            /* Comprobar campos vacíos */
            if($dni=="" || $nombre=="" || $apellido=="" || $usuario=="" || $admin_usuario=="" || $admin_clave==""){
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
                    "Texto" => "El nombre no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$apellido)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El apellido no coincide con el formato solicitado",
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
                        "Texto" => "El teléfono no coincide con el formato solicitado",
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
                        "Texto" => "La dirección no coincide con el formato solicitado",
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
                    "Texto" => "El nombre de usuario no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$admin_usuario)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "Tu nombre de usuario no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$admin_clave)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "Tu clave no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }
            
            $admin_clave = mainModel::encryption($admin_clave);

            if($privilegio < 1 || $privilegio >3){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El privilegio seleccionado no es válido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if($estado != "Activa" && $estado != "Deshabilitada"){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El estado seleccionado no es válido",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando DNI ==*/
            if($dni != $campos['usuario_dni']){
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
            }

            /*== Comprobando Nombre Usuario ==*/
            if($usuario != $campos['usuario_usuario']){
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
            }
            
            /*== Comprobando Email ==*/
            if($email != $campos['usuario_email'] && $email != ""){
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $check_email = mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email = '$email';");
                    if($check_email->rowCount()>0){
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "¡Ocurrió un error inesperado!",
                            "Texto" => "El nuevo email digitado ya está registrado",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }
                }else{
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "¡Ocurrió un error inesperado!",
                        "Texto" => "El correo no es válido",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }
            }

            /*== Comprobando Claves ==*/
            if($_POST['usuario_clave_nueva_1'] != "" || $_POST['usuario_clave_nueva_2'] != "" ){

                if($_POST['usuario_clave_nueva_1'] != $_POST['usuario_clave_nueva_2']){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "¡Ocurrió un error inesperado!",
                        "Texto" => "Las nuevas contraseñas digitadas no son iguales",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }else{
                    if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$_POST['usuario_clave_nueva_1']) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$_POST['usuario_clave_nueva_2'])){
                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "¡Ocurrió un error inesperado!",
                            "Texto" => "Las nuevas contraseñas no coinciden con el formato solicitado",
                            "Tipo" => "error"
                        ];
                        echo json_encode($alerta);
                        exit();
                    }

                    $clave = mainModel::encryption($_POST['usuario_clave_nueva_1']);

                }

            }else{
                $clave = $campos['usuario_clave'];
            }

            /*== Comprobando credenciales ==*/
            if($tipo_cuenta == "Propia"){
                $check_cuenta = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM 
                usuario WHERE usuario_usuario = '$admin_usuario' AND 
                              usuario_clave = '$admin_clave' AND 
                              usuario_id = $id;");
            }else{
                session_start(['name'=>'SPM']);
                if($_SESSION['privilegio_spm']!=1){
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "¡Ocurrió un error inesperado!",
                        "Texto" => "No tienes los permisos necesarios para realizar esta operación",
                        "Tipo" => "error"
                    ];
                    echo json_encode($alerta);
                    exit();
                }

                $check_cuenta = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM 
                usuario WHERE usuario_usuario = '$admin_usuario' AND 
                              usuario_clave = '$admin_clave';");

            }

            if($check_cuenta->rowCount()<=0){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "Nombre y contraseña de administrador no válidas",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Preparando datos para enviar al modelo ==*/
            $datos_usuarios_up = [
                "DNI" => $dni,
                "Nombre" => $nombre,
                "Apellido" => $apellido,
                "Telefono" => $telefono,
                "Direccion" => $direccion,
                "Email" => $email,
                "Usuario" => $usuario,
                "Clave" => $clave,
                "Estado" => $estado,
                "Privilegio" => $privilegio,
                "ID" => $id
            ];

            if(usuarioModelo::actualizar_usuario_modelo($datos_usuarios_up)){
                $alerta = [
                    "Alerta" => "recargar",
                    "Titulo" => "¡Datos actualizados!",
                    "Texto" => "Los datos han sido actualizado con éxito",
                    "Tipo" => "success"
                ];
            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No se logró actualizar los datos, intente nuevamente",
                    "Tipo" => "error"
                ];
            }

            echo json_encode($alerta);

        }// FIn controlador actualizar usuario
    }
