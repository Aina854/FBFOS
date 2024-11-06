<div class="row">
     <!-- Daily Sales Card -->
     <div class="col-md-6">
     <a href="{{ route('dailySalesData') }}" class="card text-decoration-none">
     <div class="card-body">
        <strong class="card-title" style="color: #007bff;">Daily Sales</strong> 
        <span style="font-size: 0.6em; color: #6c757d;">Today</span>
        <h3 id="todaySalesAmount" style="color: #28a745;">RM {{ number_format((float)$todaySales, 2) }}</h3>
        <p style="font-size: 0.8em;" id="salesChange">
            <i class="fas fa-arrow-{{ $todayChange >= 0 ? 'up' : 'down' }} text-{{ $todayChange >= 0 ? 'success' : 'danger' }}"></i>
            {{ number_format((float) $todayChange, 2) }}% from yesterday
        </p>
     </div>
    </a>

    </div>
    


     <!-- Weekly Sales Card -->
     <div class="col-md-6">
     <a href="{{ route('weeklySalesData') }}" class="card text-decoration-none">
            <div class="card-body">
                <strong class="card-title" style=" color: #007bff;">Weekly Sales</strong> 
                <span style="font-size: 0.6em; color: #6c757d;">This Week</span>
                <h3 id="todaySalesAmount" style=" color: #28a745;" >RM {{ number_format((float)$weeklySales, 2) }}</h3>
                <p style="font-size: 0.8em;" id="salesChange">
                    <i class="fas fa-arrow-{{ $weeklyChange >= 0 ? 'up' : 'down' }} text-{{ $weeklyChange >= 0 ? 'success' : 'danger' }}"></i>
                    {{ number_format((float) $weeklyChange, 2) }}% from last week
                </p>
            </div>
            </a>
     </div>
 </div>
 <div class="row">
    <!-- Monthly Sales Card -->
    <div class="col-md-6">
    <a href="{{ route('monthlySalesData') }}" class="card text-decoration-none">
            <div class="card-body">
                <strong class="card-title" style=" color: #007bff;">Monthly Sales</strong> 
                <span style="font-size: 0.6em; color: #6c757d;">This Month</span>
                <h3 id="todaySalesAmount" style=" color: #28a745;" >RM {{ number_format((float)$monthlySales, 2) }}</h3>
                <p style="font-size: 0.8em;" id="salesChange">
                    <i class="fas fa-arrow-{{ $monthlyChange >= 0 ? 'up' : 'down' }} text-{{ $monthlyChange >= 0 ? 'success' : 'danger' }}"></i>
                    {{ number_format((float)$monthlyChange, 2) }}% from last month
                </p>
            </div>
        
        </a>
    </div>

    <!-- Yearly Sales Card -->
    <div class="col-md-6">
    <a href="{{ route('yearlySalesData') }}" class="card text-decoration-none">
            <div class="card-body">
                <strong class="card-title" style=" color: #007bff;">Yearly Sales</strong> 
                <span style="font-size: 0.6em; color: #6c757d;">This Year</span>
                <h3 id="todaySalesAmount" style=" color: #28a745;" >RM {{ number_format((float)$yearlySales, 2) }}</h3>
                <p style="font-size: 0.8em;" id="salesChange">
                    <i class="fas fa-arrow-{{ $yearlyChange >= 0 ? 'up' : 'down' }} text-{{ $yearlyChange >= 0 ? 'success' : 'danger' }}"></i>
                    {{ number_format((float)$yearlyChange, 2) }}% from last year
                </p>
            </div> 
        </a>
    </div>
</div>