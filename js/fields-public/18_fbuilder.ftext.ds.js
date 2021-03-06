	$.fbuilder.controls[ 'ftextds' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'ftextds' ].prototype,
		$.fbuilder.controls[ 'ftext' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"ftextds",
			show:function()
				{
					return $.fbuilder.controls[ 'ftext' ].prototype.show.call( this );
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