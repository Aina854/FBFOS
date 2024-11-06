@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Sales Dashboard</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="row mt-4">
                <div class="col-md-12">
                    <canvas id="weeklySalesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @include('partials.salescard') <!-- Keep this for sales card -->
        </div>
    </div>

    <div class="container">
    @php
        // Set the locale to use Sunday as the first day of the week
        $startOfWeek = now()->startOfWeek(Carbon\Carbon::SUNDAY)->format('F j, Y');
        $endOfWeek = now()->endOfWeek(Carbon\Carbon::SATURDAY)->format('F j, Y');
    @endphp

    <h4 class="text-center my-3">
        <span style="font-size: 0.8em; color: #403f3e;">Weekly Sales Summary</span> - 
        <span style="color: #007bff; font-size: 0.8em;">({{ $startOfWeek }} - {{ $endOfWeek }})</span>
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
                @php
                    $currentDay = null;
                @endphp

                @forelse ($weeklySalesSummary as $item)
                    @if ($currentDay !== $item->orderDate)
                        @if ($currentDay !== null)
                            <tr>
                            <th colspan="4" class="text-right">Total (RM):</th>
                            <th class="text-center">{{ number_format($dailyTotals[$currentDay] ?? 0, 2) }} </th>
                            </tr>
                        @endif
                        @php
                            $currentDay = $item->orderDate;
                            $dayName = \Carbon\Carbon::parse($currentDay)->format('l'); // Get the day name
                        @endphp
                        <tr>
                            <td colspan="5" class="text-center font-weight-bold">{{ $dayName }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-center">{{ $item->menuId }}</td>
                        <td class="text-center">{{ $item->menuName }}</td>
                        <td class="text-center">{{ number_format($item->pricePerUnit, 2) }}</td>
                        <td class="text-center">{{ $item->quantitySold }}</td>
                        <td class="text-center">{{ number_format($item->totalRevenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No sales data available for this week.</td>
                    </tr>
                @endforelse

                @if ($currentDay !== null)
                    <tr>
                        
                        <th colspan="4" class="text-right">Total (RM):</th>
                        <th class="text-center">{{ number_format($dailyTotals[$currentDay] ?? 0, 2) }} </th>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total Revenue Sum (RM):</th>
                    <th id="totalRevenueSum" class="text-center">{{ number_format($totalRevenueSum, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for the chart
    const orderDates = @json($orderDates);
    const weeklyTotals = @json($weeklyTotals); // Update to reflect weekly totals

    // Set up Chart.js for weekly sales
    const ctx = document.getElementById('weeklySalesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: orderDates,
            datasets: [{
                label: 'Weekly Sales (Last 7 Weeks)', // Update label
                data: weeklyTotals,
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
                        text: 'Week',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Sales Amount (RM)',
                        font: {
                            weight: 'bold'
                        }
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection
