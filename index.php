<?php
// Define monitored services
$services = [
    "httpd" => "Apache HTTP (httpd)",
    "mariadb" => "MariaDB (DB)",
    "redis" => "Redis",
    "nginx" => "Nginx",
    "sshd" => "SSH",
    "firewalld" => "FirewallD"
];

// Function to get status
function getStatus($service) {
    $status = trim(shell_exec("systemctl is-active $service"));
    return $status;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>System Service Monitoring</title>
<style>
/* Top blue bar */
header {
  position: fixed; top:0; left:0; width:100%;
  background:#007bff; color:white; padding:10px;
  font-weight:600; font-size:18px; z-index:1000;
}

/* Sidebar */
.sidebar {
  position: fixed; top:50px; left:0;
  width:220px; background:#111; color:white;
  height:calc(100% - 50px); overflow:auto;
  padding:15px;
}
.sidebar h2 { color:#00ffff; margin:0 0 10px; font-size:16px; }
.service-item {
  background:#1c1c1c; padding:8px; margin-bottom:5px; border-radius:4px;
  display:flex; justify-content:space-between; cursor:pointer;
}
.service-item:hover { background:#2b2b2b; }
.status-pill {
  padding:2px 6px; border-radius:10px; font-size:12px; font-weight:bold;
}
.green { background:#00cc44; color:white; }
.red { background:#cc0000; color:white; }
.black { background:#333; color:white; }

/* Main content */
.main {
  margin-left:220px; padding:20px; padding-top:70px;
  background:#f4f4f4; min-height:100vh;
}

/* Service header */
.service-top { display:flex; align-items:center; gap:10px; margin-bottom:15px; }

/* Two columns */
.row { display:flex; gap:20px; margin-bottom:20px; }
.col { flex:1; }
.ai-box, .logs {
  background:white; padding:12px; border:1px solid #dcdcdc; border-radius:6px;
  min-height:300px; box-shadow:0 1px 3px rgba(0,0,0,0.06);
}
.logs pre { white-space:pre-wrap; font-size:13px; }

/* RCA Section */
.rca-section { background:white; border:1px solid #dcdcdc; border-radius:6px; padding:12px; }
.rca-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
.rca-header button {
  background:#007bff; color:white; border:none; padding:6px 10px; border-radius:4px; cursor:pointer;
}
.rca-header button:hover { background:#0056b3; }
</style>
<script>
let currentService = "";

function loadService(service, label) {
    currentService = service;
    document.getElementById('serviceTitle').innerText = label;
    document.getElementById('serviceStatus').innerText = "Loading...";
    fetch('get_logs.php?service=' + service)
        .then(res => res.text())
        .then(data => {
            document.getElementById('logsContent').innerHTML = data;
        });
    // Fake AI suggestion for demo
    document.getElementById('aiContent').innerHTML = `<p>AI analysis for ${label}...</p>`;
    document.getElementById('rcaContent').innerHTML = `<p>Pending RCA for ${label}</p>`;
}

function generateRCA() {
    document.getElementById('rcaContent').innerHTML = `
        <p><b>Root Cause:</b> Port 80 conflict with nginx</p>
        <p><b>Fix:</b> Stop nginx or change Apache's Listen port</p>
        <p><b>Prevention:</b> Add pre-deploy port check</p>
    `;
}
</script>
</head>
<body>

<header>System Service Monitoring</header>

<div class="sidebar">
  <h2>Services</h2>
  <?php foreach ($services as $name => $label): 
    $status = getStatus($name);
    $colorClass = $status === 'active' ? 'green' : ($status === 'failed' ? 'red' : 'black');
  ?>
    <div class="service-item" onclick="loadService('<?php echo $name; ?>','<?php echo $label; ?>')">
      <span><?php echo $label; ?></span>
      <span class="status-pill <?php echo $colorClass; ?>"><?php echo $status; ?></span>
    </div>
  <?php endforeach; ?>
</div>

<div class="main">
  <div class="service-top">
    <h2 id="serviceTitle">Select a service</h2>
    <span id="serviceStatus" class="status-pill black">-</span>
  </div>

  <div class="row">
    <div class="col">
      <div class="ai-box">
        <h3>AI Suggestions</h3>
        <div id="aiContent">Select a service to see suggestions</div>
      </div>
    </div>
    <div class="col">
      <div class="logs">
        <h3>Logs</h3>
        <pre id="logsContent">-</pre>
      </div>
    </div>
  </div>

  <div class="rca-section">
    <div class="rca-header">
      <span><b>RCA:</b></span>
      <button onclick="generateRCA()">AI Suggestion</button>
    </div>
    <div id="rcaContent">Select a service for RCA details</div>
  </div>
</div>

</body>
</html>

