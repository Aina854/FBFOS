@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Sales Dashboard</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="row mt-4">
                <div class="col-md-12">
                    <canvas id="dailySalesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
        @include('partials.salescard')
        </div>
    </div>
    <div class="container">
    <h4 class="text-center my-3">
        <span style="font-size: 0.8em; color: #403f3e;">Daily Sales Summary</span> - 
        <span style="color: #007bff; font-size: 0.8em;">({{ now()->format('l, F j, Y') }})</span>
    </h4>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="text-center">Menu ID</th>
                <th class="text-center">Menu Name</th>
                <th class="text-center">Price per Unit (RM)</th>
                <th class="text-center">Quantity Sold</th>
                <th class="text-center">Total Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Revenue Sum (RM):</th>
                <th id="totalRevenueSum" class="text-center">{{ number_format($totalRevenueSum, 2) }}</th>
            </tr>
        </tfoot>

            @forelse ($dailySalesSummary as $item)
                <tr>
                    <td class="text-center">{{ $item->menuId }}</td>
                    <td class="text-center">{{ $item->menuName }}</td>
                    <td class="text-center">{{ number_format($item->pricePerUnit, 2) }}</td>
                    <td class="text-center">{{ $item->quantitySold }}</td>
                    <td class="text-center">{{ number_format($item->totalRevenue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No sales data available for today.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Prepare data for the chart
    const orderDates = @json($orderDates);
    const dailyTotals = @json($dailyTotals);

    // Set up Chart.js for daily sales
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: orderDates,
            datasets: [{
                label: 'Daily Sales (Last 7 Days)',
                data: dailyTotals,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',  // X-axis label
                        font: {
                            weight: 'bold'  // Make the x-axis label bold
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Sales Amount (RM)',  // Y-axis label
                        font: {
                            weight: 'bold'  // Make the y-axis label bold
                        }
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection
