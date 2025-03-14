<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Code</th>
            <th>Description</th>
            <th>Category</th>
            <th>Initial Stock</th>
            <th>Stock In</th>
            <th>Stock Out</th>
            <th>Returns</th>
            <th>Current Stock</th>
            <th>Unit Price</th>
            <th>Stock Value</th>
            <th>Reorder Level</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportData as $item)
            <tr
                class="{{ $item['current_stock'] < $item['reorder_level'] ? 'table-warning' : '' }} {{ $item['current_stock'] <= 0 ? 'table-danger' : '' }}">
                <td>{{ $item['code'] }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['category'] }}</td>
                <td class="text-right">{{ number_format($item['initial_stock']) }}</td>
                <td class="text-right">{{ number_format($item['stock_in']) }}</td>
                <td class="text-right">{{ number_format($item['stock_out']) }}</td>
                <td class="text-right">{{ number_format($item['returns']) }}</td>
                <td class="text-right font-weight-bold">{{ number_format($item['current_stock']) }}</td>
                <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                <td class="text-right">₱{{ number_format($item['stock_value'], 2) }}</td>
                <td class="text-right">{{ number_format($item['reorder_level']) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-secondary">
            <td colspan="7" class="text-right font-weight-bold">Totals:</td>
            <td class="text-right font-weight-bold">
                {{ number_format(array_sum(array_column($reportData, 'current_stock'))) }}</td>
            <td></td>
            <td class="text-right font-weight-bold">
                ₱{{ number_format(array_sum(array_column($reportData, 'stock_value')), 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
