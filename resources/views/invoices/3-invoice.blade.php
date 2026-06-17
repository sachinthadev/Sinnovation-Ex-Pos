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
            font-size: 13px;
            margin-top: 10mm;
        }
        .invoice-footer p {
            margin: 1mm 0;
        }
        .header-title-row {
            display: table;
            width: 100%;
        }
        .header-title-row .site-name {
            display: table-cell;
            text-align: left;
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            font-weight: bold;
            vertical-align: middle;
        }
        .header-title-row .invoice-label {
            display: table-cell;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            font-weight: bold;
            vertical-align: middle;
        }
        .date-time-row {
            display: table;
            width: 100%;
        }
        .date-time-row .col-date {
            display: table-cell;
            text-align: left;
            font-size: 13px;
        }
        .date-time-row .col-time {
            display: table-cell;
            text-align: right;
            font-size: 13px;
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
        @page {
    margin: 3mm;
}

table,
tr,
td,
th {
    page-break-inside: avoid;
}

.invoice-header,
.invoice-footer,
.customer-section {
    page-break-inside: avoid;
}
    </style>
</head>
<body>
    <div class="invoice-header">
        <span style="margin-top: 2mm">&nbsp;&nbsp;</span>
        <div class="header-title-row">
            <span class="site-name">{{$site_name}}</span>
            <span class="invoice-label">INVOICE</span>
        </div>
        <p style="font-size: 12px;">{{$site_description}}</p>

        <p>Invoice Number: #{{ str_pad($invoiceNumber, 6, '0', STR_PAD_LEFT) }}</p>
        <div class="date-time-row">
            <span class="col-date">Date: {{ $date }}</span>
            <span class="col-time">Time: {{ $time }}</span>
        </div>
        <p>Cashier: {{ $order->user->name ?? 'N/A' }}</p>
    </div>

    {{-- ── Customer details: vehicle number and mileage only ── --}}
    @php
        $showCustomerSection = ($order->customer && !empty($order->customer->vehicle_identifier))
                            || !empty($order->mileage);
    @endphp
    @if($showCustomerSection)
    <div class="customer-section">
        <table class="two-col">
            <tbody>
                @if($order->customer && !empty($order->customer->vehicle_identifier))
                <tr>
                    <td class="col-label">Vehicle No.</td>
                    <td class="col-value">{{ $order->customer->vehicle_identifier }}</td>
                </tr>
                @endif
                @if(!empty($order->mileage))
                <tr>
                    <td class="col-label">Mileage</td>
                    <td class="col-value">{{ $order->mileage }}</td>
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
