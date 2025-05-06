window.onload = function (pEvent) {
	var links = document.getElementsByTagName("a");
	for (var i = 0; links.length > i; i++) {
		if (links[i].className == "literature") {
			links[i].target = "_tab";
		}
	}
};
