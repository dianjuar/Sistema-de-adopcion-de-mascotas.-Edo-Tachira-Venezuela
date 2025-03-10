<?php 
//session_start();
/*===== USER Archivar publicación - Post ON =========================================*/
if(isset($_POST["Guardar_Datos_DuenoActual"]))
{	
	$post_id = $_POST["post-id"];
    cambiarEstado_archivado($post_id);
	$data = get_post_meta( $post_id, 'post', true );

	$data['nombre-dueno'] = sanitize_text_field( $_POST['nombre-Dueno'] );
	$data['telefono-dueno'] = sanitize_text_field( $_POST['telefono-Dueno'] );

	update_post_meta( $post_id, 'post', $data);
}
/*===== USER Archivar publicación - Post OFF ========================================*/

//Cambia el estado de un post a archivado.
function cambiarEstado_archivado($postID)
{ 
    //Esto es cambiar le estado a archivado --open--
    $post = get_post( $postID );
    $post->post_status = "archive";
    wp_update_post( $post );
    //Esto es cambiar le estado a archivado --close--

    ?>
	<script type="text/javascript">
	window.onload = function() {
		swal({
	    title: '¡Que Bien!',
	    text:  '¡Nos alegra mucho que esta mascota haya encontrado un hogar!',
	    type:  'success'
		},
        function()
        {
            window.location.replace(  window.location.href  );
        });
	};
	</script>
    <?php
}
?>

<!-- MODAL --> 
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form  method="post" action="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Finalizar publicación</h4>
                </div>
                <div class="modal-body">
                    <p> 
                        Por favor ingresa los datos de la persona que quedara a cargo de la mascota 
                        <label id="nombre-mascota">"<?php echo $post->post_title; ?>"</label>
                    </p>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Nombre:</label>
                        <input type="text" class="form-control" name="nombre-Dueno" id="nombre-Dueno" placeholder="Nombre" required>
                    </div>                      
                    <div class="form-group">
                        <label for="exampleInputPassword1">Teléfono</label>
                                                    <!--+58(0426)702-2951-->
                                                    <!-- 04267022951 -->
                        <input type="text" pattern="^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3,4})[-. )]*(\d{3})[-. ]*(\d{2})[-. ]*(\d{2})(?: *x(\d+))?\s*$" class="form-control" name="telefono-Dueno" id="telefono-Dueno" placeholder="teléfono" required>
                        <small><abbr title="Ejemplo">Ejm:</abbr> +58 (0426) 123.45.67</small>
                    </div>
                    <input type="hidden" id="post-id" name="post-id" value="<?php echo $post->ID; ?>">
                </div>
                <div class="modal-footer">
                    <button name="Guardar_Datos_DuenoActual" type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL -->
