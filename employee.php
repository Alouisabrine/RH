<?php
// connexion à la base de données
$conn = new mysqli("localhost", "root", "", "rh_dashboard");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Nombre d'employés par département
$sql_dep = "SELECT department, COUNT(*) AS total FROM employees GROUP BY department";
$result_dep = $conn->query($sql_dep);
$dep_data = [];
$dep_labels = [];
while($row = $result_dep->fetch_assoc()){
    $dep_labels[] = $row['department'];
    $dep_data[] = $row['total'];
}

// Distribution des salaires
$sql_salary = "SELECT salary FROM employees";
$result_salary = $conn->query($sql_salary);
$salary_data = [];
while($row = $result_salary->fetch_assoc()){
    $salary_data[] = $row['salary'];
}

// Recrutements par année
$sql_hire = "SELECT YEAR(hire_date) AS year, COUNT(*) AS total FROM employees GROUP BY YEAR(hire_date)";
$result_hire = $conn->query($sql_hire);
$hire_labels = [];
$hire_data = [];
while($row = $result_hire->fetch_assoc()){
    $hire_labels[] = $row['year'];
    $hire_data[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard RH Simple & Attractif</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}
.kpi-card {
    border-radius: 12px;
    padding: 20px;
    color: white;
    text-align: center;
    transition: transform 0.2s;
}
.kpi-card:hover { transform: scale(1.05); }
.bg-employes { background-color: #0d6efd; }
.bg-salaire { background-color: #198754; }
.bg-it { background-color: #ffc107; color: #000; }
.bg-recrut { background-color: #dc3545; }
.card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 15px; }
h2, h5 { font-weight: 600; color: #333; }
</style>
</head>
<body>
<div class="container mt-4">

    <div class="text-center mb-4">
        <h2>Dashboard RH</h2>
        <p class="text-muted">Vue rapide des employés et salaires</p>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card bg-employes">
                <h5>Total Employés</h5>
                <h2 id="totalEmp">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card bg-salaire">
                <h5>Salaire Moyen</h5>
                <h2 id="avgSalary">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card bg-it">
                <h5>Employés IT</h5>
                <h2 id="itEmp">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card bg-recrut">
                <h5>Nouveaux Recrutements</h5>
                <h2 id="newHire">0</h2>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <h5 class="mb-2 text-center">Répartition par Département</h5>
                <canvas id="depChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <h5 class="mb-2 text-center">Distribution des Salaires</h5>
                <canvas id="salaryChart"></canvas>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <h5 class="mb-2 text-center">Recrutements par Année</h5>
                <canvas id="hireChart"></canvas>
            </div>
        </div>
    </div>

</div>

<script>
const depLabels = ['IT', 'HR', 'Finance', 'Marketing'];
const depData = [12, 7, 5, 3];
const hireLabels = ['2021','2022','2023'];
const hireData = [4, 10, 13];
const salaryData = [3500, 3000, 4200, 3700];

// KPI dynamiques
document.getElementById('totalEmp').innerText = depData.reduce((a,b)=>a+b,0);
document.getElementById('avgSalary').innerText = Math.round(salaryData.reduce((a,b)=>a+b,0)/salaryData.length);
document.getElementById('itEmp').innerText = depData[0];
document.getElementById('newHire').innerText = hireData[hireData.length-1];

// Charts
new Chart(document.getElementById('depChart'), {
    type: 'doughnut',
    data: { labels: depLabels, datasets:[{ data: depData, backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545'] }] },
    options: { responsive:true, plugins:{ legend:{ position:'bottom' } } }
});

new Chart(document.getElementById('hireChart'), {
    type: 'line',
    data: { labels: hireLabels, datasets:[{ label:'Recrutements', data: hireData, borderColor:'#0d6efd', backgroundColor:'rgba(13,110,253,0.2)', fill:true, tension:0.3 }] },
    options: { responsive:true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
});

new Chart(document.getElementById('salaryChart'), {
    type: 'bar',
    data: { labels: depLabels, datasets:[{ label:'Salaire', data: salaryData, backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545'] }] },
    options: { responsive:true, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
});
</script>

</body>
</html>