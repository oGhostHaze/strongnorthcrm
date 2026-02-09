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
    <div class="min-h-screen min-w-screen">
        <!-- Page Content -->
        @php
            $row_count = 25;
        @endphp
        <main>
            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="p-2 border-gray-200 flex ">
                        <label class="my-2 btn btn-sm btn-secondary ms-2" for="update_details">Edit Additional
                            Details</label>
                        <a class="my-2 btn btn-sm btn-warning ms-2"
                            href="{{ route('signaturepad', $oa->oa_number) }}">Signature</a>
                        <a href="#" class="btn btn-sm btn-info ms-auto" onclick="printdiv('print_div')">Print</a>
                    </div>
                    <div class="overflow-hidden bg-white sm:rounded-lg" id="print_div">
                        <div class="flex mb-1 p-6 pb-0">
                            <div class="w-1/4 ps-4 mb-0">
                                <img src="{{ url('img/str.png') }}" alt="" class="img-fluid"
                                    style="max-width: 60%">
                            </div>
                            <div class="w-1/2 text-center">
                                <h3 class="font-bold my-0">StrongNorth Enterprises OPC</h3>
                                <p class="text-sm p-0 my-0">(Independent Authorized Dealer)</p>
                                <p class="text-sm p-0 my-0">9-10 VYV Bldg., Valdez Center, Brgy 1 San Nicolas, Ilocos
                                    Norte</p>
                                <p class="text-sm p-0 my-0">Contact: 0917-891-9180</p>
                            </div>
                            <div class="w-1/4 pe-4 mb-0">
                                <img src="{{ url('img/right.png') }}" alt="" class="img-fluid ms-auto"
                                    style="max-width: 60%">
                            </div>
                        </div>
                        <div class="p-6 pt-2 text-center bg-white border-b border-gray-200 sm:px-20">
                            <h3 class="font-bold text-xl">ORDER AGREEMENT</h3>
                            <h7 class="font-bold text-error">OA #: {{ $oa->oa_number }}</h7>
                        </div>
                        <div class="p-6" style="min-height: 70vh;">
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
                                    <div>Consultant: <span class="font-bold capitalize">{{ $oa->oa_consultant }}</span>
                                    </div>
                                    <div>Associate: <span class="font-bold capitalize">{{ $oa->oa_associate }}</span>
                                    </div>
                                    <div>Presenter: <span class="font-bold capitalize">{{ $oa->oa_presenter }}</span>
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
                                            <th class="text-start w-7/12">Item</th>
                                            <th class="text-end w-2/12">Unit Price</th>
                                            <th class="text-end w-1/12">Qty</th>
                                            <th class="text-end w-2/12">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($oa->items()->get() as $order)
                                            @php
                                                $row_count--;
                                                $print_desc = $order->custom_description ?? $order->product->product_description;
                                            @endphp
                                            <tr class="border border-black {!! $order->remarks == 'Composed of:' ? 'active"' : '' !!}">
                                                <td>{!! $order->product->tblset_id
                                                    ? '<span class="font-bold">' . e($print_desc) . '</span> Composed of:'
                                                    : e($print_desc) !!}</td>
                                                <td class="text-end">{{ number_format($order->item_price, 2) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ $order->item_qty + $order->returned + $order->released }}</td>
                                                <td class="text-end">{{ number_format($order->item_total, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            @php
                                                $row_count--;
                                            @endphp
                                            <tr>
                                                <td class="text-center text-muted" colspan="7">No items found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <table class="w-full border border-black text-black">
                                    <thead class="bg-gray-200">
                                        <tr class="border border-black">
                                            <th class="text-start w-7/12">Type/Gift</th>
                                            <th class="text-end w-2/12">Unit Price</th>
                                            <th class="text-end w-1/12">Qty</th>
                                            <th class="text-end w-2/12">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($oa->gifts()->get() as $order)
                                            @php
                                                $row_count--;
                                            @endphp
                                            <tr class="border border-black">
                                                <td>
                                                    @if ($order->type)
                                                        <span>[{{ $order->type }}]</span>
                                                    @endif
                                                    {{ $order->product->product_description }}
                                                </td>
                                                <td class="text-end">{{ number_format($order->item_price, 2) }}</td>
                                                <td class="text-end">
                                                    {{ $order->item_qty + $order->returned + $order->released }}</td>
                                                <td class="text-end">{{ number_format(0, 2) }}</td>
                                            </tr>
                                        @empty
                                            @php
                                                $row_count--;
                                            @endphp
                                            <tr>
                                                <td class="text-center text-muted" colspan="4">No gifts found/td>
                                            </tr>
                                        @endforelse
                                        <tr class="border border-black">
                                            <td class="text-center" colspan="4"> ---- Nothing follows ----</td>
                                        </tr>
                                        @for ($row = 0; $row < $row_count; $row++)
                                            <tr class="border border-black">
                                                <td class="text-center text-white" colspan="4"> -</td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                    <tfoot>
                                        @php
                                            $total = $oa->items()->sum('item_total');
                                        @endphp
                                        {{-- <tr class='table-light'><td class="text-end" colspan='4'><strong>SUBTOTAL:</strong></td><td class='text-end' colspan="2"><span>&#8369; </span>{{number_format($subtotal = $oa->oa_price_override ? $oa->oa_price_override :  $oa->items()->sum('item_total') ,2)}}</td></tr> --}}
                                        <tr class='table-light'>
                                            <td class="text-end" colspan='3'><strong>TOTAL:</strong></td>
                                            <td class="text-end"><strong>&#8369;
                                                    {{ number_format($subtotal = $oa->oa_price_override ? $oa->oa_price_override : $oa->items()->sum('item_total'), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="grid grid-cols-2 gap-1 px-3 py-5 border mt-auto">
                                <div class="flex flex-col">
                                    <div class="flex">
                                        <span>Delivery Date: {{ $oa->delivery_date }} </span>
                                    </div>
                                    <div class="flex">
                                        <span>Time: {{ $oa->delivery_time }} </span>
                                    </div>
                                    <div class="flex">
                                        <span>Total Amount:
                                            {{ number_format($subtotal = $oa->oa_price_override ? $oa->oa_price_override : $oa->items()->sum('item_total'), 2) }}
                                        </span>
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
                                        <small>Checks payable only to <span class="font-bold uppercase">StrongNorth
                                                Enterprises OPC</span></small>
                                    </p>
                                    <div class="flex flex-col justify-center pt-5 mt-10 text-center">
                                        <div class="mx-auto">
                                            @if ($oa->host_signature)
                                                <img src="{{ url('upload/' . $oa->host_signature) }}" class="h-20"
                                                    alt="Host Signature">
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
        </main>
    </div>


    {{-- UPDATE DETAILS MODAL --}}

    <input type="checkbox" id="update_details" class="modal-toggle" />
    <div class="modal">
        <form class="w-11/12 max-w-5xl modal-box" action="{{ route('order.update_details') }}" method="POST">
            <label for="update_details" class="absolute btn btn-sm btn-circle right-4 top-4">✕</label>
            <h3 class="text-lg font-bold">Update Additional Details</h3>
            <div class="w-full p-4">
                @csrf
                <input type="text" name="oa_number" value="{{ $oa->oa_number }}" hidden>
                <div class="w-full mb-2 form-control">
                    <label class="input-group">
                        <span class="whitespace-nowrap">Current Level</span>
                        <select class="w-full max-w-xs select-bordered select" name='current_level'>
                            <option value="Associate">Associate</option>
                            <option value="Consultant">Consultant</option>
                            <option value="Senior Consultant">Senior Consultant</option>
                            <option value="Distributor">Distributor</option>
                        </select>
                    </label>
                </div>

                <div class="w-full mb-2 form-control">
                    <label class="input-group">
                        <span class="whitespace-nowrap">Delivery Date</span>
                        <input type="date" min="{{ date('Y-m-d') }}" class="w-full input input-bordered"
                            name="delivery_date" />
                    </label>
                </div>
                <div class="w-full mb-2 form-control">
                    <label class="input-group">
                        <span class="whitespace-nowrap">Delivery Time</span>
                        <input type="time" class="w-full input input-bordered" name="delivery_time" />
                    </label>
                </div>
                <div class="w-full pr-3 mb-2 form-control">
                    <label class="input-group">
                        <span class="text-sm whitespace-nowrap">Initial Investment</span>
                        <input type="number" step="0.01" class="w-full input input-bordered"
                            name="initial_investment" />
                    </label>
                </div>
                <div class="w-full mb-2 form-control">
                    <label class="input-group">
                        <span class="w-1/4 whitespace-nowrap">Terms</span>
                        <input type="text" class="w-full input input-bordered" name="terms" />
                    </label>
                </div>
            </div>
            <div class="modal-action">
                <label for="update_details" class="btn btn-error">Cancel</label>
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </form>
    </div>


    <script>
        function printdiv(divID) {
            var printContents = document.getElementById('print_div').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(true);
        };
    </script>

</body>

</html>
