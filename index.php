<?php
// Volunteer Registration Form
require_once 'config.php';

// Environment-specific error reporting
if (defined('ERROR_REPORTING') && ERROR_REPORTING) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Optional: Debug database connection (only in debug mode)
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    echo "<!-- DEBUG: ";
    if (defined('DB_HOST')) {
        $test_conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($test_conn->connect_error) {
            echo "DB ERROR: " . $test_conn->connect_error . " | ENV: " . ENVIRONMENT;
        } else {
            echo "DB OK | ENV: " . ENVIRONMENT;
        }
    } else {
        echo "CONFIG ERROR";
    }
    echo " -->";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Volunteer Registration</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background: #f7f7fb; }
    .card { border-radius: 1rem; }
    .req::after{content:" *"; color:#d00;}
  </style>
</head>
<body>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-3">Volunteer Registration</h3>
          <form id="volForm" class="row g-3">
            <div class="col-md-6">
              <label class="form-label req">Full Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label req">Mobile</label>
              <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="e.g., 9876543210" required>
              <div id="mobile-status" class="form-text"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="name@example.com">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone (Alt)</label>
              <input type="tel" name="phone" class="form-control">
            </div>

            <div class="col-md-3">
              <label class="form-label">Village</label>
              <div class="input-group">
                <select id="village_id" name="village_id" class="form-select">
                  <option value="">-- Select Village --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddVillage">Add</button>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">City</label>
              <div class="input-group">
                <select name="city_id" id="city" class="form-select">
                  <option value="">-- Select City --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddCity">Add</button>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">State</label>
              <div class="input-group">
                <select name="state_id" id="state" class="form-select">
                  <option value="">-- Select State --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddState">Add</button>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">Country</label>
              <div class="input-group">
                <select name="country_id" id="country" class="form-select">
                  <option value="">-- Select Country --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddCountry">Add</button>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Occupation</label>
              <div class="input-group">
                <select name="occupation_id" id="occupation" class="form-select">
                  <option value="">-- Select Occupation --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddOcc">Add</button>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Interested Seva</label>
              <div class="input-group">
                <select name="seva_interest_id" id="seva" class="form-select">
                  <option value="">-- Select Seva --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddSeva">Add</button>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Items Brought</label>
              <div class="input-group">
                <select name="items_brought[]" id="items_brought" class="form-select" multiple size="4">
                  <option value="">-- Select Items --</option>
                </select>
                <button class="btn btn-outline-secondary" type="button" id="btnAddItem">Add</button>
              </div>
              <div class="form-text">Hold Ctrl/Cmd to select multiple items</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="dob" class="form-control">
            </div>

            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Any notes"></textarea>
            </div>

            <div class="col-12 d-flex gap-2">
              <button class="btn btn-primary" type="submit">Save Volunteer</button>
              <a class="btn btn-secondary" href="admin/list.php">View List / Broadcast</a>
            </div>
            <div id="msg" class="pt-2"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Visitor Management Modal -->
<div class="modal fade" id="visitorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Existing Visitor - Record New Visit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="visitorDetails">
          <div class="card mb-3">
            <div class="card-header">
              <button class="btn btn-outline-primary btn-sm" id="toggleDetails">
                Hide Visitor Details
              </button>
            </div>
            <div class="card-body" id="detailsContent">
              <div id="visitorInfo"></div>
            </div>
          </div>
        </div>
        
        <div id="visitHistory">
          <div class="card mb-3">
            <div class="card-header">
              <button class="btn btn-outline-success btn-sm" id="toggleHistory">
                Hide Visit History (<span id="visitCount">0</span> visits)
              </button>
            </div>
            <div class="card-body" id="historyContent">
              <div id="historyTable"></div>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Record New Visit</h6>
          </div>
          <div class="card-body">
            <form id="newVisitForm">
              <div class="mb-3">
                <label class="form-label">Items Brought</label>
                <select name="items_brought[]" id="visitItems" class="form-select" multiple size="4">
                  <option value="">-- Loading Items --</option>
                </select>
                <div class="form-text">Hold Ctrl/Cmd to select multiple items</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" id="visitRemarks" class="form-control" rows="3" placeholder="Any notes about this visit..."></textarea>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" id="saveVisit">
                  <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                  Save Visit
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show error message to user
function showErrorMessage(message) {
    const msgDiv = document.getElementById('msg');
    if (msgDiv) {
        msgDiv.innerHTML = `<div class="alert alert-warning alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const alert = msgDiv.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

// Check if mobile number exists in visitor database
async function checkMobileExists(mobile) {
    if (!mobile || mobile.length < 10) return;
    
    const statusDiv = document.getElementById('mobile-status');
    statusDiv.innerHTML = '<span class="text-info">🔍 Checking mobile number...</span>';
    
    try {
        const response = await fetch(`api/visitors.php?fn=check-mobile&mobile=${mobile}`);
        const data = await response.json();
        
        if (data.exists) {
            if (data.found_in === 'visitors') {
                statusDiv.innerHTML = '<span class="text-warning">⚠️ This mobile number already exists in visitor database. <button class="btn btn-sm btn-primary ms-2" onclick="openVisitorModal(\'' + mobile + '\')">Record New Visit</button></span>';
            } else if (data.found_in === 'volunteers') {
                statusDiv.innerHTML = '<span class="text-info">ℹ️ This mobile number exists in volunteer database. <button class="btn btn-sm btn-primary ms-2" onclick="openVisitorModal(\'' + mobile + '\')">Record New Visit</button></span>';
            }
        } else {
            statusDiv.innerHTML = '<span class="text-success">✅ New mobile number - ready for registration</span>';
        }
    } catch (error) {
        console.error('Error checking mobile:', error);
        statusDiv.innerHTML = '<span class="text-muted">Unable to check mobile number</span>';
    }
}

// Visitor Management Functions
let currentVisitorMobile = '';

async function openVisitorModal(mobile) {
    currentVisitorMobile = mobile;
    
    // Load visitor data
    try {
        const response = await fetch(`api/visitors.php?fn=get-history&mobile=${mobile}`);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        
        // Reset modal state before populating
        resetVisitorModal();
        
        if (data.ok && data.visitor) {
            populateVisitorModal(data.visitor);
        } else {
            populateBasicModal(mobile);
        }
        
        // Load items for the visit form
        await loadVisitItems();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('visitorModal'));
        modal.show();
        
    } catch (error) {
        console.error('Error loading visitor data:', error);
        alert('Error loading visitor information');
    }
}

function populateVisitorModal(visitor) {
    // Update modal title based on data source
    const modalTitle = document.querySelector('#visitorModal .modal-title');
    if (visitor.is_volunteer_data) {
        modalTitle.textContent = 'Volunteer - Record New Visit';
    } else {
        modalTitle.textContent = 'Existing Visitor - Record New Visit';
    }
    
    // Show visitor details section
    document.getElementById('visitorDetails').style.display = 'block';
    
    // Create clean, responsive visitor details UI
    const visitorInfo = document.getElementById('visitorInfo');
    
    visitorInfo.innerHTML = `
        <div class="row g-2">
            <div class="col-md-6">
                <strong>Name:</strong> ${visitor.name || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Mobile:</strong> ${visitor.mobile || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Email:</strong> ${visitor.email || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Phone:</strong> ${visitor.phone || 'N/A'}
            </div>
            <div class="col-md-4">
                <strong>Village:</strong> ${visitor.village_name || 'N/A'}
            </div>
            <div class="col-md-4">
                <strong>City:</strong> ${visitor.city_name || 'N/A'}
            </div>
            <div class="col-md-4">
                <strong>State:</strong> ${visitor.state_name || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Country:</strong> ${visitor.country_name || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>DOB:</strong> ${visitor.dob || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Occupation:</strong> ${visitor.occupation_name || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Seva Interest:</strong> ${visitor.seva_name || 'N/A'}
            </div>
            <div class="col-12">
                <small class="text-muted">Registered: ${new Date(visitor.registered_at).toLocaleString()}</small>
            </div>
        </div>
    `;
    
    // Content is already visible by default, just update button text
    const toggleDetails = document.getElementById('toggleDetails');
    if (toggleDetails) {
        toggleDetails.textContent = 'Hide Visitor Details';
    }
    
    // Show visit history if available
    if (visitor.visits && visitor.visits.length > 0) {
        document.getElementById('visitHistory').style.display = 'block';
        document.getElementById('visitCount').textContent = visitor.visits.length;
        
        let historyHTML = `
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%;">#</th>
                            <th style="width: 25%;">Date & Time</th>
                            <th style="width: 35%;">Items Brought</th>
                            <th style="width: 32%;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        visitor.visits.forEach((visit, index) => {
            const items = Array.isArray(visit.items_brought) 
                ? visit.items_brought.join(', ') 
                : (visit.items_brought || 'None');
            
            const serialNumber = visitor.visits.length - index; // Reverse order (latest first)
            
            // Parse the visit date and format it properly
            const visitDate = new Date(visit.visit_date);
            const formattedDate = visitDate.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit', 
                year: 'numeric'
            });
            const formattedTime = visitDate.toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            
            historyHTML += `
                <tr>
                    <td><span class="badge bg-primary">${serialNumber}</span></td>
                    <td>
                        <div class="text-nowrap">
                            <strong>${formattedDate}</strong><br>
                            <small class="text-muted">${formattedTime}</small>
                        </div>
                        <small class="text-info" title="Database time: ${visit.visit_date}">📅</small>
                    </td>
                    <td>
                        ${items !== 'None' ? 
                            `<span class="badge bg-success me-1">${items.split(', ').length} items</span><br><small class="text-muted">${items}</small>` 
                            : '<span class="badge bg-secondary">No items</span>'
                        }
                    </td>
                    <td><small>${visit.remarks || '<em class="text-muted">No remarks</em>'}</small></td>
                </tr>
            `;
        });
        
        historyHTML += `
                    </tbody>
                </table>
            </div>
        `;
        
        // Set the clean history content
        const historyTable = document.getElementById('historyTable');
        historyTable.innerHTML = historyHTML;
        
        // Content is already visible by default, just update button text
        const toggleHistory = document.getElementById('toggleHistory');
        if (toggleHistory) {
            toggleHistory.innerHTML = `Hide Visit History (${visitor.visits.length} visits)`;
        }
    } else {
        // Show history section even if no visits yet
        document.getElementById('visitHistory').style.display = 'block';
        document.getElementById('visitCount').textContent = '0';
        
        const historyTable = document.getElementById('historyTable');
        historyTable.innerHTML = `
            <div class="text-center py-4">
                <div class="text-muted">
                    <i class="fas fa-history fa-2x mb-2"></i>
                    <p class="mb-0">No visit history found for this visitor.</p>
                    <small>New visits will appear here.</small>
                </div>
            </div>
        `;
        
        // Content is already visible by default, just update button text
        const toggleHistory = document.getElementById('toggleHistory');
        if (toggleHistory) {
            toggleHistory.innerHTML = 'Hide Visit History (0 visits)';
        }
    }
}

function populateBasicModal(mobile) {
    // For volunteers who don't have visitor records yet
    document.getElementById('visitorDetails').style.display = 'none';
    document.getElementById('visitHistory').style.display = 'none';
}

function resetVisitorModal() {
    // Reset all modal sections to default state
    // Keep sections visible, just reset their content
    document.getElementById('visitorDetails').style.display = 'block';
    document.getElementById('visitHistory').style.display = 'block';
    
    // Reset toggle buttons - now content is visible by default
    const toggleDetails = document.getElementById('toggleDetails');
    const toggleHistory = document.getElementById('toggleHistory');
    
    if (toggleDetails) {
        toggleDetails.textContent = 'Hide Visitor Details';
    }
    
    if (toggleHistory) {
        toggleHistory.innerHTML = 'Hide Visit History (<span id="visitCount">0</span> visits)';
    }
    
    // Clear content
    document.getElementById('visitorInfo').innerHTML = '';
    document.getElementById('historyTable').innerHTML = '';
    
    // Reset visit count
    const visitCountSpan = document.getElementById('visitCount');
    if (visitCountSpan) {
        visitCountSpan.textContent = '0';
    }
    
    // Reset form
    document.getElementById('newVisitForm').reset();
    
    // Show content sections by default (remove the display:none)
    document.getElementById('detailsContent').style.display = 'block';
    document.getElementById('historyContent').style.display = 'block';
}

async function loadVisitItems() {
    try {
        const response = await fetch('api/items.php?fn=list');
        const data = await response.json();
        
        const select = document.getElementById('visitItems');
        select.innerHTML = '<option value="">-- Select Items --</option>';
        
        // Convert data to array if it's an object with numeric keys
        let items = [];
        if (Array.isArray(data)) {
            items = data;
        } else if (data && typeof data === 'object') {
            // Convert object with numeric keys to array
            items = Object.values(data).filter(item => 
                item && typeof item === 'object' && item.id && !item.environment // Exclude debug info
            );
        }
        
        if (items.length > 0) {
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.item_name;
                option.textContent = item.item_name;
                select.appendChild(option);
            });
            console.log(`✅ Loaded ${items.length} items for visit form`);
        } else {
            select.innerHTML += '<option value="" disabled>No items available</option>';
        }
    } catch (error) {
        console.error('Error loading items:', error);
        const select = document.getElementById('visitItems');
        select.innerHTML = '<option value="">Error loading items</option>';
    }
}

// Modal toggle handlers
document.addEventListener('DOMContentLoaded', function() {
    // Toggle visitor details
    document.getElementById('toggleDetails')?.addEventListener('click', function() {
        const content = document.getElementById('detailsContent');
        const isVisible = content.style.display !== 'none';
        content.style.display = isVisible ? 'none' : 'block';
        this.textContent = isVisible ? 'Show Visitor Details' : 'Hide Visitor Details';
    });
    
    // Toggle visit history
    document.getElementById('toggleHistory')?.addEventListener('click', function() {
        const content = document.getElementById('historyContent');
        const isVisible = content.style.display !== 'none';
        content.style.display = isVisible ? 'none' : 'block';
        const visitCount = document.getElementById('visitCount')?.textContent || '0';
        this.innerHTML = isVisible ? 
            `Show Visit History (<span id="visitCount">${visitCount}</span> visits)` : 
            `Hide Visit History (${visitCount} visits)`;
    });
    
    // Handle new visit form submission
    document.getElementById('newVisitForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveVisit');
        const spinner = document.getElementById('saveSpinner');
        
        saveBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData();
            formData.append('mobile', currentVisitorMobile);
            formData.append('fn', 'add-visit');
            
            // Get selected items
            const selectedItems = Array.from(document.getElementById('visitItems').selectedOptions)
                .map(option => option.value)
                .filter(value => value);
            
            selectedItems.forEach(item => formData.append('items_brought[]', item));
            
            const remarks = document.getElementById('visitRemarks').value;
            formData.append('remarks', remarks);
            
            const response = await fetch('api/visitors.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.ok) {
                alert('Visit recorded successfully!');
                
                // Reset form
                document.getElementById('newVisitForm').reset();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('visitorModal'));
                modal.hide();
                
                // Clear mobile field status
                document.getElementById('mobile-status').innerHTML = '';
                document.getElementById('mobile').value = '';
                
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
            
        } catch (error) {
            console.error('Error saving visit:', error);
            alert('Error saving visit');
        } finally {
            saveBtn.disabled = false;
            spinner.classList.add('d-none');
        }
    });
});

