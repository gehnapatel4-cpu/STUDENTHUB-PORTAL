<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Fetch API CRUD - Practical 13</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="site-header">
        <div class="top-bar">
            <h1>PHP JSON API &amp; Fetch CRUD</h1>
            <div class="header-buttons">
                <button class="btn-theme" id="themeBtn">Dark Mode</button>
                <button class="btn-menu" id="menuBtn">&#9776;</button>
            </div>
        </div>

        <nav class="page-nav" id="mainNav">
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="events.html">Events</a>
            <a href="faq.html">FAQ</a>
            <a href="dashboard.html">Dashboard</a>
            <a href="ajax_crud.html" class="active">AJAX CRUD</a>
            <a href="contact.html">Contact</a>
        </nav>
    </header>

    <div id="statusAlert" class="card" style="display: none; font-weight: bold;"></div>

    <div class="card">
        <h2>Add Event (Async Fetch API - No Page Reload)</h2>
        <form id="ajaxForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                <div>
                    <label><strong>Event Title:</strong></label>
                    <input type="text" id="evTitle" placeholder="Title" required>
                </div>
                <div>
                    <label><strong>Category:</strong></label>
                    <select id="evCategory" required>
                        <option value="Technical">Technical</option>
                        <option value="Sports">Sports</option>
                        <option value="Cultural">Cultural</option>
                        <option value="Academic">Academic</option>
                    </select>
                </div>
                <div>
                    <label><strong>Date:</strong></label>
                    <input type="date" id="evDate" required>
                </div>
                <div>
                    <label><strong>Venue:</strong></label>
                    <input type="text" id="evVenue" placeholder="Venue" required>
                </div>
            </div>

            <div style="margin-top: 10px;">
                <label><strong>Description:</strong></label>
                <textarea id="evDesc" rows="2" placeholder="Brief event description" required></textarea>
            </div>

            <div align="center" style="margin-top: 14px;">
                <input type="submit" id="submitBtn" value="Add Event via AJAX">
                <input type="reset" value="Clear">
            </div>
        </form>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h2>Live Events Table (Fetched from api.php)</h2>
            <button id="refreshBtn" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.9rem;">Refresh List</button>
        </div>

        <div id="loadingIndicator" style="text-align: center; padding: 20px; font-weight: bold; color: #64748b;">
            Loading records from API...
        </div>

        <table id="eventsTable" style="display: none;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title &amp; Category</th>
                    <th>Description</th>
                    <th>Date &amp; Venue</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>

    <hr>
    <p align="center">
        <a href="index.html" class="btn">Back to Home</a>
        <a href="dashboard.html" class="btn btn-secondary">Go to Dashboard</a>
    </p>

    <script src="script.js"></script>
    <script>
        const statusAlert = document.getElementById("statusAlert");
        const ajaxForm = document.getElementById("ajaxForm");
        const loadingIndicator = document.getElementById("loadingIndicator");
        const eventsTable = document.getElementById("eventsTable");
        const tableBody = document.getElementById("tableBody");
        const refreshBtn = document.getElementById("refreshBtn");

        function showMessage(msg, isSuccess) {
            statusAlert.style.display = "block";
            statusAlert.textContent = msg;
            if (isSuccess) {
                statusAlert.style.color = "#166534";
                statusAlert.style.background = "#f0fdf4";
                statusAlert.style.borderColor = "#16a34a";
            } else {
                statusAlert.style.color = "#991b1b";
                statusAlert.style.background = "#fef2f2";
                statusAlert.style.borderColor = "#dc2626";
            }
            setTimeout(function() {
                statusAlert.style.display = "none";
            }, 4000);
        }

        function loadEvents() {
            loadingIndicator.style.display = "block";
            eventsTable.style.display = "none";

            fetch("api.php?action=list")
                .then(function(res) {
                    return res.json();
                })
                .then(function(result) {
                    loadingIndicator.style.display = "none";
                    if (result.success) {
                        eventsTable.style.display = "table";
                        renderTable(result.data);
                    } else {
                        showMessage(result.message, false);
                    }
                })
                .catch(function(err) {
                    loadingIndicator.style.display = "none";
                    showMessage("API error: " . err.message, false);
                });
        }

        function renderTable(events) {
            tableBody.innerHTML = "";
            if (!events || events.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" align="center">No events found. Add one above.</td></tr>';
                return;
            }

            events.forEach(function(ev, idx) {
                const tr = document.createElement("tr");
                tr.id = "row-" + ev.id;
                tr.innerHTML = `
                    <td>${idx + 1}</td>
                    <td><strong>${escapeHtml(ev.title)}</strong><br><span style="font-size: 0.8rem; color: #64748b;">${escapeHtml(ev.category)}</span></td>
                    <td>${escapeHtml(ev.description)}</td>
                    <td>${escapeHtml(ev.event_date)}<br><span style="font-size: 0.8rem; color: #64748b;">${escapeHtml(ev.venue)}</span></td>
                    <td>
                        <button class="btn" style="padding: 4px 10px; font-size: 0.8rem; background: #dc2626;" onclick="deleteEvent(${ev.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        function escapeHtml(text) {
            if (!text) return "";
            return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        ajaxForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const payload = {
                title: document.getElementById("evTitle").value.trim(),
                category: document.getElementById("evCategory").value,
                event_date: document.getElementById("evDate").value,
                venue: document.getElementById("evVenue").value.trim(),
                description: document.getElementById("evDesc").value.trim()
            };

            fetch("api.php?action=add", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(result) {
                if (result.success) {
                    showMessage(result.message, true);
                    ajaxForm.reset();
                    loadEvents();
                } else {
                    showMessage(result.message, false);
                }
            })
            .catch(function(err) {
                showMessage("Submission failed: " + err.message, false);
            });
        });

        function deleteEvent(id) {
            if (!confirm("Are you sure you want to delete this event via AJAX?")) return;

            fetch("api.php?action=delete", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: id })
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(result) {
                if (result.success) {
                    showMessage(result.message, true);
                    const row = document.getElementById("row-" + id);
                    if (row) row.remove();
                } else {
                    showMessage(result.message, false);
                }
            })
            .catch(function(err) {
                showMessage("Delete request failed: " + err.message, false);
            });
        }

        refreshBtn.addEventListener("click", loadEvents);
        loadEvents();
    </script>
</body>
</html>
