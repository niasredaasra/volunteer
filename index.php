<?php
// Volunteer Registration Form

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// TEMPORARY: Debug database connection
echo "<!-- DEBUG: ";
if (file_exists('config.php')) {
    include 'config.php';
    if (defined('DB_HOST')) {
        $test_conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($test_conn->connect_error) {
            echo "DB ERROR: " . $test_conn->connect_error;
        } else {
            echo "DB OK";
        }
    } else {
        echo "CONFIG ERROR";
    }
} else {
    echo "CONFIG FILE MISSING";
}
echo " -->";
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
              <input type="tel" name="mobile" class="form-control" placeholder="e.g., 9876543210" required>
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
        
        const items = Array.isArray(data) ? data : [];
        
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
        
        const items = Array.isArray(data) ? data : [];
        
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
        const countries = Array.isArray(data) ? data : [];
        
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
      
      const items = Array.isArray(data) ? data : [];
      
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
      msg.innerHTML = '<div class="alert alert-success">Saved! Volunteer ID: ' + data.id + '</div>';
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
