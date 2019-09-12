jQuery( document ).ready(function() {
	var progressItems = document.querySelectorAll(".LeaderBoard__item_progress");
	var N = 0;

	function doProgressInSequence() {
		if ( N >= progressItems.length ) {
			return;
		}
		progressItems[N].style.width = progressItems[N].getAttribute("data-w") + "%";
		N++;
		setTimeout(doProgressInSequence, 300);
	}

	doProgressInSequence();

});

