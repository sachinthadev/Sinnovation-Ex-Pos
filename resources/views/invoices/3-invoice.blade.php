<!-- resources/views/invoices/invoice.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family:"terminus";
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .text-right{
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            overflow:wrap;
        }
        th, td {
            padding-top: 2mm;
            padding-bottom:2mm;
            text-align: left;
            border-left: 0;
            border-right: 0;
        }
        
        .border-b{
            border-bottom: 1px solid #333;
        }
        .pb-1{
            padding-bottom: 1mm;
        }
       .pb-2{
    padding-bottom: 2mm;
}
        .pt-0{
            padding-top: 0;
        }
        .pb-0{
            padding-bottom: 0;
        }
        .font-bold{
            font-weight: bold;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 3mm;
        }
        .invoice-header h1{
            font-size: 16px;
            margin-top: 3mm;
        }
        .invoice-header p {
            font-size: 14px;
            margin: 0;
        }
        .invoice-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10mm;
        }
        .text-center{
            text-align: center
        }
        .border-b-d{
            border-bottom: 1px dashed #999;
        }
        h2{
            padding-top:0.5mm; 
            text-align:center; 
            padding-bottom:0mm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
        }
        .p-x-1{
            padding-left: 1mm;
            padding-right:1mm;
        }
        .p-x-2{
            padding-left: 2mm;
            padding-right:2mm;
        }
        table th{
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
        }
        .mt-0{
            margin-top: 0;
        }

        /* ── Customer details two-column block ── */
        .customer-section {
            margin-bottom: 3mm;
            border-top: 1px dashed #999;
            border-bottom: 1px dashed #999;
            padding: 2mm 0;
        }
        .customer-section h3 {
            text-align: center;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0 0 2mm 0;
            padding: 0;
            letter-spacing: 1px;
        }
        .two-col {
            width: 100%;
            border-collapse: collapse;
        }
        .two-col td {
            padding: 1mm 0;
            font-size: 13px;
            vertical-align: top;
        }
        .two-col .col-label {
            width: 45%;
            font-weight: bold;
        }
        .two-col .col-value {
            width: 55%;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <span style="margin-top: 2mm">&nbsp;&nbsp;</span>
        <h1 class="text-center" style="margin-bottom:0; font-family: 'Courier New', Courier, monospace">
            {{$site_name}}
        </h1>
        <p style="font-size: 12px;">{{$site_description}}</p><h2>INVOICE</h2>
        <p>Invoice Number: #{{ str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT) }}</p>
        <p>Date: {{ $date }}</p>
        <p>Time: {{ $time }}</p>
        <p>Cashier: {{ $order->user->name ?? 'N/A' }}</p>
        @if(!empty($order->mileage))
        <p>Mileage: {{ $order->mileage }}</p>
        @endif
    </div>

    {{-- ── Customer details (two-column), shown only when a customer is linked ── --}}
    @if($order->customer)
    <div class="customer-section">
        <h3>CUSTOMER DETAILS</h3>
        <table class="two-col">
            <tbody>
                <tr>
                    <td class="col-label">Name</td>
                    <td class="col-value">
                        {{ trim($order->customer->first_name . ' ' . $order->customer->last_name) }}
                    </td>
                </tr>
                @if(!empty($order->customer->vehicle_identifier))
                <tr>
                    <td class="col-label">Vehicle No.</td>
                    <td class="col-value">{{ $order->customer->vehicle_identifier }}</td>
                </tr>
                @endif
                @if(!empty($order->customer->vehicle_model))
                <tr>
                    <td class="col-label">Vehicle Model</td>
                    <td class="col-value">{{ $order->customer->vehicle_model }}</td>
                </tr>
                @endif
                @if(!empty($order->customer->phone))
                <tr>
                    <td class="col-label">Phone</td>
                    <td class="col-value">{{ $order->customer->phone }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    <table>
        <thead>
          <tr>
    <th>Product</th>
    <th class="text-center p-x-1">QTY</th>
    <th class="text-right">Amount</th>
</tr>
<tr>
    <th class="border-b-d" colspan="3" style="height: 0; padding:0;"></th>
</tr>
        </thead>
        <tbody>
            @php 
                $i = 0; 
                $total_price = 0;
                $grand_total = 0;
            @endphp
            @foreach($items as $item)
    @php $i++; @endphp

    <tr>
        <td class="pb-1" colspan="3">
            {{ $i.'.'.($item->product?->name ?? $item->name) }}
        </td>
    </tr>

    <tr>
        <td class="text-center pt-0">
            {{ number_format($item['price'], 2, '.', '') }}
        </td>

        <td class="text-center pt-0">
            {{ $item['quantity'] }}
        </td>

        @php
            $item_total = $item->price * $item->quantity;
            $total_price += $item_total;
            $grand_total += $item_total;
        @endphp

        <td class="text-right pt-0">
            {{ number_format($item_total, 2, '.', '') }}
        </td>
    </tr>

    <tr>
       <td class="border-b-d" colspan="3" style="height: 0; padding:0;"></td>
    </tr>
@endforeach

            <tr>
                <td class="text-center" colspan="3"
    style="padding-top:3mm; padding-bottom:0; font-size:16px">
                    INVOICE SUMMARY
                </td>
            </tr>

            <tr>
                <td class="mt-2" colspan="2" style="font-size:16px;">
    Total
</td>
                <td class="pt-0" style="text-align: right; font-size:16px">
                    {{ number_format($grand_total, 2) }}
                </td>
            </tr>
            <tr>
                <td class="border-b-d" colspan="4" style="height: 0; padding:0; padding-top:2mm"></td>
            </tr>
            <tr>
                <td colspan="2" style="font-size:16px">
    Grand Total
</td>
                <td class="text-right" style=" font-size:16px">
                    {{ number_format($grand_total, 2) }}
                </td>
            </tr>
            <tr>
                <td class="border-b-d" colspan="3" style="height: 0; padding:0;"></td>
            </tr>
            
        </tbody>
    </table>

    <div class="invoice-footer">
        <p>Contact: 0713  377 477 / 0763  881 800</p>
        <p>Thissa Road,  Ranna</p>
        <p>Thank you for your purchase!</p>
        <p>Powered by Sinnovation Ex</p>
    </div>
</body>
</html>
