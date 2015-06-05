(function() {
	if((window == window.top) && (location.pathname != '/vclinic/index.php'))
		location.assign('http://'+location.hostname+'/vclinic/index.php');
})();