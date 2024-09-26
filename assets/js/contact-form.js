jQuery(document).ready(function ($) {
	console.log("Document ready");
	
	// Form validation
	$("#contactpage").validate({
		submitHandler: function (form) {
			console.log("Form validation passed");
			submitSignupFormNow($(form));
			return false;
		},
		rules: {
			name: {
				required: true
			},
			phone: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			message: {
				required: true
			}
		},
		errorElement: "span",
		errorPlacement: function (error, element) {
			error.appendTo(element.parent());
		}
	});

	// Form submission function
	function submitSignupFormNow(form) {
		console.log("Submitting form");
		var formData = form.serialize();
		console.log("Form data:", formData);
		
		$.ajax({
			url: "contact-form.php",
			type: "POST",
			data: formData,
			dataType: 'json',
			success: function (response) {
				console.log("AJAX success", response);
				if (response.status === "success") {
					$("#form_result").html('<span class="form-success">' + response.message + '</span>');
					form[0].reset();
				} else {
					$("#form_result").html('<span class="form-error">' + response.message + '</span>');
				}
				$("#form_result").show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log("AJAX error", textStatus, errorThrown);
				console.log("Response Text:", jqXHR.responseText);
				$("#form_result").html('<span class="form-error">An error occurred while sending your message. Please try again later.</span>');
				$("#form_result").show();
			}
		});
	}
});
