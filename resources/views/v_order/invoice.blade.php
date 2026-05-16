<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body{
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: #222;
        }

        .invoice-box{
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 35px;
        }

        .top-section{
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }

        .company-section{
            width: 50%;
        }

        .company-section img{
            width: 140px;
            margin-bottom: 10px;
        }

        .company-name{
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .company-address{
            line-height: 1.7;
            font-size: 14px;
        }

        .invoice-section{
            text-align: right;
            width: 40%;
        }

        .invoice-section h1{
            font-size: 34px;
            margin-bottom: 15px;
        }

        .invoice-section table{
            width: 100%;
        }

        .invoice-section td{
            padding: 4px 0;
            border: none;
            font-size: 14px;
        }

        .info-section{
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .info-box{
            width: 45%;
        }

        .info-box h3{
            font-size: 15px;
            margin-bottom: 10px;
            border-bottom: 2px solid #444;
            padding-bottom: 5px;
        }

        .info-box p{
            line-height: 1.8;
            font-size: 14px;
        }

        .product-table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .product-table th{
            background: #2f3e4e;
            color: white;
            padding: 12px;
            font-size: 14px;
        }

        .product-table td{
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .text-right{
            text-align: right;
        }

        .summary{
            width: 200px;
            margin-left: auto;
            margin-top: 30px;
        }

        .summary table{
            width: 100%;
        }

        .summary td{
            padding: 8px 0;
            border: none;
            font-size: 15px;
        }

        .grand-total{
            font-size: 28px;
            font-weight: bold;
            margin-top: 15px;
            text-align: right;
        }


.signature{
    width: 100%;
    margin-top: 50px;
}

.signature-box{
    width: 250px;
    margin-left: auto;
    text-align: center;
    position: relative;
}

.signature-space{
    height: 120px;
    position: relative;
}

.stamp-img{
    width: 120px;
    opacity: 0.9;

    position: absolute;
    top: 0;
    left: 50%;

    transform: translateX(-50%) rotate(-10deg);
}

        @media print{
            body{
                background: white;
                padding: 0;
            }

            .invoice-box{
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="invoice-box">

        {{-- TOP --}}
        <div class="top-section">

            <div class="company-section">
                <img src="{{ asset('./image/logo.png') }}" alt="Logo">

                <div class="company-name">
                    CV XYZ STORE
                </div>

                <div class="company-address">
                    Poris Mansion Exclusive 2, Tangerang 15141 <br>
                    Telp: 0821-1494-3996 <br>
                    Email: xyzstore@gmail.com
                </div>
            </div>

            <div class="invoice-section">
                <h1>Invoice</h1>

                <table>
                    <tr>
                        <td><b>Invoice ID</b></td>
                        <td class="text-right">INV/{{ $order->id }}</td>
                    </tr>

                    <tr>
                        <td><b>Tanggal</b></td>
                        <td class="text-right">
                            {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}
                        </td>
                    </tr>

                    <tr>
                        <td><b>Status</b></td>
                        <td class="text-right">{{ $order->status }}</td>
                    </tr>
                </table>
            </div>

        </div>

        {{-- CUSTOMER --}}
        <div class="info-section">

            <div class="info-box">
                <h3>Info Perusahaan</h3>

                <p>
                    <b>CV XYZ STORE</b><br>
                    Tangerang, Banten <br>
                    Telp: 0821-1494-3996
                </p>
            </div>

            <div class="info-box">
                <h3>Penerima</h3>

                <p>
                    <b>{{ $order->customer->user->nama ?? '-' }}</b><br>

                    <b>alamat :</b> {{ $order->customer->alamat ?? '-' }} <br>

                    <b>Telp :</b> 
                    {{ $order->customer->user->hp ?? '-' }}
                </p>
            </div>

        </div>

        {{-- TABLE --}}
        <table class="product-table">

            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kuantitas</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($order->orderItems as $row)

                    <tr>

                        <td>
                            <b>{{ $row->produk->nama_produk }}</b>
                        </td>

                        <td>
                            {{ $row->quantity }}
                        </td>

                        <td>
                            Rp.
                            {{ number_format($row->harga, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp.
                            {{ number_format($row->harga * $row->quantity, 0, ',', '.') }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        {{-- SUMMARY --}}
        <div class="summary">

            <table>


                <tr>
                    <td><b>Ongkir</b></td>
                    <td class="text-right">
                        + Rp.
                        {{ number_format($order->biaya_ongkir, 0, ',', '.') }}
                    </td>
                </tr>


            </table>

            <div class="grand-total">
                Subtotal<br>
                Rp.
                {{ number_format($order->total_harga, 0, ',', '.') }}
            </div>

        </div>

       <div class="signature">

    <div class="signature-box">

        <p>
            Tangerang,
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </p>

        <div class="signature-space">

            {{-- CAP --}}
            <img 
                src="{{ asset('image/cap.png') }}" 
                alt="Cap XYZ Store"
                class="stamp-img">

        </div>

        <p>
            <b>XYZ STORE</b>
        </p>

    </div>

</div>
        </div>

    </div>

    <script>
        window.onload = function () {

            window.print();

            setTimeout(() => {
                window.location.href = document.referrer;
            }, 100);

        };
    </script>

</body>

</html>