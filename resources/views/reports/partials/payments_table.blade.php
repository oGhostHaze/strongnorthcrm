<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Payment ID</th>
            <th>Order Number</th>
            <th>Client</th>
            <th>Payment Date</th>
            <th>Payment Mode</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Reference No.</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $payment)
            <tr>
                <td>{{ $payment['payment_id'] }}</td>
                <td>{{ $payment['order_number'] }}</td>
                <td>{{ $payment['client'] }}</td>
                <td>{{ $payment['payment_date'] ? \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y') : '-' }}
                </td>
                <td>{{ $payment['payment_mode'] }}</td>
                <td class="text-right">₱{{ number_format($payment['amount'], 2) }}</td>
                <td>
                    <span
                        class="badge bg-{{ $payment['status'] == 'Posted' ? 'success' : ($payment['status'] == 'Unposted' ? 'warning' : 'danger') }}">
                        {{ $payment['status'] }}
                    </span>
                </td>
                <td>{{ $payment['reference_no'] ?: '-' }}</td>
                <td>{{ $payment['remarks'] ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <td colspan="5" class="text-right font-weight-bold">Total Amount:</td>
            <td class="text-right font-weight-bold">
                ₱{{ number_format(array_sum(array_column($reportData, 'amount')), 2) }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Payment Status Summary</h6>
            </div>
            <div class="card-body">
                @php
                    $statusSummary = [];
                    foreach ($reportData as $payment) {
                        $status = $payment['status'];
                        if (!isset($statusSummary[$status])) {
                            $statusSummary[$status] = [
                                'count' => 0,
                                'amount' => 0,
                            ];
                        }
                        $statusSummary[$status]['count']++;
                        $statusSummary[$status]['amount'] += $payment['amount'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statusSummary as $status => $data)
                            <tr>
                                <td>
                                    <span
                                        class="badge bg-{{ $status == 'Posted' ? 'success' : ($status == 'Unposted' ? 'warning' : 'danger') }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">₱{{ number_format($data['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Payment Mode Summary</h6>
            </div>
            <div class="card-body">
                @php
                    $modeSummary = [];
                    foreach ($reportData as $payment) {
                        $mode = $payment['payment_mode'];
                        if (!isset($modeSummary[$mode])) {
                            $modeSummary[$mode] = [
                                'count' => 0,
                                'amount' => 0,
                            ];
                        }
                        $modeSummary[$mode]['count']++;
                        $modeSummary[$mode]['amount'] += $payment['amount'];
                    }
                @endphp

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Payment Mode</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modeSummary as $mode => $data)
                            <tr>
                                <td>{{ $mode }}</td>
                                <td class="text-right">{{ number_format($data['count']) }}</td>
                                <td class="text-right">₱{{ number_format($data['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if (count($reportData) > 10)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Payment Timeline</h6>
                </div>
                <div class="card-body">
                    @php
                        $datePayments = [];
                        foreach ($reportData as $payment) {
                            $date = \Carbon\Carbon::parse($payment['payment_date'])->format('Y-m-d');
                            if (!isset($datePayments[$date])) {
                                $datePayments[$date] = 0;
                            }
                            $datePayments[$date] += $payment['amount'];
                        }
                        // Sort by date
                        ksort($datePayments);
                    @endphp

                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-right">Amount</th>
                                <th>Distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datePayments as $date => $amount)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                                    <td class="text-right">₱{{ number_format($amount, 2) }}</td>
                                    <td>
                                        <div class="progress">
                                            @php
                                                $percentage = ($amount / array_sum($datePayments)) * 100;
                                            @endphp
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $percentage }}%"
                                                aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                                <td class="font-weight-bold">Total</td>
                                <td class="text-right font-weight-bold">
                                    ₱{{ number_format(array_sum($datePayments), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

@if (count($reportData) > 5)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Financial Insights</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Payment Mode Distribution</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Mode</th>
                                                    <th class="text-right">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalAmount = array_sum(array_column($reportData, 'amount'));
                                                    $modePercentages = [];
                                                    foreach ($modeSummary as $mode => $data) {
                                                        $modePercentages[$mode] =
                                                            ($data['amount'] / $totalAmount) * 100;
                                                    }
                                                    // Sort by percentage (descending)
                                                    arsort($modePercentages);
                                                @endphp

                                                @foreach ($modePercentages as $mode => $percentage)
                                                    <tr>
                                                        <td>{{ $mode }}</td>
                                                        <td class="text-right">{{ number_format($percentage, 1) }}%
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Payment Status Analysis</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Status</th>
                                                    <th class="text-right">Count</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($statusSummary as $status => $data)
                                                    <tr>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $status == 'Posted' ? 'success' : ($status == 'Unposted' ? 'warning' : 'danger') }}">
                                                                {{ $status }}
                                                            </span>
                                                        </td>
                                                        <td class="text-right">{{ number_format($data['count']) }}</td>
                                                        <td class="text-right">₱{{ number_format($data['amount'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Payment Statistics</h6>
                                    @php
                                        $amountValues = array_column($reportData, 'amount');
                                        $maxPayment = !empty($amountValues) ? max($amountValues) : 0;
                                        $minPayment = !empty($amountValues) ? min($amountValues) : 0;
                                        $avgPayment = !empty($amountValues)
                                            ? array_sum($amountValues) / count($amountValues)
                                            : 0;

                                        // Get client with highest payment amount
                                        $clientPayments = [];
                                        foreach ($reportData as $payment) {
                                            if (!isset($clientPayments[$payment['client']])) {
                                                $clientPayments[$payment['client']] = 0;
                                            }
                                            $clientPayments[$payment['client']] += $payment['amount'];
                                        }
                                        arsort($clientPayments);
                                        $topClient = key($clientPayments);
                                        $topClientAmount = reset($clientPayments);
                                    @endphp

                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>Total Payments</span>
                                            <span class="badge bg-primary badge-pill">{{ count($reportData) }}</span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>Highest Payment</span>
                                            <span>₱{{ number_format($maxPayment, 2) }}</span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>Lowest Payment</span>
                                            <span>₱{{ number_format($minPayment, 2) }}</span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>Average Payment</span>
                                            <span>₱{{ number_format($avgPayment, 2) }}</span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>Top Client</span>
                                            <span title="{{ $topClient ?? 'N/A' }}">
                                                {{ $topClient ? (strlen($topClient) > 15 ? substr($topClient, 0, 15) . '...' : $topClient) : 'N/A' }}
                                            </span>
                                        </li>
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>Top Client Amount</span>
                                            <span>₱{{ number_format($topClientAmount ?? 0, 2) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