// Generic function to load dropdown with error handling
async function loadDropdown(apiUrl, selectId, itemKey, defaultText, successCallback = null) {
    const sel = document.getElementById(selectId);
    try {
        const res = await fetch(apiUrl);
        
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();
        
        // Handle API error responses
        if (data.error) {
            throw new Error(data.error);
        }
        
        sel.innerHTML = `<option value="">${defaultText}</option>`;
        
        // Handle warning (empty data) responses
        if (data.warning && data.data) {
            console.warn(`${selectId} API warning:`, data.warning);
            sel.innerHTML += '<option value="" disabled>⚠️ No data found</option>';
            return;
        }
        
        // Convert data to array if it's an object with numeric keys
        let items = [];
        if (Array.isArray(data)) {
            items = data;
        } else if (data && typeof data === 'object') {
            // Convert object with numeric keys to array
            items = Object.values(data).filter(item => 
                item && typeof item === 'object' && item.id && !item.environment // Exclude debug info
            );
        }
        
        if (items.length === 0) {
            sel.innerHTML += '<option value="" disabled>⚠️ No data available</option>';
            return;
        }
        
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item[itemKey];
            sel.appendChild(opt);
        });
        
        console.log(`✅ Loaded ${items.length} ${selectId} items`);
        
        // Call success callback if provided
        if (successCallback) {
            successCallback(items);
        }
        
    } catch (error) {
        console.error(`❌ Error loading ${selectId}:`, error);
        sel.innerHTML = `<option value="">❌ Error loading ${selectId}</option>`;
        showErrorMessage(`Failed to load ${selectId}. Please refresh the page.`);
    }
}

