(function ($, Drupal) {
  Drupal.behaviors.myModuleBehavior = {
    attach: function (context, settings) {
		
		
		 $(document).ready(function () {
			 $('.page-node-type-performance-listing #edit-actions--2 input.form-submit').val('Pay Now');
			  $('.path-detail-view #block-bartikcustomgary-content .form-submit, .path-teacher-detail-view #block-bartikcustomgary-content .form-submit, .view-display-id-page_4 .form-submit, .view-display-id-page_5 .form-submit').val('Yes - feature my listing');
			 // $('.uc-product-add-to-cart-form #edit-actions input.form-submit').val('Pay Now');			 
			 
			 $('.page-node-type-ad-type .form-actions input').val('Proceed to Payment');
			 $('.view-id-user_content .views-field-buy-it-now .form-actions input').val('Pay Now');
			 
			var fullDate = new Date();		
			var seconds = fullDate.getSeconds();	
			var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1); 
			var currentDate = fullDate.getDate()+twoDigitMonth+fullDate.getFullYear()+seconds;
			//console.log(currentDate);
			
			 $('input#edit-model-0-value').val(currentDate);
			 //$('input#edit-price-0-value').val(price);
			 $( ".path-cart .uc-cart-view-form .qty" ).text($(".path-cart .uc-cart-view-form .qty" ).text().replace("Quantity", "Month"));

			// var banner;
			// var square
		 $('input:radio[name=field_select_adtype]').change(function() {
				if (this.value == 'Banner AD') {
					 var banner = $(".node-own-ad-type input#edit-field-banner-0-value").val();
					 $('.node-own-ad-type input#edit-price-0-value').val(banner);
				}
				else if (this.value == 'Square Tile AD') {
					var square = $(".node-own-ad-type input#edit-field-square-ad-price-0-value").val();
					 $('.node-own-ad-type input#edit-price-0-value').val(square);
				}
			}); 
			
			
			if($(".step2 input#edit-field-banner-0-value").val()!=""){
				var banners = $(".node-own-ad-type.step2 input#edit-field-banner-0-value").val();
					$('.node-own-ad-type.step2 input#edit-price-0-value').val(banners);					
			}else{
				var squares = $(".node-own-ad-type.step2 input#edit-field-square-ad-price-0-value").val();
				$('.node-own-ad-type.step2 input#edit-price-0-value').val(squares);	
			}
			
			jQuery(".path-detail-view .views-field-buy-it-now .form-submit, .path-teacher-detail-view .views-field-buy-it-now .form-submit, .path-event-detail-view .views-field-buy-it-now .form-submit").on("click", function (){
				if ($('#PayCheck').is(":checked")){
					return true;
				}else{
					 $('#massag').fadeIn('slow', function(){
					   $('#massag').delay(2000).fadeOut(); 
					});
					return false;					
				}
				
			});
			
			
			$(".path-user .view-id-user_content tr td.views-field-field-approved").each(function(){	
				var stat = $(this).text();
				var tr = $.trim(stat);					
				if(tr == 'Yes'){
					$(this).parent().addClass("paidClass");
				} 
			}); 			
			$(".paidClass .views-field-buy-it-now form").replaceWith( "paid" );
		 });
		 
		function deleteAllCookies() {	
			var date = new Date();
			var minutes = 1;
			date.setTime(date.getTime() + (minutes * 60 * 1000));
			
			var cookies = document.cookie.split(";");
			for (var i = 0; i < cookies.length; i++) {
				var cookie = cookies[i];
				var eqPos = cookie.indexOf("=");
				var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
				document.cookie = name + "=;expires="+date;
				console.log(document.cookie);
			}		
		}
    // alert('asdasd');
    }
  };
})(jQuery, Drupal);

 jQuery(document).ready(function () {	
	jQuery("#block-test h2").on("click", function (e){
		jQuery('#block-test ul').slideToggle();	
	});
		
	jQuery("#block-exposedformsearchpage-1 h2").on("click", function (e){	
		jQuery('#views-exposed-form-search-page-1').slideToggle();	
	});

	//jQuery( "#ui-id-1" ).replaceWith( "<span id='ui-id-1' class='ui-dialog-title'>Subscribe to Choir Central</span>" ); 
	jQuery(".field--widget-daterange-datelist .clearfix").addClass("add-date-button");
	jQuery('.page-node-type-product input#edit-submit-508').val('JOIN');
	jQuery('.user-register-form input.form-submit').val('NEXT');
	
	jQuery('.menu--footer .menu-item--expanded ul').css('display', 'none');
	jQuery('.menu-item--expanded').on("click",function(){    
		jQuery(this).find("ul.menu").toggle();
		jQuery(this).siblings().find("ul.menu").hide();
	});
	
	/*  if(jQuery('#edit-field-add-date-2-value').is(":checked")){
		alert('test');
		jQuery("#field-date-2").css('display', 'block');  
	}  */
	jQuery("body").delegate(".all_sessions .field--name-field-add-date-2", "click", function(){
		jQuery(".step1 .all_sessions #field-date-2").css('display', 'block');  
		jQuery(".step1 .all_sessions .field--name-field-add-date-2").css('display', 'none');  
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-datee-3-value", "click", function(){
		jQuery(".step1 .all_sessions #field-date-3").css('display', 'block'); 
		jQuery(".step1 .all_sessions .field--name-field-add-datee-3").css('display', 'none');  		
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-4-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-4").css('display', 'block'); 
	jQuery(".step1 .all_sessions .field--name-field-add-date-4").css('display', 'none');  			
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-5-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-5").css('display', 'block'); 
jQuery(".step1 .all_sessions .field--name-field-add-date-5").css('display', 'none'); 		
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-6-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-6").css('display', 'block'); 
jQuery(".step1 .all_sessions .field--name-field-add-date-6").css('display', 'none'); 		
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-7-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-7").css('display', 'block');  
		jQuery(".step1 .all_sessions .field--name-field-add-date-7").css('display', 'none'); 
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-8-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-8").css('display', 'block');
jQuery(".step1 .all_sessions .field--name-field-add-date-8").css('display', 'none'); 		
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-9-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-9").css('display', 'block');  
		jQuery(".step1 .all_sessions .field--name-field-add-date-9").css('display', 'none'); 
	});
	jQuery("body").delegate(".all_sessions #edit-field-add-date-10-value", "click", function(){
		jQuery(".step1 .all_sessions #edit-group-date-10").css('display', 'block');  
		jQuery(".step1 .all_sessions .field--name-field-add-date-10").css('display', 'none'); 
	});
	
	var $urls = window.location.href.search("pass-reset-token");
  if($urls>=0 ){
	  jQuery("#block-bartikcustomgary-content").addClass("passwordEdit");
	  jQuery(".passwordEdit .custom-text").css('display', 'block !important');
	  jQuery(".existuser, #block-bartikcustomgary-local-tasks").css('display', 'none');
  }
  
	jQuery( "#uc-cart-view-form tr input" ).each(function() {
		var imput = jQuery("#uc-cart-view-form tr input").val();
		if(imput == "Remove"){
			jQuery(this).parent("td").addClass( "td-remove" );
		}
	});
	
});
	jQuery(document).ready(function(){
  jQuery("#block-useraccountmenu-2-menu").click(function(){
    
    var target = jQuery(this).parent().children(".content");
    jQuery(target).slideToggle();
  });
});
			
