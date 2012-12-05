function updateContent() {
	$("#status-indicator").text("Fetching your data...");
	$.get("getStaleSites.php",null,function(data) {
		$("#status-indicator").text("Data loaded").removeClass().addClass("alert alert-block");
		$("#main-text").html(data);
	},"html");
	$.get("getIgnoredSites.php",null,function(data) {
		$("#ignoredSites").html(data);
	},"html");
}

$(document).ready(function() {
	$("#update-button").click(function() {
		$("#status-indicator").text("Updating base path...").removeClass().addClass("alert alert-block");
		$.post("updateBasePath.php",
			{url: $("#base-dir").val()},
			function(data) {
				if(data == "success") {
					updateContent();
				}
				else {
					$("#status-indicator").text(data).removeClass().addClass("alert alert-block alert-error");
				}
		});
	});
	$("#exclude-button").click(function() {
		$("#status-indicator").text("Updating exclude path/s...").removeClass().addClass("alert alert-block");
		$.post("addExcludeUrls.php",
			{url: $("#exclude-dir").val()},
			function(data) {
				if(data == "success") {
					updateContent();
				}
				else {
					$("#status-indicator").text(data).removeClass().addClass("alert alert-block alert-error");
				}
		});
	});
});