// Load villages from DB
async function loadVillages() {
    await loadDropdown('api/villages.php?fn=list', 'village_id', 'village_name', '-- Select Village --');
}

// Load items from DB
async function loadItems() {
    const sel = document.getElementById('items_brought');
    try {
        const res = await fetch('api/items.php?fn=list');
        
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        sel.innerHTML = '<option value="">-- Select Items --</option>';
        
        if (data.warning && data.data) {
            console.warn('Items API warning:', data.warning);
            sel.innerHTML += '<option value="" disabled>⚠️ No items found</option>';
            return;
        }
        
        // Convert data to array if it's an object with numeric keys
        let items = [];
        if (Array.isArray(data)) {
            items = data;
        } else if (data && typeof data === 'object') {
            // Convert object with numeric keys to array
            items = Object.values(data).filter(item => 
                item && typeof item === 'object' && item.id && !item.environment // Exclude debug info
            );
        }
        
        if (items.length === 0) {
            sel.innerHTML += '<option value="" disabled>⚠️ No items available</option>';
            return;
        }
        
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.item_name; // Note: using item_name as value for items
            opt.textContent = item.item_name;
            sel.appendChild(opt);
        });
        
        console.log(`✅ Loaded ${items.length} items`);
        
    } catch (error) {
        console.error('❌ Error loading items:', error);
        sel.innerHTML = '<option value="">❌ Error loading items</option>';
        showErrorMessage('Failed to load items. Please refresh the page.');
    }
}

