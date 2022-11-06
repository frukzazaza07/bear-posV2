<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/bear/css/dailysale-report.css">
    <link rel="stylesheet" href="/bear/lib/jquery/js/datepicker/jq-datepicker.css">
    <link rel="stylesheet" href="/bear/lib/datatable-bootstrap5/dataTables.bootstrap5.min.css">
</head>

<body>
    <link href="/bear/lib/bootstrap-5.0.1-dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="/bear/lib/fontawesome-free-5.15.3-web/css/all.css" rel="stylesheet">
    <link href="/bear/lib/sweetalert2/sweetalert2.css" rel="stylesheet">
    <script src="/bear/lib/bootstrap-5.0.1-dist/js/bootstrap.bundle.min.js"></script>
    <script src="/bear/lib/bootstrap-5.0.1-dist/js/bootstrap.min.js"></script>
    <script src="/bear/lib/axios/axios.min.js"></script>
    <script src="/bear/lib/sweetalert2/sweetalert2.min.js"></script>
    <script src="/bear/lib/jquery/js/jquery.min.js"></script>
    <div style="max-width: 500px; max-height: 500px">
        <canvas id="myChart" width="100%" height="100%"></canvas>
    </div>
    <script src="/bear/js/customValidation.js"></script>
    <!-- datepicker thai -->
    <script src="/bear/lib/jquery/js/datepicker/jq-datepicker.js"></script>
    <script src="/bear/lib/jquery/js/datepicker/jq-datepicker.th-TH.js"></script>
    <!-- datatable -->
    <script src="/bear/lib/datatable-bootstrap5/dataTables.bootstrap5.min.js"></script>
    <script src="/bear/lib/datatable-bootstrap5/jquery.dataTables.min.js"></script>
    <script src="/bear/lib/chart.js/Chart.min.js"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>