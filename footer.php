<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					footer.php                                               **|
|**		Creation:					February 20, 2012                                        **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Description:				Footer of the website                                    **|
\**********************************************************************************************/
################################################################################################
 
 ?>			</div>
		</div> 
	
		<footer>
			<div class="main" style="text-align:center;" >
				It's Now BABY &copy; 2012 |
				<a title="Mentions légales" class="dialogue" href="ml.php" > Mentions légales</a> | 
				<?php if($public){ ?>
				<a onclick='connect(true);' > Admin </a>
				<?php } else { ?> 
				<a onclick='connect(false);' > Disconnect </a>
				<?php } ?> 
			</div>
		</footer>
	
		<div id='connexion'></div>
		<script type="text/javascript" charset="utf8" >
			function connect(bool){
				
				//we oppen a prompt window using jquery ui dialog method
				$('#connexion').css("display", 'inline-block');//activate the bloc
				$('#connexion').html("<center><h3>Entrer your logins:</h3><br><br><br>Login<br><input type='text' id='login' /><br><br><br>Password<br><input type='password' id='pass' /><br><br></center>");//update the content
				$( "#dialog:ui-dialog" ).dialog( "destroy" );//destroy other ui-dialog windows
				if(bool){
					$( "#connexion" ).dialog({resizable: false,width: 320,modal: true,show: 'slide',hide: 'clip',	
						buttons: {
							"Connexion": function() {
								var action = ajax_open("inc/open_session.php?login="+htmlspecialchars($('#login').val())+"&pass="+htmlspecialchars($('#pass').val()));
								if (action>0) { // if the answer is positive, it'is ok
									$( "#connexion" ).dialog({resizable: false,width: 310,modal: true,show: 'slide',hide: 'clip',	
										buttons: {// set ui-dialog prefenrences
											"Ok": function() {
												document.location.replace("index.php");
											}
										}
									});
									$('#connexion').html("<center>Your are logged in. </center>");
								}
								else {// the answer is negative, a problem occured
									$( "#connexion" ).dialog({resizable: false,width: 310,modal: true,show: 'slide',hide: 'clip',	buttons: {
										"Ok": function() {
											$( this ).dialog( 'close' );
										}
									}});
									$('#connexion').html("<center>(Error "+action+") Your aren't logged in. Maybe your logins are wrong!</center>");
								}
							}
						}
					});	
				}
				else{
					var action = ajax_open("inc/open_session.php?out&login&pass");
					if (action>0) { // if the answer is positive, it'is ok
						$( "#connexion" ).dialog({resizable: false,width: 310,modal: true,show: 'slide',hide: 'clip',	
							buttons: {// set ui-dialog prefenrences
								"Ok": function() {
									document.location.replace("index.php");
								}
							}
						});
						$('#connexion').html("<center>Your are logged out. </center>");
					}
					else {// the answer is negative, a problem occured
						$( "#connexion" ).dialog({resizable: false,width: 310,modal: true,show: 'slide',hide: 'clip',	buttons: {
							"Ok": function() {
								$( this ).dialog( 'close' );
							}
						}});
						$('#connexion').html("<center>(Error "+action+") A problem occured while logging out.");
					}
				}
				$( "#connexion" ).dialog('open');//open the ui-dialog window
			}
			
			/* UI Dialog begin */
	
			 $(document).ready(function() {
			
				//script pour afficher en mode dialog(jquery ui-customs)
				$('.dialogue').each(function()
				{
					var $link = $(this);


					$link.click(function() {
						 var $dialog = $('<div style="font-size:0.9em;"></div>')
						.load($link.attr('href'))
						.dialog({
							autoOpen: false,
							title: $link.attr('title'),
							width: 800,
							height:610,
							modal:true,
							show: 'slide',
							hide: 'clip',
							position: 'center'
						});
						$dialog.dialog('open');

						return false;
					});
				});
			
			});
			/* UI Dialog end */
		</script>
	</body>
</html>