// Load cities from DB
async function loadCities() {
    await loadDropdown('api/cities.php?fn=list', 'city', 'city_name', '-- Select City --');
}

// Load states from DB
async function loadStates() {
    await loadDropdown('api/states.php?fn=list', 'state', 'state_name', '-- Select State --');
}

// Load countries from DB
async function loadCountries() {
    const sel = document.getElementById('country');
    try {
        const res = await fetch('api/countries.php?fn=list');
        
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }
        
        const data = await res.json();
        
        // Handle API error responses
        if (data.error) {
            throw new Error(data.error);
        }
        
        sel.innerHTML = '<option value="">-- Select Country --</option>';
        
        // Handle warning (empty data) responses
        if (data.warning && data.data) {
            console.warn('Countries API warning:', data.warning);
            sel.innerHTML += '<option value="" disabled>⚠️ No countries found</option>';
            return;
        }
        
        let indiaId = null; // Store India's ID for auto-selection
        
        // Convert data to array if it's an object with numeric keys
        let countries = [];
        if (Array.isArray(data)) {
            countries = data;
        } else if (data && typeof data === 'object') {
            // Convert object with numeric keys to array
            countries = Object.values(data).filter(item => 
                item && typeof item === 'object' && item.id && !item.environment // Exclude debug info
            );
        }
        
        if (countries.length === 0) {
            sel.innerHTML += '<option value="" disabled>⚠️ No countries available</option>';
            return;
        }
        
        countries.forEach(country => {
            const opt = document.createElement('option');
            opt.value = country.id;
            opt.textContent = country.country_name;
            sel.appendChild(opt);
            
            // Find India's ID for auto-selection
            if (country.country_name.toLowerCase() === 'india') {
                indiaId = country.id;
            }
        });
        
        // Auto-select India if found
        if (indiaId) {
            sel.value = indiaId;
            console.log('India auto-selected with ID:', indiaId);
        }
        
        console.log(`✅ Loaded ${countries.length} countries`);
        
    } catch (error) {
        console.error('❌ Error loading countries:', error);
        sel.innerHTML = '<option value="">❌ Error loading countries</option>';
        
        // Show user-friendly error message
        showErrorMessage('Failed to load countries. Please refresh the page.');
    }
}

