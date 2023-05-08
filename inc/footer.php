        <div class="footer text-center">
            <?php if ( isset ( $_SESSION['id'] ) ): ?><div><i style="font-size: 0.7rem;"><?= ltrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/'); ?></i></div><?php endif; ?>
            <div><small><i class="pl pl-heart"></i> Desenvolvido por <a href="https://instagram.com/ashelabs" target="_blank"><strong>@ashelabs</strong>.</small></a></div>
            <img src="<?= BASEURL; ?>assets/img/Logo.png" height="35px">
        </div>

        <div id="modal">
            <div id="modal-content">
                <a class="icon wi-close close"><span class="label">Fechar</span></a>
                <div id="modal-ajax"></div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="<?= BASEURL; ?>patients/scripts/jquery.mask.js"></script>
        <script src="<?= BASEURL; ?>patients/scripts/simple-lightbox.min.js"></script>

        <script>
// const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
// if (isMobile) {
//    let bbody = document.querySelector('body');
//    bbody.style.display = 'none';
//}

window.oncontextmenu = function(event) {
    const isMobile = navigator.userAgentData.mobile; //resolves true/false
    if ( isMobile) {
        event.preventDefault();
        event.stopPropagation();
        return false;
    }
};

            $('body').on('click', '.trigger', function(e) {
                e.preventDefault();
                var action = this.href;

                $.ajax({
                    url: action,
                    success: function(data) {
                        $('#modal').addClass('md-show');
                        $('#modal-ajax').html(data);
                        console.log('Input element:', $('#myInput'));
                        $('#myInput').focus();
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            });
        </script>

        <script>
            if ( $('#imgInp').length ) {
                imgInp.onchange = evt => {
                    const [file] = imgInp.files
                    if (file) {
                        blah.src = URL.createObjectURL(file)
                    }
                }
            }

            $('body').on('submit', '#upload', function(e) {
                e.preventDefault();
                var action = $(this).attr('action'), 
                    reaction = $(this).data('reaction');

				$.ajax({
					url: action, // <-- point to server-side PHP script 
					dataType: 'text',  // <-- what to expect back from the PHP script, if anything
					cache: false,
					contentType: false,
					processData: false,
					data: new FormData(this),                         
					type: 'post',
					success: function(data) {
						console.log(data);
						$('.profile-pic').removeClass('load');
						if ( reaction ) {
						    console.log('reloading');
						    // location.reload();
						}
					}
				});
            });

            $('body').on('change', '#imgInp', function() {
                console.log('Enviando imagem...');
                $('#upload').submit();
            });

			$('body').on('change', '#imgInp', function () {
                $('.profile-pic').addClass('load');
			});
        </script>

        <script type="text/javascript">
        	var lightbox = new SimpleLightbox('.gallery a', { /* options */ });
        	var zoom_pic = new SimpleLightbox('.profile-pic a', { /* options */ });
        </script>

        <script>
            var SPMaskBehavior = function (val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            }, spOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };
            
            $('input[type="tel"]').mask(SPMaskBehavior, spOptions);

        	$('input.cpf').mask('000.000.000-00', {reverse: true});

            $('input.cep').mask('00000-000', {placeholder: "_____-___"});

            $('input[type=text]').on('input', function() {
                if ( $(this).hasClass('uid') ) return false;

                $(this).val (function () {
                    return this.value.toUpperCase();
                });
            });

        	$(document).ready(function() {
        		/* 1. Visualizing things on Hover - See next part for action on click */
        		$('#stars li').on('mouseover', function() {
        			var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
        
        			// Now highlight all the stars that's not after the current hovered star
        			$(this).parent().children('li.star').each(function(e){
        				if (e < onStar) {
        					$(this).addClass('hover');
        				}
        				else {
        					$(this).removeClass('hover');
        				}
        			});
        		}).on('mouseout', function(){
        			$(this).parent().children('li.star').each(function(e){
        				$(this).removeClass('hover');
        			});
        		});
        	  
        	  
        		/* 2. Action to perform on click */
        		$('#stars li').on('click', function() {
        			var onStar = parseInt($(this).data('value'), 10), // The star currently selected
        			stars = $(this).parent().children('li.star');
        
        			for (i = 0; i < stars.length; i++) {
        				$(stars[i]).removeClass('selected');
        			}
        
        			for (i = 0; i < onStar; i++) {
        				$(stars[i]).addClass('selected');
        			}
        
        			// JUST RESPONSE (Not needed)
        			var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
        			$('#rating').val(ratingValue);
        		});
        
        		/* 3. Action to perform on load */
        		var onStar = $('#rating').val(), // The star currently selected
        			stars = $('#stars').children('li.star');
        
        		for (i = 0; i < stars.length; i++) {
        			$(stars[i]).removeClass('selected');
        		}
        
        		for (i = 0; i < onStar; i++) {
        			$(stars[i]).addClass('selected');
        		}
        	});
        </script>

        <script>
            $('body').on('click', '.startover', function(e) {
				e.preventDefault();

				var form = $(this).closest('form'), 
				    action = form.attr('action');

				// Disable buttons
				$(this).find('button').prop('disabled', true);

				// Send the data using post
				var serialize = form.serializeArray(), 
				    posting = $.post( action, serialize );
				    console.log(action);

				// Get results
				posting.done(function( data ) {
            	    $('.justadded').html('Novos procedimentos adicionados, atualize.');
            	    form[0].reset();
            	    form.find('select').slice(1).remove();
				});

				console.log ( 'Script end' );
            });

            $('body').on('click', '.drop', function(e) {
                e.preventDefault();

                if (! confirm ( 'Continuar?' ) )
                    return false;

                var action = this.href;
        
                // Send the data using post
                var getting = $.get( action );
                
                // Get results
                getting.done(function() {
                	location.reload();
                });
            });

            $('body').on('click', 'a.print', function(e) {
                e.preventDefault();

                var type = $(this).data('type');

                if ( $(this).data('pid') ) {
                    pid = $(this).data('pid');
                    printId(pid, type);
                } else {
                    form = $(this).data('form');
                    printForm(form, 'closure');
                }
            });

            function printId(pid, type = 'income') {
				var action = '<?= BASEURL; ?>patients/print.php';

				// Send the data using post
				var pid = pid, 
				    posting = $.post( action, { id: pid, type: type } );

				// Get results
				posting.done(function( data ) {
					var w = window.open('about:blank', 'Imprimir');
					w.document.write(data);
				});

				console.log ( 'Print request done.' );
            }

            function printForm(form, type) {
				var action = '<?= BASEURL; ?>closure/print.php';

				// Send the data using post
				var form = $(form).serializeArray(), 
				    posting = $.post( action, form );
				    console.log(action);

				// Get results
				posting.done(function( data ) {
					var w = window.open('about:blank', 'Imprimir');
					w.document.write(data);
				});

				console.log ( 'Print request done.' );
            }
        </script>

        <script>
			$('body').on('click', '.group > .header .caret', function(e) {
				e.preventDefault();
				$group = $(this).parents('.group');

				$('.group').not($group).removeClass('active');;

                $group.toggleClass('active');
			});
        </script>

        <script>
			$('body').on('click', 'label[for="mobile"]', function(e) {
				e.preventDefault();
				$group = $('.mobile_toggle');

                $group.slideToggle(200);
			});
        </script>

        <script>
            $.fn.slideFadeToggle = function(easing, timing, callback) {
                return this.animate({ opacity: 'toggle', height: 'toggle' }, timing , easing, callback);
            };

            $('body').on('click', '.expand', function(e) {
               e.preventDefault();

                $('.tohide').slideFadeToggle('linear', 200);
            });

            $('body').on('click', 'label[for="chat"]', function(e) {
                if (! $('#cha').hasClass('shown') ) {
                    $('#cha').load('/chat.php');
                } else {
                    $('#cha').html('<label for="chat" class="call icon wi-chat"><span class="label">Ajuda</span></label>');
                }

                $('#cha').toggleClass('shown');
            });
        </script>

		<script>
            if ('serviceWorker' in navigator) {
              navigator.serviceWorker
                .register('<?= BASEURL; ?>serviceWorker.js')
                .then(serviceWorker => {
                  // console.log('Service Worker registered: ' + serviceWorker);
                })
                .catch(error => {
                  // console.log('Error registering the Service Worker: ' + error);
                });
            }
		</script>

        <div id="cha">
            <label for="chat" class="call icon wi-chat"><span class="label">Ajuda</span></label>
        </div>

        <style>
            #cha {
                z-indeX: 9999;
                position: fixed;
                bottom: 1rem; right: 1rem;
            }

            @media (max-width: 736px) {
                #cha {
                    bottom: 6rem;
                }
            }

                #cha .call {
                    display: block;
                    width: 3.25rem;
                    height: 3.25rem;
                    line-height: 3.25rem;
                    font-size: 1.25rem;
                    text-align: center;
                    /* background-color: #0074D9; */
                    background-color: #0074D9;
                    color: white;
                    border-radius: 50%;
                    opacity: .5;
                }

                #cha:hover .call {
                    opacity: 1;
                }

            #cha.shown { bottom: 0; right: 0; }
        </style>
	</body>
</html>