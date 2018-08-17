	$.fbuilder.controls[ 'femailds' ]=function(){};
	$.extend(
		$.fbuilder.controls[ 'femailds' ].prototype,
		$.fbuilder.controls[ 'femail' ].prototype,
		$.fbuilder.controls[ 'datasource' ].prototype,
		{
			ftype:"femailds",
			show:function()
				{
					return $.fbuilder.controls[ 'femail' ].prototype.show.call( this );
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