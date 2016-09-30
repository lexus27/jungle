/**
 * Created by Alexey on 23.08.2016.
 */


(function(){


	$(document).ready(function() {


		function scrollToElement(element){
			var el = $( element );
			var elOffset = el.offset().top;
			var elHeight = el.height();
			var windowHeight = $(window).height();
			var offset;

			if (elHeight < windowHeight) {
				offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
			}
			else {
				offset = elOffset;
			}
			window.scrollTo(0, offset);
			return false;
		}


		function checkToTopButton(){
			var scrollMin = 500;
			var button = document.getElementById('to-top-button');
			if(window.scrollY > scrollMin){
				button.style.display = 'block';
			}else{
				button.style.display = 'none';
			}
		}

		var scrollMin = 500;
		var button = document.getElementById('to-top-button');
		if(window.scrollY > scrollMin){
			button.style.display = 'block';
		}else{
			button.style.display = 'none';
		}
		button.addEventListener('click',function(){
			var intervalId = setInterval(function(){
				var to = window.scrollY - 130;
				if(to < 0){
					clearInterval(intervalId);
					intervalId = null;
					window.scrollTo(0,0);
				}else{
					window.scrollTo(0,to - 10);
				}
			},10);
		});
		var scrolled = false;
		window.addEventListener('scroll',function() {
			checkToTopButton();
		});






		var cancel = false;

		var standbyTop = false;
		var standbyTopCount = 0;

		var standbyBottom = false;
		var standbyBottomCount = 0;



		function scrollHorizontal(element, delta,amount, e){
			var scrollLeft = element.scrollLeft;
			element.scrollLeft -= (delta * amount);

			var timeout = null;
			if(scrollLeft === element.scrollLeft){

				if(cancel && e){
					if(!timeout)timeout = setTimeout(function(){
						cancel = false;
						standbyTop = false;standbyTopCount = 0;
						standbyBottom = false;standbyBottomCount = 0;
					},600);
					return;
				}

				if(delta < 0){
					standbyTop = false;standbyTopCount = 0;
					if(standbyBottom){
						if(standbyBottomCount >=6){
							cancel = true;
							return;
						}else{
							standbyBottomCount++;
						}
					}else{
						standbyBottom = true;
					}
					if(e)e.preventDefault();


				}

				if(delta > 0){
					standbyBottom = false;standbyBottomCount = 0;
					if(standbyTop){
						if(standbyTopCount >=6){
							cancel = true;
							return;
						}else{
							standbyTopCount++;
						}
					}else{
						standbyTop = true;
					}
					if(e)e.preventDefault();
				}
			}else{
				if(e){
					e.preventDefault();
					scrollToElement(element);
				}
			}





		}

		var intervalId = null, autoScroll = true;
		$('.scroll-horizontal').on('mouseover',function(){
			if(!intervalId && autoScroll!==false){
				var that = this;
				intervalId = setInterval(function(){
					if(scrollHorizontal(that,-1,4)===false){
						clearInterval(intervalId);intervalId = null;
					}
				},10);
			}
		}).mousewheel(function(e, delta) {
			if(intervalId){
				clearInterval(intervalId);
			}
			autoScroll = false;
			intervalId = null;
			scrollHorizontal(this,delta,50,e);
		});
	});


})();






/**
* Slider Code
*/
(function(){

	function initSlider(img, clickable, paths, delay){

		var current_image_index = paths.length>1?randomInteger(0 , paths.length - 1):0;

		delay = delay || 15000;
		var timeoutId = null;
		var nextImageFn = function(){
			current_image_index++;
			if(paths.length <= current_image_index){
				current_image_index = 0;
			}
			img.src = paths[current_image_index];
		};
		var triggerSlideImageFn = function(e){
			if(e && e.toElement !== clickable){
				return;
			}
			nextImageFn.call();
			if(timeoutId){
				clearTimeout(timeoutId);
			}
			timeoutId = setTimeout(triggerSlideImageFn, delay);

		};

		img.src = paths[current_image_index];
		clickable.addEventListener('click',triggerSlideImageFn,false);
		setTimeout(triggerSlideImageFn,delay);
	}
	var paths;



	var sliders = document.getElementsByClassName('headslider');
	if(sliders){
		for(var i=0;i<sliders.length;i++){
			var slider = sliders[i];
			paths = slider.getAttribute('data-images');
			if(paths){
				paths = paths.join(',');
			}else{
				paths = [
					'/assets/images/slider-img-3.png',
					'/assets/images/autobiz.jpg',
					'/assets/images/slider-img-5.png',
					'/assets/images/aircraft.jpg',
					'/assets/images/slider-img-6.png',
					'/assets/images/avtomatizaciya-marketinga.jpg',
					'/assets/images/city.jpg',
					'/assets/images/slider-img-2.png',
					'/assets/images/internet-tech.png',
					'/assets/images/waterplan.jpg',
					'/assets/images/slider-img-1.jpg',
					'/assets/images/city-real.jpg',
					'/assets/images/slider-img-4.png',
					'/assets/images/market-team.jpg',
					'/assets/images/skyhouse.jpg',
					'/assets/images/slider-img-7.png'
				];
			}
			var images = slider.getElementsByClassName('headslider-image');
			if(images){
				initSlider(images[0],slider.getElementsByClassName('headslider-frame')[0], paths, 15000);
			}

		}
	}
})();


function randomInteger(min, max) {
	var rand = min + Math.random() * (max - min);
	rand = Math.round(rand);
	return rand;
}