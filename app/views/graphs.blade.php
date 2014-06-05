@extends('site.layouts.default')

@section('title')
@parent
:: Results
@stop

@section('content')
<div id="container" style="width:100%; height:1000px;"></div>
<script type="text/javascript">
        $.get('http://cpanel.ls.pe/servers.csv', function(data) {
            var options = {
                chart: {
                    renderTo: 'container',
                    defaultSeriesType: 'areaspline'
                },
                title: {
                    text: 'Servers'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr']
                },
                yAxis: {
                    title: {
                        text: 'Units'
                    }
                },
                series: []
            };
            // Split the lines
            var lines = data.split('\n');

            // Iterate over the lines and add categories or series
            $.each(lines, function(lineNo, line) {
                var items = line.split(',');

                // header line containes categories
                if (lineNo == 0) {
                    $.each(items, function(itemNo, item) {
                        if (itemNo > 0)
                            options.xAxis.categories.push(item);
                    });
                }

                // the rest of the lines contain data with their name in the first 
                // position
                else {
                    var series = {
                        data: [1]
                    };
                    $.each(items, function(itemNo, item) {
                        if (itemNo == 0) {
                            series.name = item;
                        } else {
                            series.data.push(parseFloat(item));
                        }
                    });

                    options.series.push(series);

                }

            });

            // Create the chart
            var chart = new Highcharts.Chart(options);
        });
</script>
@stop