<?php
// actions.php
if (!isset($_GET['service']) || !isset($_GET['action'])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$service = escapeshellcmd($_GET['service']);
$action  = escapeshellcmd($_GET['action']);

// allowed actions whitelist
$allowed = ['start','stop','restart','reload','enable','disable','status'];
if (!in_array($action, $allowed)) {
    http_response_code(400);
    echo "Action not allowed";
    exit;
}

$cmd = "sudo /bin/systemctl $action $service 2>&1";
$output = shell_exec($cmd);

// log action with timestamp
$log = date('c') . " - $action $service\n" . strip_tags($output) . "\n\n";
file_put_contents('/var/log/service-monitor/actions.log', $log, FILE_APPEND | LOCK_EX);

echo nl2br(htmlspecialchars($output));

