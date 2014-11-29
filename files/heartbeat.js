function sendHeartbeat() {
	$.ajax("heartbeat.php");
}

heartbeat=setInterval(sendHeartbeat, 4000);