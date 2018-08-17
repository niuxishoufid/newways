<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Google chart</title>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        function init() {
            google.load('visualization', '1.1', {'packages': ['corechart'], callback: drawCharts});
        }

        function drawCharts() {
            drawSampleChart('piechart');
        }

        function drawSampleChart(containerId) {
            var data = google.visualization.arrayToDataTable([
                ['Taste', 'Range per Pizza'],
                ['BBQChicken', 25],
                ['Charcoal-grilled Beef Rib', 25],
                ['Kokuuma Just Meat', 25],
                ['Shrimp Mayo Bacon', 25],
            ]);

            var options = {
                title: 'Would you like to eat a pizza?'
            };

            var chart = new google.visualization.PieChart(document.getElementById(containerId));
            chart.draw(data, options);
        }
    </script>
</head>
<body onload="init()">
    <div id="piechart" style="width: 900px; height: 500px;"></div>
</body>
</html>