// Add new village
async function addVillage() {
    const name = prompt("Enter new village name:");
    if (!name || name.trim() === '') return;
    
    try {
        const formData = new FormData();
        formData.append('village_name', name.trim());
        formData.append('fn', 'add');
        
        const res = await fetch('api/villages.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        if (data.ok) {
            alert("Village added successfully!");
            await loadVillages(); // Reload dropdown
            document.getElementById('village_id').value = data.id; // Auto-select new village
        } else {
            alert("Error: " + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error adding village:', error);
        alert("Error: Failed to add village");
    }
}

// Add new item
async function addItem() {
    const name = prompt("Enter new item name:");
    if (!name || name.trim() === '') return;
    
    try {
        const formData = new FormData();
        formData.append('item_name', name.trim());
        formData.append('fn', 'add');
        
        const res = await fetch('api/items.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        if (data.ok) {
            alert("Item added successfully!");
            await loadItems(); // Reload dropdown
        } else {
            alert("Error: " + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error adding item:', error);
        alert("Error: Failed to add item");
    }
}

// Add new city
async function addCity() {
    const name = prompt("Enter new city name:");
    if (!name || name.trim() === '') return;
    
    try {
        const formData = new FormData();
        formData.append('city_name', name.trim());
        formData.append('fn', 'add');
        
        const res = await fetch('api/cities.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        if (data.ok) {
            alert("City added successfully!");
            await loadCities(); // Reload dropdown
            document.getElementById('city').value = data.id; // Auto-select new city
        } else {
            alert("Error: " + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error adding city:', error);
        alert("Error: Failed to add city");
    }
}

// Add new state
async function addState() {
    const name = prompt("Enter new state name:");
    if (!name || name.trim() === '') return;
    
    try {
        const formData = new FormData();
        formData.append('state_name', name.trim());
        formData.append('fn', 'add');
        
        const res = await fetch('api/states.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        if (data.ok) {
            alert("State added successfully!");
            await loadStates(); // Reload dropdown
            document.getElementById('state').value = data.id; // Auto-select new state
        } else {
            alert("Error: " + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error adding state:', error);
        alert("Error: Failed to add state");
    }
}

// Add new country
async function addCountry() {
    const name = prompt("Enter new country name:");
    if (!name || name.trim() === '') return;
    
    try {
        const formData = new FormData();
        formData.append('country_name', name.trim());
        formData.append('fn', 'add');
        
        const res = await fetch('api/countries.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        if (data.ok) {
            alert("Country added successfully!");
            await loadCountries(); // Reload dropdown
            document.getElementById('country').value = data.id; // Auto-select new country
        } else {
            alert("Error: " + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error adding country:', error);
        alert("Error: Failed to add country");
    }
}


  async function loadSelect(url, selectId, labelKey, valueKey='id') {
    const sel = document.getElementById(selectId);
    try {
      const res = await fetch(url);
      
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }
      
      const data = await res.json();
      
      if (data.error) {
        throw new Error(data.error);
      }
      
      sel.innerHTML = '<option value="">-- Select --</option>';
      
      if (data.warning && data.data) {
        console.warn(`${selectId} API warning:`, data.warning);
        sel.innerHTML += '<option value="" disabled>⚠️ No data found</option>';
        return;
      }
      
      // Convert data to array if it's an object with numeric keys
      let items = [];
      if (Array.isArray(data)) {
        items = data;
      } else if (data && typeof data === 'object') {
        // Convert object with numeric keys to array
        items = Object.values(data).filter(item => 
          item && typeof item === 'object' && item.id && !item.environment // Exclude debug info
        );
      }
      
      if (items.length === 0) {
        sel.innerHTML += '<option value="" disabled>⚠️ No data available</option>';
        return;
      }
      
      items.forEach(it => {
        const opt = document.createElement('option');
        opt.value = it[valueKey];
        opt.textContent = it[labelKey];
        sel.appendChild(opt);
      });
      
      console.log(`✅ Loaded ${items.length} ${selectId} items`);
      
    } catch (error) {
      console.error(`❌ Error loading ${selectId}:`, error);
      sel.innerHTML = `<option value="">❌ Error loading ${selectId}</option>`;
      showErrorMessage(`Failed to load ${selectId}. Please refresh the page.`);
    }
  }

  async function loadDatalist(field, dlId) {
    const res = await fetch('api/suggestions.php?field=' + field);
    const data = await res.json();
    const dl = document.getElementById(dlId);
    dl.innerHTML = '';
    data.forEach(v => {
      const opt = document.createElement('option');
      opt.value = v;
      dl.appendChild(opt);
    });
  }

  async function addMaster(endpoint, key, promptText) {
    const val = prompt(promptText);
    if (!val) return;
    const form = new FormData();
    form.append(key, val);
    form.append('fn', 'add');
    const res = await fetch(endpoint, { method: 'POST', body: form });
    const data = await res.json();
    if (data.error) { alert('Error: ' + data.error); return; }
    // Reload select and select new item
    if (endpoint.includes('occupations')) {
      await loadSelect('api/occupations.php?fn=list', 'occupation', 'occupation_name');
      document.getElementById('occupation').value = data.id;
    } else {
      await loadSelect('api/seva_interests.php?fn=list', 'seva', 'seva_name');
      document.getElementById('seva').value = data.id;
    }
  }

  document.getElementById('btnAddOcc').addEventListener('click', () => addMaster('api/occupations.php', 'occupation_name', 'Enter new occupation'));
  document.getElementById('btnAddSeva').addEventListener('click', () => addMaster('api/seva_interests.php', 'seva_name', 'Enter new seva interest'));
  document.getElementById('btnAddVillage').addEventListener('click', addVillage);
  document.getElementById('btnAddItem').addEventListener('click', addItem);
  document.getElementById('btnAddCity').addEventListener('click', addCity);
  document.getElementById('btnAddState').addEventListener('click', addState);
  document.getElementById('btnAddCountry').addEventListener('click', addCountry);

  // Add mobile number validation
  document.getElementById('mobile').addEventListener('blur', (e) => {
    checkMobileExists(e.target.value);
  });

  // Load initial data
  loadVillages(); // Load villages dropdown
  loadItems(); // Load items dropdown
  loadCities(); // Load cities dropdown
  loadStates(); // Load states dropdown
  loadCountries(); // Load countries dropdown
  loadSelect('api/occupations.php?fn=list', 'occupation', 'occupation_name');
  loadSelect('api/seva_interests.php?fn=list', 'seva', 'seva_name');

  document.getElementById('volForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = new FormData(e.target);
    form.append('fn','add');
    const res = await fetch('api/volunteers.php', { method: 'POST', body: form });
    const data = await res.json();
    const msg = document.getElementById('msg');
    if (data.ok) {
      const successMsg = data.message || ('Saved! Volunteer ID: ' + data.id);
      let msgContent = '<div class="alert alert-success">' + successMsg;
      if (data.visitor_id && data.visit_id) {
        msgContent += '<br><small class="text-muted">✓ Visitor record created (ID: ' + data.visitor_id + ') ✓ Visit recorded (ID: ' + data.visit_id + ')</small>';
      }
      msgContent += '</div>';
      msg.innerHTML = msgContent;
      e.target.reset();
      // Reload all dropdowns to ensure they're fresh
      loadVillages();
      loadItems();
      loadCities();
      loadStates();
      loadCountries(); // This will auto-select India again
    } else {
      msg.innerHTML = '<div class="alert alert-danger">Error: ' + (data.error || 'Unknown') + '</div>';
    }
  });
</script>
</body>
</html>
