@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Sales Dashboard</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="row mt-4">
                <div class="col-md-12">
                    <canvas id="yearlySalesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @include('partials.salescard') <!-- Keep this for sales card -->
        </div>
    </div>

    <div class="container">
        @php
            // Get the current year and the start and end of the year
            $startOfYear = now()->startOfYear()->format('F j, Y');
            $endOfYear = now()->endOfYear()->format('F j, Y');
        @endphp

        <h4 class="text-center my-3">
            <span style="font-size: 0.8em; color: #403f3e;">Yearly Sales Summary</span> - 
            <span style="color: #007bff; font-size: 0.8em;">({{ $startOfYear }} - {{ $endOfYear }})</span>
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
                    $currentMonth = null;
                @endphp

                @forelse ($yearlySalesSummary as $item) <!-- Changed to yearlySalesSummary -->
                    @if ($currentMonth !== $item->orderMonth)
                        @if ($currentMonth !== null)
                            <tr>
                                <th colspan="4" class="text-right">Total (RM):</th>
                                <th class="text-center">{{ number_format($monthlyTotals[$currentMonth] ?? 0, 2) }}</th>
                            </tr>
                        @endif
                        @php
                            $currentMonth = $item->orderMonth;
                            $monthName = \Carbon\Carbon::parse($currentMonth)->format('F Y'); // Get the month name
                        @endphp
                        <tr>
                            <td colspan="5" class="text-center font-weight-bold">{{ $monthName }}</td>
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
                        <td colspan="5" class="text-center">No sales data available for this year.</td>
                    </tr>
                @endforelse

                @if ($currentMonth !== null)
                    <tr>
                        <th colspan="4" class="text-right">Total (RM):</th>
                        <th class="text-center">{{ number_format($monthlyTotals[$currentMonth] ?? 0, 2) }}</th>
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
        const orderYears = @json($orderYears);
        const yearlyTotals = @json($yearlyTotals);

        // Set up Chart.js for yearly sales
        const ctx = document.getElementById('yearlySalesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // Change to 'bar' for better visibility of yearly data
            data: {
                labels: orderYears,
                datasets: [{
                    label: 'Yearly Sales (Last 3 Years)', // Update label
                    data: yearlyTotals,
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
                            text: 'Year',
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
