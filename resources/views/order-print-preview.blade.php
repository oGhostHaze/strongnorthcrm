<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="winter">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden bg-white sm:rounded-lg">
                        <div class="p-6 text-center bg-white border-b border-gray-200 sm:px-20">
                            <h3 class="font-bold">ORDER AGREEMENT</h3>
                            <h7 class="font-bold text-error">OA #: {{ $oa->oa_number }}</h7>
                        </div>
                        <div class="bg-opacity-25">
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-1 px-3">
                                    <div class="flex-col">
                                        <div>Date: <span class="font-bold">{{ $oa->oa_date }}</span></div>
                                        <div>Client: <span class="font-bold capitalize">{{ $oa->oa_client }}</span>
                                        </div>
                                        <div>Address: <span class="font-bold capitalize">{{ $oa->oa_address }}</span>
                                        </div>
                                        <div>Contact #: <span class="font-bold">{{ $oa->oa_contact }}</span></div>
                                    </div>
                                    <div class="flex-col">
                                        <div>Consultant: <span
                                                class="font-bold capitalize">{{ $oa->oa_consultant }}</span>
                                        </div>
                                        <div>Associate: <span
                                                class="font-bold capitalize">{{ $oa->oa_associate }}</span>
                                        </div>
                                        <div>Presenter: <span
                                                class="font-bold capitalize">{{ $oa->oa_presenter }}</span>
                                        </div>
                                        <div>Team Builder: <span
                                                class="font-bold capitalize">{{ $oa->oa_team_builder }}</span></div>
                                        <div>Distributor: <span
                                                class="font-bold capitalize">{{ $oa->oa_distributor }}</span></div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <table class="w-full border border-black text-black">
                                        <thead class="bg-gray-200">
                                            <tr class="border border-black">
                                                <th class="text-start">Item</th>
                                                <th class="text-end">Unit Price</th>
                                                <th class="text-end">Qty</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($oa->items()->get() as $order)
                                                <tr class="border border-black {!! $order->remarks == 'Composed of:' ? 'active"' : '' !!}">
                                                    <td>{!! $order->product->tblset_id
                                                        ? '<span class="font-bold">' . $order->product->product_description . '</span> Composed of:'
                                                        : $order->product->product_description !!}</td>
                                                    <td class="text-end">{{ number_format($order->item_price, 2) }}
                                                    </td>
                                                    <td class="text-end">{{ $order->item_qty }}</td>
                                                    <td class="text-end">{{ number_format($order->item_total, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-muted" colspan="7">No items found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <table class="w-full border border-black border-top-0 text-black">
                                        <thead class="bg-gray-200">
                                            <tr class="border border-black">
                                                <th class="text-start">Type</th>
                                                <th class="text-start">Gift</th>
                                                <th class="text-end">Item Price</th>
                                                <th class="text-end">Qty</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($oa->gifts()->get() as $order)
                                                <tr class="border border-black">
                                                    <td>{{ $order->type }}</td>
                                                    <td>{{ $order->product->product_description }}</td>
                                                    <td class="text-end">{{ number_format($order->item_price, 2) }}
                                                    </td>
                                                    <td class="text-end">{{ $order->item_qty }}</td>
                                                    <td class="text-end">{{ number_format(0, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-muted" colspan="6">No gifts found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            @php
                                                $total = $oa->items()->sum('item_total');
                                            @endphp
                                            {{-- <tr class='table-light'><td class="text-end" colspan='4'><strong>SUBTOTAL:</strong></td><td class='text-end' colspan="2"><span>&#8369; </span>{{number_format($subtotal = $oa->oa_price_override ? $oa->oa_price_override :  $oa->items()->sum('item_total') ,2)}}</td></tr> --}}
                                            <tr class='table-light'>
                                                <td class="text-end" colspan='4'><strong>TOTAL:</strong></td>
                                                <td class="text-end" colspan="2"><strong>&#8369;
                                                        {{ number_format($total, 2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="grid grid-cols-2 gap-1 px-3 py-5 border">
                                        <div class="flex flex-col">
                                            <div class="flex">
                                                <span>Delivery Date: {{ $oa->delivery_date }} </span>
                                            </div>
                                            <div class="flex">
                                                <span>Time: {{ $oa->delivery__time }} </span>
                                            </div>
                                            <div class="flex">
                                                <span>Total Amount: {{ number_format($total, 2) }} </span>
                                            </div>
                                            <br><br><br><br><br>
                                        </div>
                                        <div class="flex flex-col">
                                            <div class="flex">
                                                <span class=" text-ellipsis">Current Spirit of Success Level: </span>
                                                <span>{{ $oa->current_level }}</span>
                                            </div>
                                            <div class="flex">
                                                <span>Initial Investment: </span> <span
                                                    class="ml-1">{{ number_format($oa->initial_investment, 2) }}</span>
                                            </div>
                                            <div class="flex">
                                                <span>Balance: </span> <span
                                                    class="ml-1">{{ number_format($total - $oa->initial_investment, 2) }}</span>
                                            </div>
                                            <div class="flex">
                                                <span>Terms: </span> <span class="ml-1">{{ $oa->terms }}</span>
                                            </div>
                                            <p class="text-ellipsis">
                                                <small>Checks payable only to <span
                                                        class="font-bold uppercase">StrongNorth Cookware
                                                        Trading</span></small>
                                            </p>
                                            <div class="flex flex-col justify-center pt-5 mt-10 text-center">
                                                <div class="mx-auto">
                                                    @if ($oa->host_signature)
                                                        <img src="{{ url('upload/' . $oa->host_signature) }}"
                                                            class="h-20" alt="Host Signature">
                                                    @endif
                                                </div>
                                                <div class="w-full border-t">
                                                    <span>Signature of Host</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
