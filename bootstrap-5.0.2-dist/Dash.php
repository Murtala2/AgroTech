<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: bootstrap-5.0.2-dist/Welcome.html");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Irrigation Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('Farmer.jpg') no-repeat center center fixed;
            background-size: cover;
            animation: moveBackground 20s linear infinite alternate;
        }
        @keyframes moveBackground {
            0% { background-position: center top; }
            100% { background-position: center bottom; }
        }
        .gradient-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(34, 193, 195, 0.3), rgba(0, 255, 47, 0.3));
            z-index: 0;
        }
        .content {
            position: relative;
            z-index: 1;
        }
        .card {
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
            padding: 20px;
            margin: 15px 0;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .dashboard-title {
            color: rgb(0, 255, 47);
        }
        .thermometer-bar {
            height: 100px;
            width: 30px;
            background: #e0e0e0;
            border-radius: 15px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .thermometer-fill {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #f72213;
            border-radius: 15px;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2;
        }
        .popup {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: 100px auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="gradient-overlay"></div>
    <div class="content container mt-3">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="dashboard-title">🌾 AgroTech Dashboard, <?php echo $_SESSION['email']; ?>!</h2>
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" id="userSettingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    ⚙️ User Settings
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userSettingsDropdown">
                    <li><a class="dropdown-item" href="#" onclick="showDashboard()">🏠 Home</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showReport()">📊 Report</a></li>
                    <li><a class="dropdown-item" href="logout.php">🚪 Logout</a></li>
                </ul>
            </div>
        </div>
        <div id="dashboard">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <h5>🌱 Soil Moisture</h5>
                        <canvas id="soilMoistureGauge"></canvas>
                        <div id="soilMoistureValue">45%</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <h5>🌡️ Temperature</h5>
                        <div class="thermometer-bar">
                            <div id="temperatureFill" class="thermometer-fill" style="height: 60%;"></div>
                        </div>
                        <div id="temperatureValue">30°C</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <h5>💧 Humidity</h5>
                        <canvas id="humidityGauge"></canvas>
                        <div id="humidityValue">60%</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card text-center">
                        <h5>🩺 Plant Health Status</h5>
                        <div class="sensor-value" id="plantHealth">Monitoring...</div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <h5>Pump Control</h5>
                        <button class="btn btn-success w-100 mb-2" onclick="controlPump('ON')">Turn Pump ON</button>
                        <button class="btn btn-danger w-100" onclick="controlPump('OFF')">Turn Pump OFF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="popup-overlay" id="reportPopup">
        <div class="popup">
            <h4>Plant Health Report</h4>
            <canvas id="reportChart"></canvas>
            <button class="btn btn-danger mt-3" onclick="closeReport()">Close</button>
        </div>
    </div>
<script>
    function createGauge(canvasId, value, max = 100) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [value, max - value],
                    backgroundColor: ['#22f713', '#e0e0e0'],
                    borderWidth: 0,
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }
    createGauge('soilMoistureGauge', 45);
    createGauge('humidityGauge', 60);

    function showReport() {
        document.getElementById('reportPopup').style.display = 'block';
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Soil Moisture', 'Temperature', 'Humidity'],
                datasets: [{
                    label: 'Plant Health Metrics',
                    data: [45, 30, 60],
                    backgroundColor: ['#22f713', '#f72213', '#13a9f7']
                }]
            }
        });
    }

    function closeReport() {
        document.getElementById('reportPopup').style.display = 'none';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
