window.onload = function (pEvent) {
	document.getElementById("reset").onclick = function (pEvent) {
		var elements = document.forms[0].elements;
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].type == "text") {
				elements[i].value = "";
			}
		}
	};
	document.getElementById("pdf").disabled = false;
	document.getElementById("pdf").onclick = function () {
		document.forms[0].target = this.id == "pdf" && this.checked
			? "_tab"
			: "";
	};
	document.getElementById("html").onclick = document.getElementById("pdf").onclick;
	document.getElementById("pdf").onclick();
};
