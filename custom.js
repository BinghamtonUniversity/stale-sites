function updateMainContent() {
	$("#status-indicator").text("Fetching your data...");
	$.get("getStaleSytes.php",null,function(data) {
		$("#status-indicator").text("Below are your stale-sites.");
		$("#main-text").html(data);
	},"html");
	
}

$(document).ready(function() {
	$("#update-button").click(function() {
		$("#status-indicator").text("Updating base path...");
		$.post("updateBasePath.php",
			{url: $("#base-dir").val()},
			function(data) {
				if(data == "success") {
					updateMainContent();
				}
				else {
					$("#status-indicator").text(data);
				}
			});
		
	})
});