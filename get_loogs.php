<?php
if (!isset($_GET['service'])) { exit("Service not specified"); }
$service = escapeshellcmd($_GET['service']);
$lines = isset($_GET['n']) ? intval($_GET['n']) : 30;
$cmd = "sudo /bin/journalctl -u $service --no-pager -n $lines 2>&1";
$logs = shell_exec($cmd);
echo nl2br(htmlspecialchars($logs));

