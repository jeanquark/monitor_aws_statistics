@extends('layoutBack')

@section('css')

@stop

@section('content')
    <div class="well">
        <h3 class="">Amazon Web Services - Virtual servers performance</h3>
        <div class="row">
            <h4 style="margin-left: 15px;"> CPU Utilization Statistics for EC2 instance {{ $cpu[0]['Value'] }}</h4>
            <div class="col-md-12">
                <div id="chart_cpu_util"></div>
            </div>
        </div>
        <hr>
        <div class="row">
            <h4 style="margin-left: 15px;">DB Utilization Statistics for RDS instance {{ $rds[0]['Value'] }}</h4>
            <div class="col-md-12">
                <div id="chart_rds_util"></div>
            </div>
        </div>
    </div><!-- /.well -->
@stop

@section('scripts')
    <script>
        var cpu = <?php 
            echo json_encode($cpu_util);
        ?>;
        var rds = <?php 
            echo json_encode($rds_util);
        ?>;
    </script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {packages: ['corechart', 'line']});
        google.charts.setOnLoadCallback(cpu_util);
        google.charts.setOnLoadCallback(rds_util);


        // CPU Utilization graph 
        function cpu_util() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'X');
            data.addColumn('number');


            function compare(a,b) {
              if (a.Timestamp < b.Timestamp)
                return -1;
              else if (a.Timestamp > b.Timestamp)
                return 1;
              else 
                return 0;
            }

            cpu.sort(compare);

            for(i = 0; i < cpu.length; i++) {
                var date = Date.parse(cpu[i].Timestamp);

                var t = new Date(date);
                var monthNames = [
                  "January", "February", "March",
                  "April", "May", "June", "July",
                  "August", "September", "October",
                  "November", "December"
                ];
                function addZero(i) {
                    if (i < 10) {
                        i = "0" + i;
                    }
                    return i;
                }
                var day = t.getDate();
                var monthIndex = t.getMonth();
                var year = t.getFullYear();
                var hour = addZero(t.getHours());
                var min = addZero(t.getMinutes());
                var date = String((day + ' ' + monthNames[monthIndex] + ' at ' + hour + ':' + min));

                data.addRow([date, parseFloat(cpu[i].Maximum)])
            };

            var options = {
                title: 'CPU Utilization (%)',
                //titlePosition: 'none',
                legend: {position: 'none'},
                //width: 1600,
                height: 400,
                hAxis: {
                    title: 'Last day span',
                    textStyle: {fontSize: 8}
                },
                vAxis: {
                    title: 'CPU Utilization (%)'
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_cpu_util'));

            chart.draw(data, options);
        }

        // Database CPU Utilization graph
        function rds_util() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'X');
            data.addColumn('number');


            function compare(a,b) {
              if (a.Timestamp < b.Timestamp)
                return -1;
              else if (a.Timestamp > b.Timestamp)
                return 1;
              else 
                return 0;
            }

            rds.sort(compare);

            for(i = 0; i < rds.length; i++) {
                var date = Date.parse(rds[i].Timestamp);

                var t = new Date(date);
                var monthNames = [
                  "January", "February", "March",
                  "April", "May", "June", "July",
                  "August", "September", "October",
                  "November", "December"
                ];
                function addZero(i) {
                    if (i < 10) {
                        i = "0" + i;
                    }
                    return i;
                }
                var day = t.getDate();
                var monthIndex = t.getMonth();
                var year = t.getFullYear();
                var hour = addZero(t.getHours());
                var min = addZero(t.getMinutes());
                var date = String((day + ' ' + monthNames[monthIndex] + ' at ' + hour + ':' + min));

                data.addRow([date, parseFloat(rds[i].Maximum)])
            };

            var options = {
                title: 'Database CPU Utilization (%)',
                legend: {position: 'none'},
                //width: 1600,
                height: 400,
                hAxis: {
                    title: 'Last day span',
                    textStyle: {fontSize: 8}
                },
                vAxis: {
                    title: 'DB CPU Utilization (%)'
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_rds_util'));

            chart.draw(data, options);
        }
    </script>
@stop

