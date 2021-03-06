	$.fbuilder.controls[ 'ftextareads' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'ftextareads' ].prototype,
		$.fbuilder.controls[ 'ftextarea' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"ftextareads",
			show:function()
				{
					return $.fbuilder.controls[ 'ftextarea' ].prototype.show.call( this );
				},
			after_show : function(){
				var me = this;
				$.fbuilder.controls[ 'datasource' ].prototype.getData.call( this, function( data )
					{ 
						if( typeof data.error != 'undefined' )
						{
							alert( data.error );
						}
						else
						{
							if( data.data.length )
							{
								$( '#' + me.name ).val( data.data[ 0 ][ 'value' ] ).change();
							}
						}	
					}
				);
			}	
	});