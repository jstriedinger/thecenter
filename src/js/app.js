import "jquery";
import "../sass/main.scss";
import TweenMax from "gsap/TweenMax"; // imports TweenLite
import TimelineMax from "gsap/TimelineMax"; // imports TimelineLite
import scrollTo from "gsap/ScrollToPlugin";
import YouTubePlayer from 'youtube-player';
export var fadeSpeed = 0.4;
var player;
var is_mobile = false;
var is_desktop = false;
var is_wide = false;

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
            player.stopVideo()
        }})
    }
}

document.addEventListener( 'wpcf7mailsent', function( event ) {
  console.log(event.detail.contactFormId)
  if(event.detail.contactFormId == 94 || event.detail.contactFormId == 179)
  {
    toggleModal("formmodal",false)
    toggleModal("thanksmodal")
  }
  else{
    setTimeout(function () {
        //5sec withour gtm reidrection, redirect ourselves
        console.log("forced redirect")
        console.log(window.location+"gracias/")
        window.location =  window.location+"gracias/";
    }, 2000);
  }
  
}, false );

$(document).ready(function() {

  if(document.getElementById("video-player"))
  {
    if( $(window).width() <= 768 )
    {
      is_mobile = true
      player = YouTubePlayer('video-player', {
          videoId: 'jX1V1Vy4UbM',
          width: 640,
          height: 300
      });
    }
    else if($(window).width() <= 1400)
    {
      is_desktop = true
      player = YouTubePlayer('video-player', {
          videoId: 'jX1V1Vy4UbM',
          width: 700,
          height: 380
      });
    }
    else if($(window).width() <= 1800)
    {
      is_wide = true
      player = YouTubePlayer('video-player', {
          videoId: 'jX1V1Vy4UbM',
          width: 800,
          height: 450
      });
    }

    // 'loadVideoById' is queued until the player is ready to receive API calls.
    //player.loadVideoById('jX1V1Vy4UbM');
    // 'playVideo' is queue until the player is ready to received API calls and after 'loadVideoById' has been called.
    player.playVideo().then(function () {
              console.log('Starting to play player1. It will take some time to buffer video before it starts playing.');
          });;
    player.on('stateChange', (event) => {
        if(event.data == 0)
        {
          toggleModal("videomodal",false)
        }
    });

  }

  $(".tabs li:not(.last)").click(function(e){
    let tabid = $(this).data("tab")
    console.log(tabid)
    console.log("here")
    $(".content-tab").removeClass("is-active")
    $(".content-tab#"+tabid).addClass("is-active")
    $(".tabs ul li").removeClass("is-active")
    $(this).addClass("is-active")
    console.log("bla")

  })
   $(".wpcf7-form").submit(function(){
        //Always disable button and change to is-loading
        var $btn = $(this).find(".button[type='submit']");
        $btn.addClass("is-loading")
        //Keep going
        return true;
    })
  // Check for click events on the navbar burger icon
  toggleModal("videomodal");
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
    let modal = $(this).data("modal");
    console.log("open")
    toggleModal(modal)
  })   
  
});
