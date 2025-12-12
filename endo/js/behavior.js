jQuery(function ($) {
  /*==============================================
  user default
==============================================*/

  //開閉
  var $sc_top;
  $(".humberger , .sp-close , .navihumberger").click(function () {
    if(this.getAttribute("aria-expanded") == "true"){
      // 閉じる
      $(this).removeClass("is-open");
      $sc_top = parseInt(document.body.style.top);
      $('body').removeClass("fixed");
      $(".sp-navi").removeClass("is-open");
      $(".pc-navi").fadeToggle();
      $(this).attr('aria-expanded','false');
      setTimeout(function () {
        scrollTo(0, $sc_top * -1);
      }, 1);
     } else {
       // 開く
       $sc_top = window.scrollY;
       $(this).addClass("is-open");
       $(".sp-navi").addClass("is-open");
       $(".pc-navi").fadeToggle();
       $(this).attr('aria-expanded','true');
       setTimeout(function () {
         $('body').addClass("fixed");
         document.body.style.top = "" + $sc_top * -1 + "px";
       }, 1);
     }
  });
  $(".g-navi_close").click(function () {
    $(this).toggleClass("is-open");
    $(".g-navi").toggleClass("is-open");
  });
  //ドロップダウンメニュー
  $(".pc-navi-haschild").hover(function () {
    $(this).children(".pc-navi_child").stop().fadeIn('middle');
    $(this).children(".pc-navi_child").css("left", ($(this).innerWidth() - $(this).children(".pc-navi_child").innerWidth()) / 2);
  }, function () {
    $(this).children(".pc-navi_child").stop().fadeOut();
  });
  // ハンバーガー内メニュー開閉
  $(".sp-navi-haschild").click(function () {
    $(this).toggleClass("is-open");
    $(this).children('.sp-navi_child').slideToggle();
  });

  //アンカーリンクを押したとき(aタグのhref属性の値に「#」が含まれている場合)
  $(".pc-navi_content a").click(function() {
      // 閉じる
      $(".humberger , .sp-close , .navihumberger").removeClass("is-open");
      $('body').removeClass("fixed");
      $(".pc-navi").fadeToggle();
      $(".humberger , .sp-close , .navihumberger").attr('aria-expanded','false');
  });

  // hrefの無害化
  $href_sanitize = $('a[target="_blank"]');
  $href_sanitize.attr("rel", "noopener noreferrer");

  // スクロール発火
  var scroll = $(window).scrollTop();
  if (scroll > 1043 && window.innerWidth > 800) {
    $('.header-frame').addClass('fixed');
  } else {
    $('.header-frame').removeClass('fixed');
  }
  $(window).scroll(function () {
    var scroll = $(window).scrollTop();
    var elemMain = $('main').offset().top;
    var elemNavi = $('.footer').offset().top - (window.innerHeight / 2) - 163;
    if (scroll > 1043 && window.innerWidth > 800) {
      $('.header-frame').addClass('fixed');
    } else {
      $('.header-frame').removeClass('fixed');
    }
    if (scroll >= elemNavi && window.innerWidth > 800) {
      $('#fixnavi_pc').addClass('stop');
    } else {
      $('#fixnavi_pc').removeClass('stop');
    }
    if (scroll >= elemMain && window.innerWidth > 800) {
      $('#pagetop').fadeIn();
    } else {
      $('#pagetop').stop().fadeOut();
    }
  });

  /*==============================================
  slider
==============================================*/
  if ($("#hero_slider").length) {
    var hero_slider = new Splide( '#hero_slider', {
//      type: 'loop',
//      speed: 2000,
//      interval: 7000,
//      rewind: true,
//      arrows: false,
//      pagination: false,
//      perPage: 1,
      type   : 'loop',
      drag: false,
//      drag: 'free',
      autoWidth: true,
      arrows: false,
      pagination: false,
      autoScroll: {
        pauseOnHover: false,
        pauseOnFocus: false,
        speed: -0.5,
      },
      gap : '50px',
      breakpoints : {
        800: {
          gap : '21px',
        },
      },

    } );
    hero_slider.mount( window.splide.Extensions );
  }

//  if ($('#t_carousel').length) {
//    var elms = document.getElementById( 't_carousel' );
//    new Splide( elms ,{
//      type   : 'loop',
//      arrows : false,
//      autoWidth: true,
//      pagination : false,
//      drag : false,
//      autoScroll: {
//        speed: .5,
//        pauseOnFocus : false,
//        pauseOnHover : false,
//      },
//    }).mount(window.splide.Extensions);
//    for ( var i = 0; i < elms.length; i++ ) {
//    }
//  }

  if ($('.t_slider').length) {
    var elms = document.getElementsByClassName( 't_slider' );
    for ( var i = 0; i < elms.length; i++ ) {
      new Splide( elms[ i ] ,{
        rewind : true,
        type   : 'fade',
        speed: 3000,
        autoplay: true,
        arrows : false,
        pagination : true,
        perPage : 1,
        perMove: 1,
        breakpoints : {
          800: {
            autoWidth: false,
          },
        },
//        autoScroll: {
//          speed: .5,
//          pauseOnFocus : false,
//          pauseOnHover : false,
//        },
//      }).mount(window.splide.Extensions);
      }).mount();
    }
  }

  if ($('.u_slider').length) {
    var elms = document.getElementsByClassName( 'u_slider' );
    for ( var i = 0; i < elms.length; i++ ) {
      new Splide( elms[ i ] ,{
        type : 'loop',
        pagination : true,
        autoplay: false,
        arrows : true,
        rewind : true,
        gap : '2em',
      }).mount();
    }
  }

  if ($("#u_slider_gallery").length) {
    var main = new Splide( '#u_slider_gallery', {
      type       : 'fade',
      pagination : false,
      arrows     : false,
      rewind : true,
//      cover      : true,
    } );

    var thumbnails = new Splide( '#u_slider_thumb', {
//      type            : 'loop',
      perPage: 5,
//      fixedWidth      : 100,
//      fixedHeight     : 70,
      isNavigation    : true,
      gap             : 10,
//      focus           : 'center',
      pagination      : false,
//      cover           : true,
      breakpoints : {
        640: {
        },
      },
    } );
    main.sync( thumbnails );
    main.mount();
    thumbnails.mount();

//    var main = new Splide( '#u_slider_gallery', {
//      type       : 'fade',
////      cover      : true,
//      autoplay   : true,
//      arrows     : false,
//      pagination : false,
//    } );
//    // リスト要素をクラス名ですべて取得
//    var thumbnails = $("#u_slider_thumb li");
//    var current;
//    for ( var i = 0; i < thumbnails.length; i++ ) {
//      initThumbnail( thumbnails[ i ], i );
//    }
//    // それぞれのリスト要素を初期化するための関数
//    function initThumbnail( thumbnail, index ) {
//      thumbnail.addEventListener( 'click', function () {
//        main.go( index );
//      } );
//    }
//    main.on( 'mounted move', function () {
//      if ( current ) {
//        current.classList.remove( 'is-active' );
//      }
//      // Splide#indexは現在アクティブなスライドのインデックスを返す
//      var thumbnail = thumbnails[ main.index ];
//      if ( thumbnail ) {
//        thumbnail.classList.add( 'is-active' );
//        current = thumbnail;
//      }
//    } );
//    main.mount();
  }

  /*==============================================
  selectを選択してページ遷移
==============================================*/
  $('.select-category').on('change', function () {
    var url = $(this).val();
    if (url != '') {
      location.href = url;
    }
  });

  /*==============================================
  独自仕様
  ==============================================*/
  // fancybox
//  if (window.innerWidth > 800) {
//    Fancybox.unbind("[data-fancybox]", {});
//  }

  // faq
//  $(".u-faq dt").click(function () {
//    $(this).toggleClass("is-open").next().slideToggle();
//  });
  $(".denture-case .l-ttl").click(function () {
    $(this).toggleClass("is-open").next().slideToggle();
  });
  $(".t-post .tabs li").click(function () {
    if(!$(this).hasClass('active')){
      $(this).parent().parent().children(".inpost").children(".content").hide(); $(this).parent().parent().children(".inpost").children(".content").eq($(this).index()).fadeIn('slow');
    }
    $(this).parent().parent().children(".tabs").children("li").removeClass('active');
    $(this).addClass('active');
  });
  $(".tabchange .tabs li").click(function () {
    if(!$(this).hasClass('active')){
      console.log($(this).parent().parent().children(".target").children(".single"));
      $(this).parent().parent().children(".target").children(".single").hide(); $(this).parent().parent().children(".target").children(".single").eq($(this).index()).fadeIn('slow');
    }
    $(this).parent().parent().children(".tabs").children("li").removeClass('active');
    $(this).addClass('active');
  });
//  $(".accordion .target").click(function () {
//    $(this).toggleClass("is-open").next().slideToggle();
//  });

  const controller = new ScrollMagic.Controller();
  $(".is-target , .fade-in, .fade-in-list").each(function(i, node) {
      var scene = new ScrollMagic.Scene({
              triggerElement: node,
              triggerHook: "onEnter",
              reverse: false,
              offset: 200,
          })
          .setClassToggle(node, "is-active")
          .addTo(controller);
  });

});
