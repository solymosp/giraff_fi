// fix email addresses in link
function fix(a) {
	a.href=a.href.replace(/I-HATE-SPAM/g, ""); return true;
}

// show/hide correct email address in status bar 
function stat(a, b) {
	if (b) {
		window.status=a.href.replace(/I-HATE-SPAM/g, "");
	}
	else {
		window.status="";
	}
}