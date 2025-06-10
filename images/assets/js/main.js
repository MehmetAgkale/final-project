/* Main Js Start */

(function ($) {
  "use strict";

  $(document).ready(function () {

    // odometer init
    if ($(".odometer").length) {
      var odo = $(".odometer");
      odo.each(function () {
        $(this).appear(function () {
          var countNumber = $(this).attr("data-count");
          $(this).html(countNumber);
        });
      });
    }

    // sidebar dropdown
    $(".has-dropdown > a").click(function (e) {
      e.preventDefault();
      var $submenu = $(this).next(".sidebar-submenu");
      var $parent = $(this).parent();
      if ($submenu.css("display") === "block") {
        $submenu.slideUp(200);
        $parent.removeClass("active");
      } else {
        $(".sidebar-submenu").not($submenu).slideUp(200);
        $(".has-dropdown.active").removeClass("active");
        $parent.addClass("active");
        $submenu.slideDown(200);
      }
    });


    $(".dashboard-body__bar-icon").on("click", function () {
      $(".sidebar-menu").addClass('show-sidebar');
      $(".sidebar-overlay").addClass('show');
    });
    $(".sidebar-menu__close, .sidebar-overlay").on("click", function () {
      $(".sidebar-menu").removeClass('show-sidebar');
      $(".sidebar-overlay").removeClass('show');
    });

    $(".counterup-item").each(function () {
      $(this).isInViewport(function (status) {
        if (status === "entered") {
          for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
            var el = document.querySelectorAll('.odometer')[i];
            el.innerHTML = el.getAttribute("data-odometer-final");
          }
        }
      });
    });


    $(".add").on("click", function () {
      if ($(this).prev().val() < 999) {
        $(this)
          .prev()
          .val(+$(this).prev().val() + 1);
      }
    });
    $(".sub").on("click", function () {
      if ($(this).next().val() > 1) {
        if ($(this).next().val() > 1)
          $(this)
            .next()
            .val(+$(this).next().val() - 1);
      }
    });


    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
          $('#imagePreview').hide();
          $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
    $("#imageUpload").change(function () {
      readURL(this);
    });

  });

  // preloader
  $(window).on("load", function () {
    $("#loading").fadeOut();
  })


  // sticky header
  $(window).on('scroll', function () {
    if ($(window).scrollTop() >= 60) {
      $('.header,.header--two').addClass('fixed-header');
    }
    else {
      $('.header,.header--two').removeClass('fixed-header');
    }
  });


  var btn = $('.scroll-top');

  $(window).scroll(function () {
    if ($(window).scrollTop() > 300) {
      btn.addClass('show');
    } else {
      btn.removeClass('show');
    }
  });

  btn.on('click', function (e) {
    e.preventDefault();
    $('html, body').animate({ scrollTop: 0 }, '300');
  });


  $('.header-search-icon').on('click', function () {
    $('.header-search-hide-show').addClass('show');
    $('.header-search-icon').hide();
    $('.close-hide-show').addClass('show');
  });

  $('.close-hide-show').on('click', function () {
    $('.close-hide-show').removeClass('show');
    $('.header-search-hide-show').removeClass('show');
    $('.header-search-icon').show();
  });


  $('.sidebar-overlay, .close-hide-show').on('click', function () {
    $('.sidebar-menu-wrapper').removeClass('show');
    $(".sidebar-overlay").removeClass('show');
  });




  // tap to top with progress

  if ($('.scroll-top').length > 0) {
    var $scrollTopBtn = $('.scroll-top');
    var $progressPath = $('.scroll-top path');
    var pathLength = $progressPath[0].getTotalLength();

    $progressPath.css({
      transition: 'none',
      strokeDasharray: pathLength + ' ' + pathLength,
      strokeDashoffset: pathLength,
    });

    $progressPath[0].getBoundingClientRect();
    $progressPath.css({
      transition: 'stroke-dashoffset 10ms linear'
    });

    var updateProgress = function () {
      var scroll = $(window).scrollTop();
      var height = $(document).height() - $(window).height();
      var progress = pathLength - (scroll * pathLength / height);
      $progressPath.css('strokeDashoffset', progress);
    };

    updateProgress();

    $(window).on('scroll', updateProgress);

    $(window).on('scroll', function () {
      if ($(this).scrollTop() > 50) {
        $scrollTopBtn.addClass('show');
      } else {
        $scrollTopBtn.removeClass('show');
      }
    });

    $scrollTopBtn.on('click', function (event) {
      event.preventDefault();
      $('html, body').animate({ scrollTop: 0 }, 800);
      return false;
    });
  }


  // slick
  $('.testimonial-slider').slick({
    dots: false,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },

      {
        breakpoint: 780,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  $('.banner-slider').slick({
    dots: false,
    infinite: true,
    speed: 300,
    autoplay: true,
    fade: true,
    cssEase: 'linear',
    autoplaySpeed: 1000,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },

      {
        breakpoint: 780,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });


  // slick
  $('.special-monent--slider').slick({
    dots: false,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,

        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        }
      },

      {
        breakpoint: 780,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,

        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
        }
      }
    ]
  });

  $('.special-monent-slider--two').slick({
    dots: false,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          autoplay: true,
          autoplaySpeed: 1000,

        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          autoplay: true,
          autoplaySpeed: 1000,
        }
      },

      {
        breakpoint: 780,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          autoplay: true,
          autoplaySpeed: 1000,
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: false,
          autoplay: true,
          autoplaySpeed: 1000,
        }
      }
    ]
  });



  var dotImages = [
    'assets/images/user/user1.png',
    'assets/images/user/user2.png',
    'assets/images/user/user3.png',
    'assets/images/user/user8.png',
    'assets/images/user/user9.png',
  ];



  // slick
  $('.testimonial-slider--two').slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    initialSlide: 2,
    arrows: false,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    customPaging: function (slider, i) {
      if (i >= 0 && i < dotImages.length) {
        var imageSrc = dotImages[i];
        return '<img src="' + imageSrc + '" class="slick-dot-image" />';
      }
    },

    responsive: [
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        }
      },

      {
        breakpoint: 780,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });



  // toggle show hide password
  $(".toggle-password-change").click(function () {
    var targetId = $(this).data("target");
    var target = $("#" + targetId);
    var icon = $(this);
    if (target.attr("type") === "password") {
      target.attr("type", "text");
      icon.removeClass("fa-eye-slash");
      icon.addClass("fa-eye");
    } else {
      target.attr("type", "password");
      icon.removeClass("fa-eye");
      icon.addClass("fa-eye-slash");
    }
  });



  // wow init
  new WOW().init();

  // slick update function
  function handleSlideArrows(slick) {
    var slidesToShow = slick.options.slidesToShow;
    var slideCount = slick.slideCount;
    var currentSlide = slick.currentSlide || 0;

    if (currentSlide === 0) {
      $(slick.$slider).find('.slick-prev').hide();
    } else {
      $(slick.$slider).find('.slick-prev').show();
    }

    if (currentSlide + slidesToShow === slideCount) {
      $(slick.$slider).find('.slick-next').hide();
    } else {
      $(slick.$slider).find('.slick-next').show();
    }
  }

  // Initialize sliders
  $('.item-slider1').slick({
    dots: false,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    arrows: true,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1399,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 1199,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 2
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1
        }
      },
      {
        breakpoint: 430,
        settings: {
          slidesToShow: 1
        }
      },
      {
        breakpoint: 400,
        settings: {
          slidesToShow: 1
        }
      }
    ]

  }).on('init reInit afterChange', function (event, slick, currentSlide) {
    handleSlideArrows(slick);
  });


  $('.item-slider1').each(function () {
    var slickInstance = $(this).slick('getSlick');
    handleSlideArrows(slickInstance);
  });


  // category slider
  $('.submenu-slider').slick({
    slidesToShow: 6,
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 1000,
    pauseOnHover: true,
    variableWidth: true,
    speed: 2000,
    dots: false,
    arrows: false,
    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
    responsive: [
      {
        breakpoint: 1199,
        settings: {
          slidesToShow: 6,
        }
      },
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 5
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 4
        }
      },
      {
        breakpoint: 400,
        settings: {
          slidesToShow: 2
        }
      }
    ]
  });





  // tap to show balance
  $(document).on('click', '.textt, .balance', function () {
    $(this).toggleClass('d-none');
    $(this).siblings('.textt, .balance').toggleClass('d-none');
    var $tapBalance = $('.tap--balance');
    $tapBalance.toggleClass('flex-row-reverse');
    $tapBalance.addClass('transition-animation');
    setTimeout(function () {
      $tapBalance.removeClass('transition-animation');
    }, 300);
  });


  // for accordion
  $(document).ready(function () {

    function applyExpandedClassOnLoad() {
      $('#accordionFlushExample .accordion-item').each(function () {
        var $accordionItem = $(this);
        var $button = $accordionItem.find('.accordion-button');
        if ($button.attr('aria-expanded') === 'true') {
          $accordionItem.addClass('active');
        }
      });
    }

    applyExpandedClassOnLoad();

    $('#accordionFlushExample').on('shown.bs.collapse', function (e) {
      var $accordionItem = $(e.target).closest('.accordion-item');
      $accordionItem.addClass('active');
    });

    $('#accordionFlushExample').on('hidden.bs.collapse', function (e) {
      var $accordionItem = $(e.target).closest('.accordion-item');
      $accordionItem.removeClass('active');
    });
  });

  $(".caret").click(function () {
    $(this).toggleClass("caret-down");
    $(this).next(".nested").toggleClass("active");
  });

  Splitting();



  $(document).on('click', '.follow-this', function () {
    if ($(this).text().trim() === 'Follow') {
      $(this).text('Following');
      $(this).addClass('following');
    } else {
      $(this).text('Follow');
      $(this).removeClass('following');
    }
  });



  // photo upload
  $(document).ready(function () {
    var images = [];

    function selectFiles() {
      $("#fileInput").click();
    }

    function onFileSelect(event) {
      const files = event.target.files;

      if (files.length === 0) return;
      for (let i = 0; i < files.length; i++) {
        if (files[i].type.split('/')[0] !== 'image') continue;
        if (!images.some((e) => e.name == files[i].name)) {
          images.push({
            name: files[i].name,
            url: URL.createObjectURL(files[i])
          });
        }
      }
      updateImages();
    }

    function deleteImage(index) {
      images.splice(index, 1);
      updateImages();
    }

    function updateImages() {
      $("#containerArea").empty();
      images.forEach(function (image, index) {
        var deleteButton = $('<span class="delete"><i class="fa-solid fa-xmark"></i></span>');
        deleteButton.click(function () {
          deleteImage(index);
        });

        var imageDiv = $('<div class="image"></div>').append(deleteButton).append($('<img src="' + image.url + '" alt="..."/>'));
        $("#containerArea").append(imageDiv);
      });
    }

    function onDragOver(event) {
      event.preventDefault();
      $("#dragArea").addClass("isDragging");
      event.originalEvent.dataTransfer.dropEffect = "copy";
    }

    function onDragLeave(event) {
      event.preventDefault();
      $("#dragArea").removeClass("isDragging");
    }

    function onDrop(event) {
      event.preventDefault();
      $("#dragArea").removeClass("isDragging");
      const files = event.originalEvent.dataTransfer.files;
      for (let i = 0; i < files.length; i++) {
        if (files[i].type.split('/')[0] !== 'image') continue;
        if (!images.some((e) => e.name == files[i].name)) {
          images.push({
            name: files[i].name,
            url: URL.createObjectURL(files[i])
          });
        }
      }
      updateImages();
    }

    $("#selectFiles").click(selectFiles);
    $("#fileInput").change(onFileSelect);
    $("#dragArea").on("dragover", onDragOver).on("dragleave", onDragLeave).on("drop", onDrop);
  });




  $('.filter-btn--wrap .btn').click(function () {
    $('.filter--box').toggleClass('d-block');
    $('.explore-item--wrap').toggleClass('col-xxl-9 col-xl-9 col-lg-9');
    $('.explore-item--wrap .col-xxl-3, .explore-item--wrap .col-lg-4').toggleClass('col-xxl-4 col-lg-4');
  });


  $(document).ready(function () {

    const colors = [
      'hsl(164, 60%, 90%)',
      'hsl(281, 100%, 95%)',
      'hsl(203, 100%, 91%)',
      'hsl(221, 100%, 91%)',
    ];

    $('.category--card,.feature--card').each(function () {
      const randomColorIndex = Math.floor(Math.random() * colors.length);
      const randomColor = colors[randomColorIndex];
      $(this).css('background', randomColor);
    });

  });



  $(document).ready(function () {
    var basePrice = parseFloat($('#mainPrice').text().replace('$', ''));
    function updatePrice() {
      var additionalPrice = 0;
      if ($('#flexRadioDefault20').is(':checked')) {
        additionalPrice = parseFloat($('#flexRadioDefault20').val());
      }
      var newPrice = basePrice + additionalPrice;
      $('#mainPrice').text('$' + newPrice);
    }
    $('.extend-licence--input').change(function () {
      updatePrice();
    });
    updatePrice();
  });



  $(document).ready(function () {
    function updateTotalPrice(container) {
      var priceInput = container.find('.form--control.price');
      var buyerFeeElement = container.find('.buyer--fee');
      var totalPriceElement = container.find('.total--price');

      var price = parseFloat(priceInput.val());

      if (isNaN(price)) {
        price = 0;
      }

      var buyerFee = parseFloat(buyerFeeElement.text().replace('$', ''));

      var totalPrice = price + buyerFee;

      totalPriceElement.text('$' + totalPrice.toFixed(2));
    }

    $('.price-option--wrap').each(function () {
      var container = $(this);

      container.find('.form--control.price').on('input', function () {
        updateTotalPrice(container);
      });

      updateTotalPrice(container);

    });
  });




  $(document).ready(function () {
    let lastScrollTop = 0;
    const element = $('.header-two--wrap');

    $(window).scroll(function () {
      const scrollTop = $(window).scrollTop();

      if (scrollTop > lastScrollTop) {

        element.addClass('scrolled');
        element.removeClass('animate')
      } else {
        element.removeClass('scrolled');
        element.addClass('animate')
      }

      lastScrollTop = scrollTop;
    });
  });




  $(document).ready(function () {
    let second = 1000;
    let minute = second * 60;
    let hour = minute * 60;
    let day = hour * 24;


    let countDown = new Date('jun 30, 2024 00:00:00').getTime();

    function myRacing() {
      let nowDate = new Date().getTime();
      let distance = countDown - nowDate;

      $('#days').text(Math.floor(distance / day));
      $('#hours').text(Math.floor((distance % day) / hour));
      $('#minutes').text(Math.floor((distance % hour) / minute));
      $('#seconds').text(Math.floor((distance % minute) / second));

      // When the countdown is over
      if (distance < 0) {
        clearInterval(MyTimer);
        $('#timeToStart').text('The camp began ☻');
        $('#timeControl').empty();
      }
    }

    let MyTimer = setInterval(myRacing, 1000);
  });


  $('.topheader-close--btn').click(function () {
    $('.offer--coutdown').addClass('d-none');
  });



  function checkScreenSize() {
    if ($(window).width() > 530) {
      $('.search--form').removeClass('search-form--active');
      $('.logo-wrapper').removeClass('d-none');
    }
  }


  checkScreenSize();


  $(window).resize(function () {
    checkScreenSize();
  });




  $('.popup_video').magnificPopup({
    type: 'iframe',
  });


  $(document).ready(function () {
    $('#additionalInfo').on('input', function () {
      var maxLength = $(this).attr('maxlength');
      var currentLength = $(this).val().length;
      $('#charCount').text(currentLength + '/' + maxLength);
    });
  });


})(jQuery);



