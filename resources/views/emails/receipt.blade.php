<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .receipt-products {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .detailable-info {
            margin-top: 20px;
        }

        .detailable-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Receipt Information</h1>
    </div>

    <div class="receipt-info">
        <div>
            <p>Receipt Number: {{ $receipt['receipt_number'] }}</p>
            <p>Date: {{ $receipt['created_at'] }}</p>
        </div>
        <div>
            <p>Total Price: ${{ $receipt['total_price'] }}</p>
            <p>Total Tax: ${{ $receipt['total_tax'] }}</p>
            <p>Payment Method: {{ $receipt['payment_method'] }}</p>
        </div>
    </div>

    <div class="receipt-products">
        <h2>Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($receipt->receiptProducts as $product)
                    <tr>
                        <td>{{ $product['product_name'] }}</td>
                        <td>${{ $product['product_price'] }}</td>
                        <td>{{ $product['product_qty'] }}</td>
                        <td>${{ $product['product_price'] * $product['product_qty'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="detailable-info">
        <h2>Details</h2>
        @if ($receipt->detailable)
            @foreach ($receipt->detailable->getAttributes() as $key => $value)
                @unless(in_array($key, ['id', 'receipt_id', 'created_at', 'updated_at']))
                    <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
                @endunless
            @endforeach
        @else
            <p>No details available</p>
        @endif
    </div>

    <div>
        <p>Message: {{ $receipt['message'] }}</p>
    </div>

</div>

</body>
</html>
