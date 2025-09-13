<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxBay Integration Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        button:hover {
            background: #0056b3;
        }
        button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .response {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            background: #e9ecef;
            border: 1px solid #ddd;
            cursor: pointer;
            border-bottom: none;
        }
        .tab.active {
            background: white;
            border-bottom: 1px solid white;
            margin-bottom: -1px;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .call-log {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .call-log .status {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status.initiated { background: #fff3cd; color: #856404; }
        .status.answered { background: #d1ecf1; color: #0c5460; }
        .status.completed { background: #d4edda; color: #155724; }
        .status.failed { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>VoxBay Integration Test Interface</h1>
    
    <div class="tabs">
        <div class="tab active" onclick="showTab('calls')">Calls</div>
        <div class="tab" onclick="showTab('sms')">SMS</div>
        <div class="tab" onclick="showTab('logs')">Call Logs</div>
        <div class="tab" onclick="showTab('balance')">Balance</div>
    </div>

    <!-- Calls Tab -->
    <div id="calls" class="tab-content active">
        <div class="container">
            <h2>Make Call</h2>
            <form id="makeCallForm">
                <div class="form-group">
                    <label for="callFrom">From Number:</label>
                    <input type="tel" id="callFrom" placeholder="+1234567890" required>
                </div>
                <div class="form-group">
                    <label for="callTo">To Number:</label>
                    <input type="tel" id="callTo" placeholder="+0987654321" required>
                </div>
                <button type="submit">Make Call</button>
            </form>
            <div id="callResponse" class="response" style="display: none;"></div>
        </div>

        <div class="container">
            <h2>Click to Call</h2>
            <form id="clickToCallForm">
                <div class="form-group">
                    <label for="agentNumber">Agent Number:</label>
                    <input type="tel" id="agentNumber" placeholder="+1234567890" required>
                </div>
                <div class="form-group">
                    <label for="customerNumber">Customer Number:</label>
                    <input type="tel" id="customerNumber" placeholder="+0987654321" required>
                </div>
                <div class="form-group">
                    <label for="callerId">Caller ID (Optional):</label>
                    <input type="tel" id="callerId" placeholder="+1234567890">
                </div>
                <button type="submit">Click to Call</button>
            </form>
            <div id="clickToCallResponse" class="response" style="display: none;"></div>
        </div>
    </div>

    <!-- SMS Tab -->
    <div id="sms" class="tab-content">
        <div class="container">
            <h2>Send SMS</h2>
            <form id="smsForm">
                <div class="form-group">
                    <label for="smsFrom">From Number:</label>
                    <input type="tel" id="smsFrom" placeholder="+1234567890" required>
                </div>
                <div class="form-group">
                    <label for="smsTo">To Number:</label>
                    <input type="tel" id="smsTo" placeholder="+0987654321" required>
                </div>
                <div class="form-group">
                    <label for="smsMessage">Message:</label>
                    <textarea id="smsMessage" placeholder="Your message here..." required maxlength="1600"></textarea>
                </div>
                <button type="submit">Send SMS</button>
            </form>
            <div id="smsResponse" class="response" style="display: none;"></div>
        </div>
    </div>

    <!-- Call Logs Tab -->
    <div id="logs" class="tab-content">
        <div class="container">
            <h2>Call Logs</h2>
            <div style="margin-bottom: 15px;">
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate">
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate">
                <button onclick="loadCallLogs()">Load Logs</button>
                <button onclick="refreshLogs()">Refresh</button>
            </div>
            <div id="callLogs">
                <p>Click "Load Logs" to fetch call logs...</p>
            </div>
        </div>
    </div>

    <!-- Balance Tab -->
    <div id="balance" class="tab-content">
        <div class="container">
            <h2>Account Balance</h2>
            <button onclick="checkBalance()">Check Balance</button>
            <div id="balanceResponse" class="response" style="display: none;"></div>
        </div>
    </div>

    <script>
        // VoxBay Client Class
        class VoxBayClient {
            constructor(baseUrl) {
                this.baseUrl = baseUrl || window.location.origin;
            }

            async makeRequest(endpoint, options = {}) {
                try {
                    const response = await fetch(`${this.baseUrl}/voxbay/${endpoint}`, {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...options.headers
                        },
                        ...options
                    });

                    const result = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(result.messages?.error || result.error || 'Request failed');
                    }

                    return result;
                } catch (error) {
                    console.error('API Error:', error);
                    throw error;
                }
            }

            async makeCall(fromNumber, toNumber, options = {}) {
                return this.makeRequest('calls', {
                    method: 'POST',
                    body: JSON.stringify({
                        from: fromNumber,
                        to: toNumber,
                        options: options
                    })
                });
            }

            async clickToCall(agentNumber, customerNumber, callerId = null) {
                return this.makeRequest('click-to-call', {
                    method: 'POST',
                    body: JSON.stringify({
                        agent_number: agentNumber,
                        customer_number: customerNumber,
                        caller_id: callerId
                    })
                });
            }

            async sendSMS(fromNumber, toNumber, message) {
                return this.makeRequest('sms', {
                    method: 'POST',
                    body: JSON.stringify({
                        from: fromNumber,
                        to: toNumber,
                        message: message
                    })
                });
            }

            async getCallLogs(filters = {}) {
                const params = new URLSearchParams(filters);
                return this.makeRequest(`calls?${params}`);
            }

            async getBalance() {
                return this.makeRequest('balance');
            }
        }

        // Initialize VoxBay client
        const voxbay = new VoxBayClient();

        // Tab functionality
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        // Helper functions
        function showResponse(elementId, data, isError = false) {
            const element = document.getElementById(elementId);
            element.style.display = 'block';
            element.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
            element.className = `response ${isError ? 'error' : 'success'}`;
        }

        function setButtonLoading(button, loading = true) {
            if (loading) {
                button.disabled = true;
                button.dataset.originalText = button.textContent;
                button.textContent = 'Processing...';
            } else {
                button.disabled = false;
                button.textContent = button.dataset.originalText;
            }
        }

        // Make Call Form
        document.getElementById('makeCallForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = e.target.querySelector('button');
            
            try {
                setButtonLoading(button);
                
                const result = await voxbay.makeCall(
                    document.getElementById('callFrom').value,
                    document.getElementById('callTo').value
                );
                
                showResponse('callResponse', result);
            } catch (error) {
                showResponse('callResponse', { error: error.message }, true);
            } finally {
                setButtonLoading(button, false);
            }
        });

        // Click to Call Form
        document.getElementById('clickToCallForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = e.target.querySelector('button');
            
            try {
                setButtonLoading(button);
                
                const result = await voxbay.clickToCall(
                    document.getElementById('agentNumber').value,
                    document.getElementById('customerNumber').value,
                    document.getElementById('callerId').value || null
                );
                
                showResponse('clickToCallResponse', result);
            } catch (error) {
                showResponse('clickToCallResponse', { error: error.message }, true);
            } finally {
                setButtonLoading(button, false);
            }
        });

        // SMS Form
        document.getElementById('smsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = e.target.querySelector('button');
            
            try {
                setButtonLoading(button);
                
                const result = await voxbay.sendSMS(
                    document.getElementById('smsFrom').value,
                    document.getElementById('smsTo').value,
                    document.getElementById('smsMessage').value
                );
                
                showResponse('smsResponse', result);
            } catch (error) {
                showResponse('smsResponse', { error: error.message }, true);
            } finally {
                setButtonLoading(button, false);
            }
        });

        // Load Call Logs
        async function loadCallLogs() {
            try {
                const filters = {};
                
                if (document.getElementById('startDate').value) {
                    filters.start_date = document.getElementById('startDate').value;
                }
                
                if (document.getElementById('endDate').value) {
                    filters.end_date = document.getElementById('endDate').value;
                }
                
                const result = await voxbay.getCallLogs(filters);
                displayCallLogs(result.data || result);
                
            } catch (error) {
                document.getElementById('callLogs').innerHTML = `
                    <div class="error">Error loading call logs: ${error.message}</div>
                `;
            }
        }

        function displayCallLogs(logs) {
            const container = document.getElementById('callLogs');
            
            if (!logs || logs.length === 0) {
                container.innerHTML = '<p>No call logs found.</p>';
                return;
            }
            
            container.innerHTML = logs.map(log => `
                <div class="call-log">
                    <div>
                        <strong>Call ID:</strong> ${log.call_id || 'N/A'} 
                        <span class="status ${log.status}">${log.status}</span>
                    </div>
                    <div><strong>From:</strong> ${log.from_number}</div>
                    <div><strong>To:</strong> ${log.to_number}</div>
                    <div><strong>Duration:</strong> ${log.duration || 0} seconds</div>
                    <div><strong>Created:</strong> ${log.created_at}</div>
                    ${log.recording_url ? `<div><strong>Recording:</strong> <a href="${log.recording_url}" target="_blank">Listen</a></div>` : ''}
                </div>
            `).join('');
        }

        function refreshLogs() {
            loadCallLogs();
        }

        // Check Balance
        async function checkBalance() {
            try {
                const result = await voxbay.getBalance();
                showResponse('balanceResponse', result);
            } catch (error) {
                showResponse('balanceResponse', { error: error.message }, true);
            }
        }

        // Set default dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            
            document.getElementById('endDate').value = today.toISOString().split('T')[0];
            document.getElementById('startDate').value = weekAgo.toISOString().split('T')[0];
        });

        // Real-time call status updates (if WebSocket is available)
        function setupRealTimeUpdates() {
            // This would connect to a WebSocket for real-time updates
            // Implementation depends on your WebSocket setup
            console.log('Real-time updates would be setup here');
        }

        // Auto-refresh call logs every 30 seconds when on logs tab
        setInterval(() => {
            if (document.getElementById('logs').classList.contains('active')) {
                loadCallLogs();
            }
        }, 30000);
    </script>
</body>
</html>