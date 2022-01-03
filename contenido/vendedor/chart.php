<html>
  <head>

  </head>
  <body>
    <div id="chart_div" style="height:700px;"></div>
  </body>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
     
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['% Ventas', 70]
        ]);

        var options = {
        /*  width: 600, height: 400,*/
          redFrom: 0, redTo: 80,
          yellowFrom:80, yellowTo: 90,
          greenFrom:90, greenTo: 100,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
        chart.draw(data, options);

        /*  
          setInterval(function() {
            data.setValue(0, 1, 15);
            chart.draw(data, options);
          }, 10000);
        */

      }
    </script>

</html>