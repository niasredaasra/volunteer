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
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Volunteers</h3>
    <div><a href="../index.php" class="btn btn-secondary btn-sm">Add New</a></div>
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

  <div class="card shadow-sm">
    <div class="card-body">
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
    const res = await fetch('../api/volunteers.php?fn=list');
    const data = await res.json();
    const tb = document.querySelector('#volTable tbody');
    tb.innerHTML = '';
    data.forEach(v => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${v.id}</td>
        <td>${esc(v.name)}</td>
        <td>${esc(v.mobile||'')}</td>
        <td>${esc(v.email||'')}</td>
        <td>${esc(v.village_name||'')}</td>
        <td>${esc(v.city_name||'')}</td>
        <td>${esc(v.state_name||'')}</td>
        <td>${esc(v.country_name||'')}</td>
        <td>${esc(v.occupation_name||'')}</td>
        <td>${esc(v.seva_name||'')}</td>
        <td>${esc(formatItems(v.items_brought)||'')}</td>
        <td>${esc(v.created_at||'')}</td>
      `;
      tb.appendChild(tr);
    });
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

  document.getElementById('sendAll').addEventListener('click', sendAll);
  loadVols();
</script>
</body>
</html>
