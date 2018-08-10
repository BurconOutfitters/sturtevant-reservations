	$.fbuilder.controls[ 'datasource' ] = function(){};
	$.fbuilder.controls[ 'datasource' ].prototype = {
		isDataSource:true,
		active : '',
		list : {
			'database' : {
				databaseData: '',
				queryData : {
					active: 'structure',
					query: '',
					value: '',
					text: '',
					table: '',
					where: '',
					orderby: ''
				},
				getData : function( callback, p ) 
					{
						var url = document.location.href,
							data = $.extend( {}, this.queryData );
						
						if( data.active == 'structure' )
						{
							data.where = p.replaceVariables( data.where );
						}
						else
						{
							data.query = p.replaceVariables( data.query );
						}

						$.ajax(
							{
								dataType : 'json',
								url : url,
								cache : false,
								data : $.extend( { cffaction: 'get_data_from_database' }, this.databaseData, data ),
								success : function( data ){
									callback( data );	
								}
							}
						);
					}
			},
			'csv' : {
				csvData : {
					text : 0,
					value : 0,
					fields : [],
					rows : []
				},
				getData : function( callback )
					{
						var obj = {
								data : []
							};
						
						for( var i in this.csvData.rows )
						{
							var text = this.csvData.text,
								value = this.csvData.value;
								
							if( typeof this.csvData.rows[ i ].length == 'undefined' )
							{
								text  = this.csvData.fields[ text ];
								value = this.csvData.fields[ value ];
							}
							
							obj.data.push( { text: this.csvData.rows[ i ][ text ], value : this.csvData.rows[ i ][ value ] } );
						}	
						callback( obj );
					}
			},
			'posttype' : {
				posttypeData:{
					posttype : '',
					value 	 : '',
					text 	 : '',
					last	 : '',
					id 		 : ''
				},
				getData : function( callback, p )
					{
						var url = document.location.href,
							data = $.extend( {}, this.posttypeData );
						
						if( !/^\s*$/.test( data.id ) )
						{
							data.id = p.replaceVariables( data.id );
						}
						
						$.ajax(
							{
								dataType : 'json',
								url : url,
								cache : false,
								data : $.extend( { cffaction: 'get_posts' }, data ),
								success : function( data ){
									callback( data );	
								}
							}
						);
						
					}
			},
			'taxonomy' : {
				taxonomyData:{
					taxonomy : '',
					value 	 : '',
					text 	 : '',
					id 		 : '',
					slug 	 : ''
				},
				getData : function( callback, p )
					{
						var url = document.location.href,
							data = $.extend( {}, this.taxonomyData );
						
						if( !/^\s*$/.test( data.id ) )
						{
							data.id = p.replaceVariables( data.id );
						}
						
						if( !/^\s*$/.test( data.slug ) )
						{
							data.slug = p.replaceVariables( data.slug );
						}
						
						$.ajax(
							{
								dataType : 'json',
								url : url,
								cache : false,
								data : $.extend( { cffaction: 'get_taxonomies' }, data ),
								success : function( data ){
									callback( data );	
								}
							}
						);
					}
			},
			'user' : {
				userData : {
					logged  : false,
					text 	: '',
					value 	: '',
					id 		: '',
					login 	: ''
				},
				getData : function( callback, p )
					{
						var url = document.location.href,
							data = $.extend( {}, this.userData );
						
						if( !data.logged )
						{
							if( !/^\s*$/.test( data.id ) )
							{
								data.id = p.replaceVariables( data.id );
							}
							
							if( !/^\s*$/.test( data.login ) )
							{
								data.login = p.replaceVariables( data.login );
							}
						}

						$.ajax(
							{
								dataType : 'json',
								url : url,
								cache : false,
								data : $.extend( { cffaction: 'get_users' }, data ),
								success : function( data ){
									callback( data );	
								}
							}
						);
					}
			}	
		},
		getData : function( callback )
			{
				this.list[ this.active ].getData( callback, this );
			},
		replaceVariables : function( str )
			{
				var _match,
					tmpArr = []; // To avoid duplicate handles
					
				while ( _match = /<[%](fieldname\d+)[%]>/.exec( str ) )
				{
					var me 	= this,
						e   = _match[ 1 ]+this.form_identifier,
						f 	= $.fbuilder[ 'forms' ][ this.form_identifier ].getItem( e );
					if( ( typeof this.hasBeenPutRelationHandles == 'undefined' || !this.hasBeenPutRelationHandles ) && $.inArray( _match[ 1 ], tmpArr ) == -1 )	
					{
						
						$( document ).on( 	'change', 
											'#'+e, 
											function()
												{
													me.after_show(); 
												} 
										);
						tmpArr.push( _match[ 1 ] );				
					}

					if( f )
					{
						str = str.replace( _match[ 0 ], f.val() );
					}
				}
				
				this.hasBeenPutRelationHandles = true;
				return str;
			}
	};