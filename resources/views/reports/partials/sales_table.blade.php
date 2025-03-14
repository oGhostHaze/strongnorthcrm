@if (isset($additionalCriteria['group_by']) && $additionalCriteria['group_by'] == 'product')
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Description</th>
                <th>Category</th>
                <th>Quantity Sold</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $item)
                <tr>
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td class="text-right">{{ number_format($item['quantity_sold']) }}</td>
                    <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <td colspan="3" class="text-right font-weight-bold">Totals:</td>
                <td class="text-right font-weight-bold">
                    {{ number_format(array_sum(array_column($reportData, 'quantity_sold'))) }}</td>
                <td></td>
                <td class="text-right font-weight-bold">
                    ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@elseif(isset($additionalCriteria['group_by']) && $additionalCriteria['group_by'] == 'date')
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Date</th>
                <th>Number of Items</th>
                <th>Quantity Sold</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                    <td class="text-right">{{ number_format($item['items_count']) }}</td>
                    <td class="text-right">{{ number_format($item['quantity_sold']) }}</td>
                    <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <td class="text-right font-weight-bold">Totals:</td>
                <td class="text-right font-weight-bold">
                    {{ number_format(array_sum(array_column($reportData, 'items_count'))) }}</td>
                <td class="text-right font-weight-bold">
                    {{ number_format(array_sum(array_column($reportData, 'quantity_sold'))) }}</td>
                <td class="text-right font-weight-bold">
                    ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@elseif(isset($additionalCriteria['group_by']) && $additionalCriteria['group_by'] == 'client')
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Client</th>
                <th>Number of Items</th>
                <th>Quantity Sold</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $item)
                <tr>
                    <td>{{ $item['client'] }}</td>
                    <td class="text-right">{{ number_format($item['items_count']) }}</td>
                    <td class="text-right">{{ number_format($item['quantity_sold']) }}</td>
                    <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <td class="text-right font-weight-bold">Totals:</td>
                <td class="text-right font-weight-bold">
                    {{ number_format(array_sum(array_column($reportData, 'items_count'))) }}</td>
                <td class="text-right font-weight-bold">
                    {{ number_format(array_sum(array_column($reportData, 'quantity_sold'))) }}</td>
                <td class="text-right font-weight-bold">
                    ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@else
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>DR Number</th>
                <th>Date</th>
                <th>Client</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $item)
                <tr>
                    <td>{{ $item['dr_number'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                    <td>{{ $item['client'] }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-right">{{ number_format($item['quantity']) }}</td>
                    <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($item['total_amount'], 2) }}</td>
                    <td>
                        <span
                            class="badge bg-{{ $item['status'] == 'Released' ? 'success' : ($item['status'] == 'To Follow' ? 'warning' : 'secondary') }}">
                            {{ $item['status'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-secondary">
                <td colspan="4" class="text-right font-weight-bold">Totals:</td>
                <td class="text-right font-weight-bold">
                    {{ number_format(array_sum(array_column($reportData, 'quantity'))) }}</td>
                <td></td>
                <td class="text-right font-weight-bold">
                    ₱{{ number_format(array_sum(array_column($reportData, 'total_amount')), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endif
