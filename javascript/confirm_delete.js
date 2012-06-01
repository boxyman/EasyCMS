$(document).ready(function() {
	$("a.delete-page").click(function() {
		var name = $(this).val();
		return confirm("Slet " + name + "?");
	});
});