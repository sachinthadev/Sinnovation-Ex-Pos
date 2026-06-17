<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class UtilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function print($order_id)
    {
        $order = Order::with([
            'items',
            'user',
            'customer'
        ])->findOrFail($order_id);

        $currency_symbol = config('settings.currency_symbol');
        $site_name = config('settings.site_name');
        $site_description = config('settings.site_description');

        $timezone = config('app.timezone', 'UTC');

        $data = [
            'invoiceNumber'    => $order->id,
            'date'             => $order->created_at->timezone($timezone)->format('Y-m-d'),
            'time'             => $order->created_at->timezone($timezone)->format('h:i:s A'),
            'items'            => $order->items,
            'order'            => $order,
            'currency_symbol'  => $currency_symbol,
            'site_name'        => $site_name,
            'site_description' => $site_description
        ];

        $html = view('invoices.3-invoice', $data)->render();

        /*
        |--------------------------------------------------------------------------
        | Dynamic Receipt Height
        |--------------------------------------------------------------------------
        */

       $order = Order::with(['items', 'user', 'customer'])->findOrFail($order_id);

/** @var \Illuminate\Database\Eloquent\Collection $items */
$items = $order->items;

$itemCount = $items->count();

        // Adjust these values if needed
        $headerHeight = 70;
        $itemHeight   = 18;
        $summaryHeight = 50;
        $footerHeight = 40;

        $receiptHeight =
            $headerHeight +
            ($itemCount * $itemHeight) +
            $summaryHeight +
            $footerHeight;

        // Minimum receipt height
        $receiptHeight = max($receiptHeight, 200);

        /*
        |--------------------------------------------------------------------------
        | Font Configuration
        |--------------------------------------------------------------------------
        */

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        /*
        |--------------------------------------------------------------------------
        | mPDF
        |--------------------------------------------------------------------------
        */

        $mpdf = new Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path('')
            ]),

            'fontdata' => $fontData + [
                'terminus' => [
                    'R' => 'Terminus.ttf',
                ]
            ],

            'default_font' => 'terminus',
            'mode' => 'utf-8',

            // Receipt width = 75mm
            // Height = calculated dynamically
            'format' => [75, $receiptHeight],

            'orientation' => 'P',

            'margin_left'   => 3,
            'margin_right'  => 3,
            'margin_top'    => 3,
            'margin_bottom' => 3,

            'shrink_tables_to_fit' => 0,
        ]);

        $mpdf->WriteHTML($html);

        $filename = 'invoice-' . $order_id . '.pdf';

        return response(
            $mpdf->Output($filename, 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'public, must-revalidate, max-age=0',
                'Pragma' => 'public',
            ]
        );
    }
}