function md_slider_function(width, maxWidth, maxHeight, height, transitionSpeed, timer, current, bullets, auto, slides){
jQuery.noConflict();
jQuery(document).ready(function md_responsive_slider_functions($) {
					
				// set slider width and height
				$('#md-slider, #md-slider ul li').css({
					'width' : width+"px",
					'height' : height+"px",					// set the slider height
					'max-width' : maxWidth+"px",			// set maximum slider width
					'max-height' : maxHeight+"px"			// set maximum slider height
				});	
				$('#md-slider-container').css({
					'max-width' : maxWidth+"px"
				});

				/* --- add styles --- */
								
					//$('#md-slider ul li').css({ 'width' : $(document).width() }); 				// set the slide width
					for(i=0;i<slides;i++){ $('#navigation').prepend('<li>bullet</li>');	} 	// generate navigation bullets
					$("#navigation").css('width', slides * 25); 							// set bullet navigation width
					$("#navigation li").eq(0).addClass('current'); 							// highlight current slide bullet
						
					$('#next').css({																
						'margin-left' : $('#md-slider ul li').width() - $('#next').width(),			// float to the right of the slider
						'margin-top' : ($('#md-slider ul li').height()/2) - ($('#next').height()/2)	// position the right arrow in the middle of the slider
					});
					$('#prev').css({																
						'margin-top' : ($('#md-slider ul li').height()/2) - ($('#next').height()/2)	// position the left arrow in the middle of the slider
					});	
					$('#next, #prev').hide();														// hide navigation arrows
					$('#md-slider').mouseenter( function(){ $('#next, #prev').fadeIn(400);				// fadeIn navigation arrows on mouseenter
					}).mouseleave( function(){ $('#next, #prev').fadeOut(600); });					// fadeOut navigation arrows on mouseleave
				
				/* --- end styles --- */
															
				/* --- on window resize --- */	
					
					$(window).resize(function(){
						var width = $("#md-slider-container").width();														// update the slider width
						var height = width * maxHeight / maxWidth;											
						var updatePosition = width * -1 * (current-1);											// update the slider position

						$('#md-content').animate( {'left' : updatePosition+'px'}, 10, 'swing');					// reset the slide position
							
							$('#md-slider, #md-slider li').css({
								'width' : width+"px",
								'height' : height+"px",															// set the slider height
								'max-width' : maxWidth+"px",													// set maximum slider width
								'max-height' : maxHeight+"px"													// set maximum slider height
							});
							$('#md-slider li a img').css({
								'width' : '100%'
							});
							$('#md-slider li').css({ 'width' : width+"px" }); 										// set the slide width
							$('#next').css({
								'margin-left' : $('#md-slider ul li').width() - $('#next').width(),				// float to the right 
								'margin-top' : (height/2) - ($('#next').height()/2)								// center the navigation arrows
							});
							$('#prev').css({
								'margin-top' : (height/2) - ($('#next').height()/2)								// center the navigation arrows
							});		
					});
					
				/* --- end window resize --- */	
				
				if(auto){ autoSlide = setTimeout(slideNext, timer);	}			// if checked auto slide
				
				function autoSlideshow(){
				if(auto){clearTimeout(autoSlide);								// clearTimeout
						autoSlide = setTimeout(slideNext, timer); }				// setTimeout
				}
		
				function titleAndInfo(){
					$('#md-slider h1').stop().animate({'margin-top': '-150px'}, 20).eq(current-1).animate({'margin-top' : '4%'}, 1000, 'swing');  // title
					$('#md-slider p').animate({'margin-top': '-250px'}, 20).eq(current-1).animate({'margin-top' : '10%'}, 500, 'swing');		   // description
				}
			
				titleAndInfo(); // animate the title and the info
				
				// Next
				function slideNext(){	
					autoSlideshow();																		// clearTimeout and setTimeout
					var width = $('#md-slider-container').width();														// set the slider width
					if(current > slides-1){
						current = 1;																		// the current slide is the first one
						$('#md-content').stop().animate( {'left' : '0px'}, transitionSpeed, 'swing');					// animate to the first slide
						titleAndInfo(); 																	// animate the title and the info
					}else{
						current +=1;																		// increment current slide number
						$('#md-content').stop().animate( {'left' : '-='+width+'px'}, transitionSpeed, 'swing');	
						$('#md-content li a img').attr({
							'width' : width,
							'height' : height
						});
						titleAndInfo(); 																	// animate the title and the info
					}
				$("#navigation li").removeClass('current').eq(current-1).addClass('current'); 				// highlight current slider bullet
				}
				
				// Prev
				function slidePrev(){
					autoSlideshow();																		// clearTimeout and setTimeout
					var width = $('#md-slider-container').width();														// set the slider width
					var updatePosition = width * -1 * (slides-1);											// update the slide position
					if(current == 1){
						current = slides;																	// the current slide is the last one
						$('#md-content').stop().animate( {'left' : updatePosition+'px'}, transitionSpeed, 'swing');	// animate to the last slide
						titleAndInfo(); 																	// animate the title and the info
					}else{
						current -=1;																		// decrement current slide number
						$('#md-content').stop().animate( {'left' : '+='+width+'px'}, transitionSpeed, 'swing');		// animate to the previous slide
						titleAndInfo(); 																	// animate the title and the info
					}	
				$("#navigation li").removeClass('current').eq(current-1).addClass('current'); 				// highlight current slider bullet
				}				
				
				/* --- arrow navigation --- */			
				
					$("#prev").click( function(){ slidePrev(); }); 											// on left arrow click show previous slide			
					$("#next").click( function(){ slideNext(); }); 											// on right arrow click show next slide
				 
				/* --- end arrow navigation --- */
				
				/* --- keyboard navigation --- */
				
					$(document.documentElement).keyup(function (event) {
					  	if (event.keyCode == 37) { slidePrev();												// animate to the previous slide
					 	 } else if (event.keyCode == 39) { slideNext();	}									// animate to the next slide
					});
				
				/* --- end keyboard navigation --- */			
								
				/* --- bullet navigation --- */
				if(bullets){
					$('#navigation li').click( function(){
							autoSlideshow();																	// clearTimeout and setTimeout
							var width = $('#md-slider-container').width();										// set the slider width
							var getPosition = $(this).index()+1;												// get the current position
							var updatePosition = width * -1 * (getPosition-1);									// update slide position
							$('#md-content').stop().animate( {'left' : updatePosition+'px'}, transitionSpeed, 'swing');	// animate to the slide clicked
							current = getPosition;																// set the current slide number
							$("#navigation li").removeClass('current').eq(current-1).addClass('current');		// highlight current slider bullet
							titleAndInfo(); 																	// animate the title and the info
					});
				}else{
					$('#navigation').fadeOut(200);	
				}
				/* --- end bullet navigation --- */

 });   
}

