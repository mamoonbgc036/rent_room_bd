<div>
    <div style="height: 300px;">
        @php
            $chartId = 'revenue-chart-' . uniqid();
        @endphp
        <div id="{{ $chartId }}" style="width: 100%; height: 100%;"></div>
    </div>

    <script>
        document.addEventListener('livewire:load', function() {
            const chartData = @json($chartData);

            const options = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'Rent Revenue',
                    data: chartData.map(item => item.rent)
                }, {
                    name: 'Booking Revenue',
                    data: chartData.map(item => item.booking)
                }],
                xaxis: {
                    categories: chartData.map(item => item.date),
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return '৳' + value.toFixed(2);
                        }
                    }
                },
                colors: ['#4e73df', '#1cc88a'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return '৳' + value.toFixed(2);
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };

            const chart = new ApexCharts(document.querySelector('#{{ $chartId }}'), options);
            chart.render();

            // Clean up on component refresh
            Livewire.on('chartUpdated', (newData) => {
                chart.updateOptions({
                    series: [{
                        name: 'Rent Revenue',
                        data: newData.map(item => item.rent)
                    }, {
                        name: 'Booking Revenue',
                        data: newData.map(item => item.booking)
                    }],
                    xaxis: {
                        categories: newData.map(item => item.date)
                    }
                });
            });
        });
    </script>
</div>
