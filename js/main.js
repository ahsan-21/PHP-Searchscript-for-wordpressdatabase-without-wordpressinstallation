/*
* Main Script
*/
;(function($) {

	function sendAjaxRequest(searchValue) {
		$(".loader").show();
		$.ajax({
			url: "ajax.php",
			data: {
				search: searchValue
			},
			success: function(result){
				$(".loader").hide();
				$("#suggestionListData").html(result);
			},
			error: function(error) {
				console.log(error);
			}
		});
	}
	$(document).ready(function() {

		$("#form1").submit(function(e) {
			e.preventDefault();
			sendAjaxRequest($('#searchInput1').val());
		});

		document.getElementById('searchInput1').addEventListener("awesomplete-select", function(event) {
			sendAjaxRequest(event.text.value);
		});
	});

})(jQuery);
