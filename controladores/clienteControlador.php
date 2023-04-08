<?php

    if($peticionAjax){
        require_once "../modelos/clienteModelo.php";
    }else{
        require_once "./modelos/clienteModelo.php";
    }

    class clienteControlador extends clienteModelo{

        /*----- Controlador para agregar cliente -----*/
        public function agregar_cliente_controlador(){
            
            $dni = mainModel::limpiar_cadena($_POST['cliente_dni_reg']);
            $nombre = mainModel::limpiar_cadena($_POST['cliente_nombre_reg']);
            $apellido = mainModel::limpiar_cadena($_POST['cliente_apellido_reg']);
            $telefono = mainModel::limpiar_cadena($_POST['cliente_telefono_reg']);
            $direccion = mainModel::limpiar_cadena($_POST['cliente_direccion_reg']);

            /*== Comprobar campos vacíos ==*/
            if($dni=="" || $nombre=="" || $apellido=="" || $telefono=="" || $direccion==""){
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
            if(mainModel::verificar_datos("[0-9-]{1,27}",$dni)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El DNI no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$nombre)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El nombre no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$apellido)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "El apellido no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            
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
            

            if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,150}",$direccion)){
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "La dirección no coincide con el formato solicitado",
                    "Tipo" => "error"
                ];
                echo json_encode($alerta);
                exit();
            }

            /*== Comprobando DNI ==*/
            $check_dni = mainModel::ejecutar_consulta_simple("SELECT cliente_dni FROM cliente WHERE cliente_dni = '$dni';");
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

            $datos_cliente_reg = [
                "DNI" => $dni,
                "Nombre" => $nombre,
                "Apellido" => $apellido,
                "Telefono" => $telefono,
                "Direccion" => $direccion
            ];

            $agregar_cliente = clienteModelo::agregar_cliente_modelo($datos_cliente_reg);

            if($agregar_cliente->rowCount()==1){
                $alerta = [
                    "Alerta" => "limpiar",
                    "Titulo" => "!Cliente registrado!",
                    "Texto" => "Los datos del cliente han sido registrado con éxito",
                    "Tipo" => "success"
                ];
            }else{
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "¡Ocurrió un error inesperado!",
                    "Texto" => "No se ha podido realizar el registro del cliente",
                    "Tipo" => "error"
                ];
            }

            echo json_encode($alerta);

        }//Fin controlador para agregar cliente

        /*----- Controlador para paginar clientes -----*/
        public function paginador_cliente_controlador($pagina,$num_registros,$privilegio,$url,$busqueda){

            $pagina = mainModel::limpiar_cadena($pagina);
            $num_registros = mainModel::limpiar_cadena($num_registros);
            $privilegio = mainModel::limpiar_cadena($privilegio);

            $url = mainModel::limpiar_cadena($url);
            $url = SERVER_URL.$url."/";

            $busqueda = mainModel::limpiar_cadena($busqueda);
            $tabla = "";

            $pagina = (isset($pagina) && $pagina > 0 ) ? (int) $pagina : 1 ;
            $inicio = ($pagina > 0 ) ? (($pagina*$num_registros) - $num_registros ) : 0 ;

            if(isset($busqueda) && $busqueda != "" ){
                $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM cliente WHERE 
                            (cliente_dni LIKE '%$busqueda%' OR cliente_nombre LIKE '%$busqueda%' 
                            OR cliente_apellido LIKE '%$busqueda%' OR cliente_telefono LIKE '%$busqueda%') 
                            ORDER BY cliente_nombre ASC LIMIT $inicio,$num_registros;";
            }else{
                $consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM cliente
                            ORDER BY cliente_nombre ASC LIMIT $inicio,$num_registros";
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
                            <th>APELLIDO</th>
                            <th>TELÉFONO</th>
                            <th>DIRECCIÓN</th>';
                            if($privilegio==1 || $privilegio==2){
                                $tabla .= '<th>ACTUALIZAR</th>';
                            }
                            if($privilegio==1 ){
                                $tabla .= '<th>ELIMINAR</th>';
                            }
            $tabla .=   '</tr>
                    </thead>
                    <tbody>
            ';

            if($total >= 1 && $pagina <= $num_paginas){

                $contador = $inicio + 1;
                $reg_inicio = $inicio + 1;
                foreach($datos as $rows){

                    $tabla .= '
                    <tr class="text-center">
                        <td>'.$contador.'</td>
                        <td>'.$rows['cliente_dni'].'</td>
                        <td>'.$rows['cliente_nombre'].'</td>
                        <td>'.$rows['cliente_apellido'].'</td>
                        <td>'.$rows['cliente_telefono'].'</td>
                        <td> 
                            <button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" 
                                    title="'.$rows['cliente_nombre'].' '.$rows['cliente_apellido'].'" 
                                    data-content="'.$rows['cliente_direccion'].'">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </td>';
                    if($privilegio==1 || $privilegio==2){
                        $tabla .= '
                            <td>
                                <a href="'.SERVER_URL.'client-update/'.mainModel::encryption($rows['cliente_id']).'/" class="btn btn-success">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            </td>
                        ';
                    }

                    if($privilegio==1){
                        $tabla .= '
                            <td>
                                <form class="FormularioAjax" action="'.SERVER_URL.'ajax/clienteAjax.php" method="POST" data-form="delete" autocomplete="off">
                                    <input type="hidden" name="cliente_id_del" value="'.mainModel::encryption($rows['cliente_id']).'">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        ';
                    }
                        
                        
                    $tabla .= '    
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
                <p class="text-right"> Mostrando cliente '.$reg_inicio.' al '.$reg_final.' de un total de '.$total.' </p>
                ';

                $tabla .= mainModel::paginador_tablas($pagina,$num_paginas,$url,7);
            }
            return $tabla;

        }//Fin controlador para paginar cliente
        

    }