<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Dispatch - {{ $printDate }}</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-header h2 { margin: 0; font-size: 1.5em; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            font-size: 0.9em; 
            table-layout: fixed; /* Force column widths */
        }
        th, td { 
            border: 1px solid #333; 
            padding: 5px; /* Reduced padding */ 
            text-align: left; 
            /* word-wrap: break-word; /* Let inner div handle word wrapping if needed */
            min-height: 3.8em; /* Adjusted for 15 rows per page */ 
            height: 3.8em;    /* Adjusted for 15 rows per page */
            overflow: hidden; /* Keep on td as a fallback/main control */
        }
        th { background-color: #f2f2f2; overflow: visible; /* Headers shouldn't be cut off */ }
        
        .content-wrapper {
            height: 3.4em; /* Adjusted relative to td height */
            overflow: hidden;
            word-wrap: break-word; /* Allow breaking words within the fixed height wrapper */
        }

        .no-print { margin-top: 20px; text-align: center; }
        .no-print button { padding: 10px 20px; font-size: 1em; cursor: pointer; }
        @media print {
            .no-print { display: none; }
            body { 
                margin: 0.25in; /* Reduced print margins */
                font-size: 10pt; 
            }
            table { font-size: 9pt; } 
            h2 { font-size: 1.2em; }
            tr { page-break-inside: avoid; } 
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h2><strong>Today's Dispatch: {{ $printDate }}</strong></h2>
    </div>

    @if($orders->isEmpty() && !isset($addBlankRows))
        <p style="text-align:center;">No orders for dispatch today.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">ID</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 12%;">Pickup Time</th>
                    <th style="width: 16%;">Message on Cake</th>
                    <th style="width: 30%;">Custom Decoration</th>
                    <th style="width: 8%;">Price</th>
                    <th style="width: 15%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}</td>
                        <td><div class="content-wrapper">{{ $order->message_on_cake ?: '-' }}</div></td>
                        <td><div class="content-wrapper">{{ $order->custom_decoration ?: '-' }}</div></td>
                        <td>{{ $order->price ? '$' . number_format($order->price, 2) : '-' }}</td>
                        <td></td>
                    </tr>
                @endforeach
                
                @php $blankRowsToAdd = 15 - $orders->count(); @endphp 
                @if($blankRowsToAdd < 0) @php $blankRowsToAdd = 0; @endphp @endif 
                @php if(isset($addBlankRows) && $orders->isEmpty()) $blankRowsToAdd = $addBlankRows > 0 ? $addBlankRows : 15; @endphp

                @for ($i = 0; $i < $blankRowsToAdd; $i++)
                    <tr>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                        <td><div class="content-wrapper">&nbsp;</div></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    @endif

    <div class="no-print">
        <button onclick="window.print()">Print This Page</button>
    </div>

</body>
</html> 