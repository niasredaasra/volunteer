<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Volunteers List & Broadcast</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style> body{background:#f7f7fb}.card{border-radius:1rem} table{font-size:0.95rem} </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Volunteers & Visitors</h3>
    <div>
      <a href="../index.php" class="btn btn-secondary btn-sm">Add New</a>
      <button id="showDebug" class="btn btn-info btn-sm ms-2">Debug Info</button>
      <button id="refreshData" class="btn btn-success btn-sm ms-2">🔄 Refresh Data</button>
    </div>
  </div>
  
  <!-- Debug Panel -->
  <div id="debugPanel" class="card shadow-sm mb-4" style="display: none;">
    <div class="card-header">
      <h5>Debug Information</h5>
    </div>
    <div class="card-body">
      <div id="debugInfo">Loading debug info...</div>
    </div>
  </div>
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Message (text)</label>
          <textarea id="wa_msg" class="form-control" rows="2" placeholder="Namaste {{name}} ji, ..."></textarea>
          <div class="form-text">You can write a personal message. {{name}} will be replaced with volunteer's name.</div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Video URL (optional)</label>
          <input id="wa_video" type="url" class="form-control" placeholder="https://... .mp4">
        </div>
        <div class="col-md-2">
          <button id="sendAll" class="btn btn-primary w-100">Send to All</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs for Volunteers and Visitors -->
  <div class="card shadow-sm">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" id="dataTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="volunteers-tab" data-bs-toggle="tab" data-bs-target="#volunteers" type="button" role="tab">
            Volunteers (<span id="volunteerCount">0</span>)
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="visitors-tab" data-bs-toggle="tab" data-bs-target="#visitors" type="button" role="tab">
            Visitors (<span id="visitorCount">0</span>)
          </button>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="tab-content" id="dataTabsContent">
        <!-- Volunteers Tab -->
        <div class="tab-pane fade show active" id="volunteers" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-striped" id="volTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Village</th>
                  <th>City</th>
                  <th>State</th>
                  <th>Country</th>
                  <th>Occupation</th>
                  <th>Seva</th>
                  <th>Items Brought</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        
        <!-- Visitors Tab -->
        <div class="tab-pane fade" id="visitors" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-striped" id="visitorTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Village</th>
                  <th>City</th>
                  <th>State</th>
                  <th>Country</th>
                  <th>Occupation</th>
                  <th>Seva</th>
                  <th>Total Visits</th>
                  <th>Last Visit</th>
                  <th>Registered</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="msg" class="pt-3"></div>
</div>

<script>
  function esc(s){return (s??'').toString().replace(/[&<>]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]))}
  
  function formatItems(itemsJson) {
    if (!itemsJson) return '';
    try {
      const items = JSON.parse(itemsJson);
      if (Array.isArray(items)) {
        return items.join(', ');
      }
      return items;
    } catch (e) {
      return itemsJson;
    }
  }

  async function loadVols() {
    console.log('🔄 Loading volunteers...');
    try {
      const res = await fetch('../api/volunteers.php?fn=list');
      console.log('📡 Response status:', res.status);
      
      const data = await res.json();
      console.log('📊 Volunteers data:', data);
      
      const tb = document.querySelector('#volTable tbody');
      if (!tb) {
        console.error('❌ Volunteers table body not found!');
        return;
      }
      
      tb.innerHTML = '';
      
      // Handle debug data in local environment
      let cleanData = data;
      if (data && typeof data === 'object' && !Array.isArray(data)) {
        // Check if it's an object with numeric keys (converted from array)
        if (data._debug) {
          console.log('🐛 Debug info detected:', data._debug);
          // Remove debug info and convert back to array
          delete data._debug;
          cleanData = Object.values(data).filter(item => 
            item && typeof item === 'object' && item.id
          );
        } else {
          // Convert object with numeric keys to array
          cleanData = Object.values(data).filter(item => 
            item && typeof item === 'object' && item.id
          );
        }
      }
      
      if (Array.isArray(cleanData) && cleanData.length > 0) {
        console.log(`✅ Processing ${cleanData.length} volunteers`);
        cleanData.forEach((v, index) => {
          console.log(`Processing volunteer ${index + 1}:`, v);
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${v.id || 'N/A'}</td>
            <td>${esc(v.name || 'N/A')}</td>
            <td>${esc(v.mobile || '')}</td>
            <td>${esc(v.email || '')}</td>
            <td>${esc(v.village_name || '')}</td>
            <td>${esc(v.city_name || '')}</td>
            <td>${esc(v.state_name || '')}</td>
            <td>${esc(v.country_name || '')}</td>
            <td>${esc(v.occupation_name || '')}</td>
            <td>${esc(v.seva_name || '')}</td>
            <td>${esc(formatItems(v.items_brought) || '')}</td>
            <td>${esc(v.created_at || '')}</td>
          `;
          tb.appendChild(tr);
        });
        document.getElementById('volunteerCount').textContent = cleanData.length;
        console.log(`✅ Successfully displayed ${cleanData.length} volunteers`);
      } else if (Array.isArray(cleanData) && cleanData.length === 0) {
        tb.innerHTML = '<tr><td colspan="12" class="text-center text-muted">No volunteers found</td></tr>';
        document.getElementById('volunteerCount').textContent = '0';
        console.log('ℹ️ No volunteers in database');
      } else {
        tb.innerHTML = '<tr><td colspan="12" class="text-center text-warning">Invalid data format received</td></tr>';
        document.getElementById('volunteerCount').textContent = 'Error';
        console.error('❌ Volunteers API returned non-array:', data);
      }
    } catch (error) {
      console.error('❌ Error loading volunteers:', error);
      const tb = document.querySelector('#volTable tbody');
      if (tb) {
        tb.innerHTML = '<tr><td colspan="12" class="text-center text-danger">Error loading volunteers: ' + error.message + '</td></tr>';
      }
      document.getElementById('volunteerCount').textContent = 'Error';
    }
  }

  async function loadVisitors() {
    console.log('🔄 Loading visitors...');
    try {
      const res = await fetch('../api/visitors.php?fn=list');
      console.log('📡 Visitors response status:', res.status);
      
      const data = await res.json();
      console.log('📊 Visitors data:', data);
      
      const tb = document.querySelector('#visitorTable tbody');
      if (!tb) {
        console.error('❌ Visitors table body not found!');
        return;
      }
      
      tb.innerHTML = '';
      
      // Handle debug data in local environment
      let cleanData = data;
      if (data && typeof data === 'object' && !Array.isArray(data)) {
        // Check if it's an object with numeric keys (converted from array)
        if (data._debug) {
          console.log('🐛 Debug info detected in visitors:', data._debug);
          // Remove debug info and convert back to array
          delete data._debug;
          cleanData = Object.values(data).filter(item => 
            item && typeof item === 'object' && item.id
          );
        } else {
          // Convert object with numeric keys to array
          cleanData = Object.values(data).filter(item => 
            item && typeof item === 'object' && item.id
          );
        }
      }
      
      if (Array.isArray(cleanData) && cleanData.length > 0) {
        console.log(`✅ Processing ${cleanData.length} visitors`);
        cleanData.forEach((v, index) => {
          console.log(`Processing visitor ${index + 1}:`, v);
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${v.id || 'N/A'}</td>
            <td>${esc(v.name || 'N/A')}</td>
            <td>${esc(v.mobile || '')}</td>
            <td>${esc(v.email || '')}</td>
            <td>${esc(v.village_name || '')}</td>
            <td>${esc(v.city_name || '')}</td>
            <td>${esc(v.state_name || '')}</td>
            <td>${esc(v.country_name || '')}</td>
            <td>${esc(v.occupation_name || '')}</td>
            <td>${esc(v.seva_name || '')}</td>
            <td>${v.total_visits || 0}</td>
            <td>${esc(v.last_visit || 'Never')}</td>
            <td>${esc(v.registered_at || '')}</td>
          `;
          tb.appendChild(tr);
        });
        document.getElementById('visitorCount').textContent = cleanData.length;
        console.log(`✅ Successfully displayed ${cleanData.length} visitors`);
      } else if (Array.isArray(cleanData) && cleanData.length === 0) {
        tb.innerHTML = '<tr><td colspan="13" class="text-center text-muted">No visitors found</td></tr>';
        document.getElementById('visitorCount').textContent = '0';
        console.log('ℹ️ No visitors in database');
      } else {
        tb.innerHTML = '<tr><td colspan="13" class="text-center text-warning">Invalid data format received</td></tr>';
        document.getElementById('visitorCount').textContent = 'Error';
        console.error('❌ Visitors API returned non-array:', data);
      }
    } catch (error) {
      console.error('❌ Error loading visitors:', error);
      const tb = document.querySelector('#visitorTable tbody');
      if (tb) {
        tb.innerHTML = '<tr><td colspan="13" class="text-center text-danger">Error loading visitors: ' + error.message + '</td></tr>';
      }
      document.getElementById('visitorCount').textContent = 'Error';
    }
  }

  async function showDebugInfo() {
    const debugPanel = document.getElementById('debugPanel');
    const debugInfo = document.getElementById('debugInfo');
    
    if (debugPanel.style.display === 'none') {
      debugPanel.style.display = 'block';
      debugInfo.innerHTML = 'Loading debug information...';
      
      try {
        // Test API endpoints
        const [volRes, visRes] = await Promise.all([
          fetch('../api/volunteers.php?fn=list'),
          fetch('../api/visitors.php?fn=list')
        ]);
        
        // Get response text first to see raw response
        const volText = await volRes.clone().text();
        const visText = await visRes.clone().text();
        
        let volData, visData;
        try {
          volData = await volRes.json();
        } catch (e) {
          volData = { error: 'JSON parse error', raw: volText };
        }
        
        try {
          visData = await visRes.json();
        } catch (e) {
          visData = { error: 'JSON parse error', raw: visText };
        }
        
        debugInfo.innerHTML = `
          <div class="row">
            <div class="col-md-6">
              <h6>Volunteers API Response:</h6>
              <p><strong>HTTP Status:</strong> ${volRes.status} ${volRes.statusText}</p>
              <p><strong>Content-Type:</strong> ${volRes.headers.get('content-type')}</p>
              ${volText.length > 500 ? 
                `<p><strong>Raw Response (first 500 chars):</strong></p><pre class="bg-warning p-2 small">${volText.substring(0, 500)}...</pre>` :
                `<p><strong>Raw Response:</strong></p><pre class="bg-light p-2 small">${volText}</pre>`
              }
              <p><strong>Parsed JSON:</strong></p>
              <pre class="bg-light p-3 small" style="max-height: 300px; overflow: auto;">${JSON.stringify(volData, null, 2)}</pre>
            </div>
            <div class="col-md-6">
              <h6>Visitors API Response:</h6>
              <p><strong>HTTP Status:</strong> ${visRes.status} ${visRes.statusText}</p>
              <p><strong>Content-Type:</strong> ${visRes.headers.get('content-type')}</p>
              ${visText.length > 500 ? 
                `<p><strong>Raw Response (first 500 chars):</strong></p><pre class="bg-warning p-2 small">${visText.substring(0, 500)}...</pre>` :
                `<p><strong>Raw Response:</strong></p><pre class="bg-light p-2 small">${visText}</pre>`
              }
              <p><strong>Parsed JSON:</strong></p>
              <pre class="bg-light p-3 small" style="max-height: 300px; overflow: auto;">${JSON.stringify(visData, null, 2)}</pre>
            </div>
          </div>
          <div class="mt-3">
            <h6>Quick Links:</h6>
            <p>
              <a href="../debug_volunteers.php" target="_blank" class="btn btn-sm btn-info">Debug Volunteers</a>
              <a href="../test_api.php" target="_blank" class="btn btn-sm btn-info">Test APIs</a>
              <a href="../check_data.php" target="_blank" class="btn btn-sm btn-info">Check Database</a>
            </p>
            <h6>Direct API Links:</h6>
            <p>
              <a href="../api/volunteers.php?fn=list" target="_blank">volunteers.php?fn=list</a> |
              <a href="../api/visitors.php?fn=list" target="_blank">visitors.php?fn=list</a>
            </p>
          </div>
        `;
      } catch (error) {
        debugInfo.innerHTML = `<div class="alert alert-danger">Debug Error: ${error.message}<br>Stack: ${error.stack}</div>`;
      }
    } else {
      debugPanel.style.display = 'none';
    }
  }

  async function sendAll() {
    const msg = document.getElementById('wa_msg').value || '';
    const vid = document.getElementById('wa_video').value || '';
    
    if (!msg && !vid) {
      document.getElementById('msg').innerHTML = '<div class="alert alert-warning">Please enter a message or video URL.</div>';
      return;
    }
    
    // Show loading state
    const sendBtn = document.getElementById('sendAll');
    const originalText = sendBtn.textContent;
    sendBtn.textContent = 'Sending...';
    sendBtn.disabled = true;
    
    try {
      const res = await fetch('../api/whatsapp.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          volunteer_ids: 'all',
          message: msg,
          video_url: vid
        })
      });
      
      const data = await res.json();
      const el = document.getElementById('msg');
      
      if (data.ok) {
        let successHtml = `<div class="alert alert-success">
          <h6>WhatsApp Messages Sent Successfully!</h6>
          <p><strong>Total Recipients:</strong> ${data.total}</p>
          <p><strong>Successful:</strong> ${data.success}</p>
          <p><strong>Failed:</strong> ${data.errors}</p>
        </div>`;
        
        // Show detailed results if there are errors
        if (data.errors > 0 && data.results) {
          successHtml += '<div class="mt-3"><h6>Detailed Results:</h6><div class="table-responsive"><table class="table table-sm">';
          successHtml += '<thead><tr><th>ID</th><th>Name</th><th>Mobile</th><th>Status</th><th>Error</th></tr></thead><tbody>';
          
          data.results.forEach(result => {
            const statusClass = result.ok ? 'text-success' : 'text-danger';
            const statusText = result.ok ? '✅ Sent' : '❌ Failed';
            successHtml += `<tr>
              <td>${result.id}</td>
              <td>${esc(result.name || 'N/A')}</td>
              <td>${esc(result.mobile)}</td>
              <td class="${statusClass}">${statusText}</td>
              <td>${esc(result.error || '')}</td>
            </tr>`;
          });
          
          successHtml += '</tbody></table></div></div>';
        }
        
        el.innerHTML = successHtml;
      } else {
        el.innerHTML = `<div class="alert alert-danger">
          <h6>WhatsApp Send Failed</h6>
          <p><strong>Error:</strong> ${data.error || 'Unknown error'}</p>
        </div>`;
      }
    } catch (error) {
      document.getElementById('msg').innerHTML = `<div class="alert alert-danger">
        <h6>Network Error</h6>
        <p><strong>Error:</strong> ${error.message}</p>
        <p>Please check your internet connection and try again.</p>
      </div>`;
    } finally {
      // Restore button state
      sendBtn.textContent = originalText;
      sendBtn.disabled = false;
    }
  }

  // Force refresh function
  function refreshAllData() {
    console.log('🔄 Force refreshing all data...');
    loadVols();
    loadVisitors();
  }

  // DOM Ready function
  document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM loaded, initializing...');
    
    // Event listeners
    document.getElementById('sendAll').addEventListener('click', sendAll);
    document.getElementById('showDebug').addEventListener('click', showDebugInfo);
    document.getElementById('refreshData').addEventListener('click', refreshAllData);
    
    // Tab event listeners
    document.getElementById('visitors-tab').addEventListener('click', function() {
      console.log('👥 Visitors tab clicked');
      loadVisitors();
    });
    
    document.getElementById('volunteers-tab').addEventListener('click', function() {
      console.log('🤝 Volunteers tab clicked');
      loadVols();
    });
    
    // Load initial data
    console.log('📊 Loading initial data...');
    setTimeout(() => {
      refreshAllData();
    }, 100); // Small delay to ensure DOM is fully ready
  });
  
  // Also load data when script runs (fallback)
  if (document.readyState === 'loading') {
    console.log('⏳ Document still loading, waiting for DOMContentLoaded...');
  } else {
    console.log('✅ Document already loaded, loading data immediately...');
    setTimeout(() => {
      refreshAllData();
    }, 100);
  }
</script>
</body>
</html>
