jQuery(function($){

  /*==============================================
  ua判別
==============================================*/
  if(!navigator.userAgent.match(/(iPhone|iPod|Android)/)){}

  /*==============================================
  外部リンクのセキュリティー対策
==============================================*/
  $('a[target="_blank"]').attr("rel","noopener noreferrer");

  /*==============================================
  selectを選択してページ遷移
==============================================*/

  $('.select-category').on('change', function () {
    var url = $(this).val();
    if (url != '') {
      location.href = url;
    }
  })

  /*==============================================
  page top
==============================================*/

  $('a[href^="#"]').click(function(){
    var speed = 500;
    var href= $(this).attr("href");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top;
    $("html, body").animate({scrollTop:position}, speed, "swing");
    return false;
  });


  if(navigator.userAgent.match(/(iPhone|iPod|Android)/)){
    var fixedMenu = $('.sp-fixed-menu');
    fixedMenu.hide();
    $(window).scroll(function () {
      if ($(this).scrollTop() > 100) {
        fixedMenu.fadeIn();
      } else {
        fixedMenu.fadeOut();
      }
    });
  }

  /*==============================================
  slick slider
==============================================*/


  //  $(".slider-gallery").slick({
  //    fade: false,
  //    centerMode: true,
  //    centerPadding: '0',
  //    slidesToShow: 3,
  //    slidesToScroll: 1,
  //    dots: true,
  //    arrows: true,
  //    responsive: [{
  //      breakpoint: 800,
  //      settings: {
  //        slidesToShow: 1,
  //        slidesToScroll: 1,
  //      }
  //    }]
  //  });


  /*==============================================
  Zero Menu
==============================================*/

  function isTouchDevice() {
    var result = false;
    if (window.ontouchstart === null) {
      result = true;
    }
    return result;
  }

  $(".humberger,.g-navi-close").click(function(){
    $('.humberger,.g-navi,.g-navi-dg').toggleClass('is-open');
  });

  //ドロップダウン
  $('.dropdown').hover(
    function() {
      //カーソルが重なった時
      $(this).addClass('is-on');
    }, function() {
      //カーソルが離れた時
      $(this).removeClass('is-on');
    }
  );

  if (isTouchDevice()) {
    $(".dropdown").click(function(e){
      $(this).toggleClass('is-on');
    })
  }

  /*==============================================
  FAQ accordion
==============================================*/
  $('.dl-faq dt').click(function(){
    $(this).toggleClass('is-open').next().slideToggle();
  });


});
