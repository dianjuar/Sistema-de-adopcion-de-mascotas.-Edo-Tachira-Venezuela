<?php
//*************************************************************************************
//añade los archivos necesarios para que en la parte de administrativa se pueda usar el sweetalert :))))
add_action( 'admin_head', function(){
		?>
			<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url') ?>/js/sweetalert2-master/dist/sweetalert2.css"> 
		    <script src="<?php bloginfo('template_url') ?>/js/sweetalert2-master/dist/sweetalert2.min.js"></script>
		<?php

		//necesario para mostar el modal de finalizar post.
		global $pagenow;

		if($pagenow == "edit.php")	
			require_once ("finalizar post.php");
		
});
//*************************************************************************************
////////////////////////////////////////////////////	
	add_action( 'admin_footer', function() {

		global $pagenow;

		if($pagenow == "edit.php" || $pagenow == "profile.php" || 
		   $pagenow == "edit-comments.php" || $pagenow == "post-new.php" ||
		   $pagenow == "post.php")
		{			
			?>
			<!-- links JQuery y bootstrap.js  -->
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
			<script>window.jQuery || document.write('<script src="<?php bloginfo('template_url') ?>/js/vendor/jquery-1.11.1.min.js"><\/script>')</script>
			<script src="<?php bloginfo('template_url') ?>/js/main.js"></script>
			<script src="<?php bloginfo('template_url') ?>/js/vendor/bootstrap.min.js"></script> 
			<?php

			switch ($pagenow) 
			{
				case 'edit.php':
					ModalAlFinalizarUnPost();
					SweetalertGestionarPost();
					agregarClass_CURRENT_post();

					?>
						<script src="<?php bloginfo('template_url') ?>/js/estados-municipios.js"></script> 
						<script>
							jQuery(document).ready(function($)
							{	
								populateEstados("rpr_estado","rpr_municipio","Todos los Estados");
								$('#rpr_estado').trigger('change');

								<?php 
								if ( isset($_GET['FILTRO_ESTADO']) ) 
								{?>
									$('#rpr_estado').val("<?php echo $_GET['FILTRO_ESTADO'] ?>").trigger('change');
								<?php 
								}
								?>

								<?php 
								if ( isset($_GET['FILTRO_MUNICIPIO']) ) 
								{?>
									$('#rpr_municipio').val("<?php echo $_GET['FILTRO_MUNICIPIO'] ?>");
								<?php 
								}
								?>
							});
						</script>
					<?php

				break;

				case 'profile.php':
					set_patterTelef("#your-profile");	

					$estado = get_user_meta( get_current_user_id(), 'rpr_estado', true); 
					$municipio = get_user_meta( get_current_user_id(), 'rpr_municipio', true); 

					?>
					<script type="text/javascript">
						jQuery(document).ready(function($)
	    				{
							$('#rpr_estado').val("<?php echo $estado ?>").trigger('change');
							$('#rpr_municipio').val("<?php echo $municipio ?>");
						});
					</script>
					<?php
				break;

				case 'edit-comments.php':
					agregarClass_CURRENT_enviadosRecibidos();
				break;

				case 'post.php':
					global $post;

					$actualizacion = true;	
					$estado = get_post_meta($post->ID, 'estado', true);
					$municipio = get_post_meta($post->ID, 'municipio', true);	

				//el break no se puso intencionalmente. !!WARNING!!

				case 'post-new.php':

					if(!$actualizacion)
					{
						$estado = get_user_meta( get_current_user_id(), 'rpr_estado', true); 
						$municipio = get_user_meta( get_current_user_id(), 'rpr_municipio', true); 
					}

					?>
						<script src="<?php bloginfo('template_url') ?>/js/estados-municipios.js"></script> 
						<script>
							jQuery(document).ready(function($)
							{	
								populateEstados("rpr_estado", "rpr_municipio");								
								$('#rpr_estado').val("<?php echo $estado ?>").trigger('change');
								$('#rpr_municipio').val("<?php echo $municipio ?>");
							});
						</script>
					<?php
				break;

			}	
		}// if super grande l:23
	} );
	///////////////////////////////////////////////////
	function agregar_bootstrap() 
	{
		?>
		<!-- Estilos de boostrap en la seccion administrativa de wordpress -->
		<link rel="stylesheet" href="<?php bloginfo('template_url') ?>/css/bootstrap.min.css"> 
		    <link rel="stylesheet" href="<?php bloginfo('template_url') ?>/css/bootstrap-theme.min.css">
		<?php
	}
	add_action( 'load-edit.php', 'agregar_bootstrap');
	//add_action( 'load-users.php', 'agregar_bootstrap');
	////////////////////////////////////////////////////
	function ModalAlFinalizarUnPost(){
		?>
		<!--Script para que salte el modal cuando se quiera finalizar un post-->
		<script>
			jQuery(document).ready(function($){
				$('.btn-finalizar').on('click',function()
				{						
					/*En el area administrativa, para finalizar post, no es posible obtener facilmente 
					el PostId y el postName (Necesarios para finalizar el post) entonces a través de js 
					se obtien gracias a que wordpress guarda estos datos en cada row de la tabla donde se
					muestran los post publicados de cada usuario.*/
					var postId = $(this).closest('tr').attr('id').split("-");
					$("#post-id").val(postId[1]);

					var postName = $(this).closest('div').prev().children(".post_title").text();
					$("#nombre-mascota").text('"'+postName+'"');

					//alert($("#post-id").val()+" and "+$("#nombre-mascota").text());
				});
			});
		</script>
		<?php
	}
	////////////////////////////////////////////////////
	function SweetalertGestionarPost(){
		//<!-- Script para que salte el sweetalert al gestionar post -->
		if(isset($_GET["mensaje"]))
		{	
			$the_post = get_post( $_POST["postID"] );
			$link = $the_post->guid;

			switch ($_GET["mensaje"]) 
			{
				case 'pendiente':
					$texto = 'Esta mascota está pendiente por revisión.';
				break;

				case 'aprobado':
					$texto = 'Esta mascota ha sido aprobada.';
				break;

				case 'publicado':
					$texto = 'Esta mascota ha sido publicada.';
				break;

				case 'editado':
					$texto = 'Esta mascota ha sido actualizada.';
				break;
			}
			?>
			<script type="text/javascript">
				window.onload = function() {
					swal({
			            title: '¡Perfecto!',
			            text: '<?php echo $texto; ?>',
			            type:  'success',
			            showCancelButton: true,
			            confirmButtonText: 'Ver',
			            cancelButtonText: 'Cerrar'
			        },
			        function() {

			        	window.open( '<?php echo $link; ?>' );					            
			        });
				};
			</script>
		    <?php				    
		}				
	}
	////////////////////////////////////////////////////
	function agregarClass_CURRENT_enviadosRecibidos(){
		if( $_GET['comment_status'] == 'enviados' )
		{
			?>
				<script>
					jQuery(document).ready(function($){
						$("li.all").children('a').removeClass("current");
						$('#com_enviados').addClass("current");
					});
				</script>
			<?php
		}
		else if( $_GET['comment_status'] == 'recibidos' )
		{
			?>
				<script>
					jQuery(document).ready(function($){
						$("li.all").children('a').removeClass("current");
						$('#com_recibidos').addClass("current");
					});
				</script>
			<?php
		}
	}
	////////////////////////////////////////////////////
	function agregarClass_CURRENT_post(){
		if( !al_isSuscriptorLogged() && isset($_GET['author']) )
		{
			?>
				<script>
					jQuery(document).ready(function($){
						$('#post_mine').addClass("current");
					});
				</script>			
			<?php
		}
	}
	////////////////////////////////////////////////////
	function set_patterTelef($IdForm)
	{
		?>
		<script src="<?php bloginfo('template_url') ?>/js/estados-municipios.js"></script> 
		<script>
			jQuery(document).ready(function($)
			{							
				$('<?php echo $IdForm; ?>').removeAttr('novalidate');
				$("#rpr_tel").attr("pattern",'^\\s*(?:\\+?(\\d{1,3}))?[-. (]*(\\d{3,4})[-. )]*(\\d{3})[-. ]*(\\d{2})[-. ]*(\\d{2})(?: *x(\\d+))?\\s*$');
				
				$("#rpr_tel").attr("required",'');
				$("#rpr_estado").attr("required",'');
				$("#rpr_municipio").attr("required",'');
				$("#rpr_direccin").attr("required",'');
				

				$('#rpr_tel').parent().append('<small><abbr title="Ejemplo">Ejm:</abbr> +58 (0426) 123.45.67</small>');
	
				populateEstados("rpr_estado", "rpr_municipio");
			});
		</script>
		<?php
	}
	////////////////////////////////////////////////
	add_action( 'get_footer', function(){

		if( $_GET["action"] == "register" )
			set_patterTelef('#registerform');
	});
?>