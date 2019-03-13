import "jquery";
import "../sass/main.scss";
import TweenMax from "gsap/TweenMax"; // imports TweenLite
import TimelineMax from "gsap/TimelineMax"; // imports TimelineLite
import scrollTo from "gsap/ScrollToPlugin";
export var fadeSpeed = 0.4


console.log("yeah!");

function toggleModal(id,open = true)
{
    if(open)
    {
        $(".modal#"+id).addClass("is-active");
        TweenMax.to("#"+id, fadeSpeed, {opacity: 1});
    }
    else
    {
        TweenMax.to($(".modal#"+id), fadeSpeed, {opacity: 0, onComplete: function(){
            TweenMax.delayedCall(0.5, function(){$(".modal#"+id).removeClass("is-active")});
            console.log("stop video")

			$(".modal#videomodal iframe").each(function() { 
			        var src= $(this).attr('src');
			        $(this).attr('src',src);  
			});

            
        }})
    }
}



$(document).ready(function() {

  // Check for click events on the navbar burger icon
  //toggleModal("videomodal");
  $(".navbar-burger").click(function() {

      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      $(".navbar-burger").toggleClass("is-active");
      $(".navbar-menu").toggleClass("is-active");

  });
  $("#playvideo").click(function(event ){
  	event.preventDefault();
  	toggleModal("videomodal");
  })

  $(".modal .close-button").click(function(event ){
        event.preventDefault();
        console.log("closing")
        let modal = $(this).data("modal");
        toggleModal(modal,false)
    })

  $(".button.opener").click(function(e){
    e.preventDefault()
    console.log("open")
    let modal = $(this).data("modal");
    toggleModal(modal)
  })
});