jQuery(".extra_review_button").css('display', 'none');
jQuery(".node-form #edit-preview").css('display', 'none');
jQuery( document ).ajaxComplete(function() {
 
  jQuery(".field--widget-daterange-datelist .clearfix").addClass("add-date-button");
  jQuery('.user-register-form input.form-submit').val('NEXT');
  //alert(htm);
  jQuery(".node-workshop-list-sstype.step2 #edit-submit").css('display', 'none');
  jQuery(".node-choir-form.step2 #edit-submit").css('display', 'none');
  jQuery(".node-event-form.step2 #edit-submit").css('display', 'none');
  jQuery(".node-teacher-form.step2 #edit-submit").css('display', 'none');
  jQuery(".node-tours-form.step2 #edit-submit").css('display', 'none');  
  jQuery(".extra_review_button").css('display', 'block');
  
  var width = jQuery(".field--widget-image-widget-crop .image-data__crop-wrapper details").attr("data-drupal-iwc-original-width");	
		var height = jQuery(".field--widget-image-widget-crop .image-data__crop-wrapper details").attr("data-drupal-iwc-original-height");

	//if(width != '680' && height != '375'){  
		jQuery("body").delegate(".extra_review_button", "click", function(){
			var htm = jQuery(".vertical-tabs__menu-item-summary").html();	
			var html2 = jQuery(".image-data__crop-wrapper summary").html();	
				if(htm == 'Cropping applied.' || htm == 'Cropping applied.<br>Soft limit reached.' || html2 == 'Crop image (cropping applied)'){
						jQuery( ".node-form.step2 #edit-submit" ).trigger( "click" );
					return true;
				}else{
					alert('Image Cropping is Required');
				return false;
				} 
			}); 
	//}
	/* else{
		jQuery(".image-data__crop-wrapper").remove();
		jQuery('.field--widget-image-widget-crop strong').removeClass('js-form-required form-required');
		jQuery(".field--widget-image-widget-crop .image-data__crop-wrapper details").attr("data-drupal-iwc-required", "0");
		jQuery(".vertical-tabs__menu-item-summary").html("Cropping applied.");
		return true;
	} */
});
 