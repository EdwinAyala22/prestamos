<script>

    let btn_salir = document.querySelector('.btn-exit-system');

    btn_salir.addEventListener('click', function(e){

        e.preventDefault();

        Swal.fire({
			title: '¿Deseas cerrar sesión?',
			text: "La sesión actual se cerrará",
			type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.value) {
                let url = '<?php echo SERVER_URL; ?>ajax/loginAjax.php';
                let token = '<?php echo $lc->encryption($_SESSION['token_spm']) ?>';
                let usaurio = '<?php echo $lc->encryption($_SESSION['usuario_spm']) ?>';

                let datos = new FormData();
                datos.append("token", token);
                datos.append("usuario", usaurio);

                fetch(url, {
                    method: 'POST',
                    body: datos
                })
                    .then(respuesta=> respuesta.json())
                    .then(respuesta => {
                        return alertas_ajax(respuesta);
                }); 

			}
		});

    });

</script>