function loadService(svc, label) {
  currentService = svc;
  document.getElementById('serviceTitle').innerText = label;
  
  fetch(`get_logs.php?service=${svc}&n=40`)
    .then(r => r.text())
    .then(t => document.getElementById('logsContent').innerHTML = t);

  // Set status pill dynamically
  fetch('status.json?_=' + Date.now())
    .then(r => r.json())
    .then(statuses => {
      const status = statuses[svc]?.status || 'unknown';
      const pill = document.getElementById('serviceStatus');
      pill.textContent = status;
      pill.className = 'status-pill ' + (status === 'active' ? 'green' : (status === 'failed' ? 'red' : 'black'));
    });

  // AI suggestion content
  document.getElementById('aiContent').innerHTML = `<p>AI analysis for ${label} loading...</p>`;

  // RCA placeholder
  document.getElementById('rcaContent').innerHTML = `Pending AI analysis for ${label}`;
}

function generateRCA() {
  document.getElementById('rcaContent').innerHTML = `
    <p><b>Root Cause:</b> Port 80 conflict with nginx</p>
    <p><b>Fix:</b> Stop nginx or change Apache's Listen port</p>
    <p><b>Prevention:</b> Add pre-deploy port check</p>
  `;
}

