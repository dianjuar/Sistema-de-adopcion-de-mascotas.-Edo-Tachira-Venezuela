<?php
////////////////////////////////////////////////////////////
	//Filtra el listado de post depende del rol logeado.
	add_filter('the_posts', function ($posts)
	{		
	    global $user_ID;
	    global $pagenow;

	    if( !al_isProgrammerLogged() && $pagenow == "edit.php" ) 
	    {
	        foreach($posts as $i => $post)
	        {
	        	switch (  $post->post_status ) 
				{
					case 'publish':
						if( al_isSuscriptorLogged() || al_isModeradorLogged() )
	    					if( $post->post_author != $user_ID)
                				unset($posts[$i]);

	    				break;

	    			case 'pending':

	    				if( al_isSuscriptorLogged() )
	    					if( $post->post_author != $user_ID)
                				unset($posts[$i]);

	    				break;

	    			case 'trash':

	    				if( $post->post_author != $user_ID)
                			unset($posts[$i]);

	    				break;

	    			case 'archive':	    

	    				if(al_isModeradorLogged() || al_isSuscriptorLogged() )
	    					if( $post->post_author != $user_ID)
                				unset($posts[$i]);

	    				break;

	    			default:
	    				# Nothing
	    				break;
	    		}

	        }
	    }

		return $posts;
	});

	//*************************************************************************************
	//ADD AUTHOR COLUMN TO THE LIST OF POSTS
	function add_author_column( $columns ) {
		
	    $columns["categoria"] = "Categoría";
	    $columns["estado"] = "Estado";
	    $columns["municipio"] = "Municipio";
	    $columns["autor"] = "Autor"; //if I set the the "author" instead "autor" I cann't modify its content
	    unset($columns["description"]); //it works
	    return $columns;
	}
	add_filter('manage_edit-post_columns', 'add_author_column'); //add the author to the columns names array
	add_filter('manage_edit-post_sortable_columns', 'add_author_column'); //add the author to the columns names sortable array

	//MODIFY CONTENT IN AUTHOR COLUMN (SET DISPLAY NAME)
	function set_display_name_autor_column( $column) {

		global $post;

		$user = get_user_by( 'id', $post->post_author );
		
	    switch ( $column ) 
	    {
	    	case 'autor':
	        	echo $user->display_name;
	        break;

	        case 'categoria': //mostar las categorias que pertenece un post

	        	$cats = array();
				foreach(wp_get_post_categories($post->ID) as $c)
				{
					$cat = get_category($c);
					array_push($cats,$cat->name);
				}

				if(sizeOf($cats)>0)				
					$post_categories = implode(',',$cats);
				else				  
					$post_categories = "SIN ASIGNAR";

				echo $post_categories;
	        break;
	    }
	}
	add_action( 'manage_posts_custom_column' , 'set_display_name_autor_column' );
	//*************************************************************************************
	// Remueve los tabs que no son necesarios.
	function eliminar_tab_todos($views) 
	{
		if(!al_isProgrammerLogged())
		{	
		  unset($views['draft']);	
		
			if( al_isSuscriptorLogged() )	
			{
				unset($views['mine']);
			}  				
		}

	 	return $views;
	}

	add_filter('views_edit-post', 'eliminar_tab_todos');	

	//pone por defecto el tab todos en el usuario suscriptor
	add_action( 'load-edit.php', function() 
	{
		if ( al_isSuscriptorLogged() && strpos( $_SERVER[ 'REQUEST_URI' ], 'post_type' ) === false )
		{
		    wp_redirect( admin_url('edit.php?post_type=post&all_posts=1') ); 
		    exit;				
		} 

	});

	//*************************************************************************************
	//añade el tab 'mio' en edit.php al moderador, administrador y superadministrador
	function add_tab_mine( $views ) 
	{
		global $current_user;
	    $views['mine'] = '<a id="post_mine" href="'.admin_url().'edit.php?post_type=post&author='.$current_user->id.'">Mío <span class="count"></span> </a>';

	    return $views;
	}

	if( !current_user_can('al_suscriptor') )
		add_action( 'views_edit-post', 'add_tab_mine' );
	//*************************************************************************************
	//Agregar un filtro al listado de post por estado --ON--
	add_action( 'restrict_manage_posts', 'wpse45436_admin_posts_filter_restrict_manage_posts' );
	/**
	 * First create the dropdown
	 * make sure to change POST_TYPE to the name of your custom post type
	 * 
	 * @author Ohad Raz
	 * 
	 * @return void
	 */
	function wpse45436_admin_posts_filter_restrict_manage_posts(){
	        ?>
		        <select id="rpr_estado" name="FILTRO_ESTADO">
		        </select>
		        <select id="rpr_municipio" name="FILTRO_MUNICIPIO">
		        </select>
	        <?php
	}

	add_filter( 'parse_query', 'wpse45436_posts_filter' );
	/**
	 * if submitted filter by post meta
	 * 
	 * make sure to change META_KEY to the actual meta key
	 * and POST_TYPE to the name of your custom post type
	 * @author Ohad Raz
	 * @param  (wp_query object) $query
	 * 
	 * @return Void
	 */
	function wpse45436_posts_filter( $query )
	{
	    global $pagenow;
	    $type = 'post';
	    if (isset($_GET['post_type'])) 
	        $type = $_GET['post_type'];
	    

	    if ( $pagenow=='edit.php' && isset($_GET['FILTRO_ESTADO']) && $_GET['FILTRO_ESTADO'] != '-1') 
	    {
	        $query->query_vars['meta_key'] = 'estado';
	        $query->query_vars['meta_value'] = $_GET['FILTRO_ESTADO'];

	        $query->query_vars['meta_key'] = 'municipio';
	        $query->query_vars['meta_value'] = $_GET['FILTRO_MUNICIPIO'];
	    }
	}
	//Agregar un filtro al listado de post por estado --OFF--
	//*************************************************************************************

?>
