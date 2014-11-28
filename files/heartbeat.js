function sendHeartbeat() {
	$.ajax("heartbeat.php");
}

heartbeat=setInterval(sendHeartbeat, 2000);