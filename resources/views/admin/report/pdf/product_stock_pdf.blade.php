<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Product Stock Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #7367f0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-danger { color: red; }
        .text-warning { color: orange; }
        .text-success { color: green; }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Product Stock Report</h2>
        <p>Generated on: {{ date('d M, Y h:i A') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th class="text-right">Buy Price</th>
                <th class="text-right">Sale Price</th>
                <th class="text-right">Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td>{{ $product->subCategory->name ?? 'N/A' }}</td>
                <td class="text-right">TK {{ number_format($product->cost_price, 2) }}</td>
                <td class="text-right">TK {{ number_format($product->selling_price, 2) }}</td>
                <td class="text-right">{{ $product->stock }}</td>
                <td>
                    @if($product->stock <= 0)
                        <span class="text-danger">Out of Stock</span>
                    @elseif($product->stock <= 10)
                        <span class="text-warning">Low Stock</span>
                    @else
                        <span class="text-success">In Stock</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer generated report.</p>
    </div>
</body>
</html>
