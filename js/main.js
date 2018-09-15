/*
* Main Script
*/
;(function($) {

	$(document).ready(function() {
		console.log("Working");

		$("#form1").submit(function(e) {
			e.preventDefault();
			$(".loader").show();
			$.ajax({
				url: "ajax.php",
				data: {
					search: $('#searchInput1').val()
				},
				success: function(result){
					$(".loader").hide();
					$("#suggestionListData").html(result);
				},
				error: function(error) {
					console.log(error);
				}
			});
		});
	});

})(jQuery);
