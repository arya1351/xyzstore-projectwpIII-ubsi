@extends('v_layouts.app')
@section('content')

<div class="col-md-12">
    <div class="order-summary clearfix">
        
        <div class="section-title">
            <p>HISTORY</p>
            <h3 class="title">HISTORY PESANAN</h3>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($orders->count() > 0)
            <table class="shopping-cart-table table">
                <thead>
                    <tr>
                        <th>ID PESANAN</th>
                        <th>TANGGAL</th>
                        <th>TOTAL BAYAR</th>
                        <th>STATUS</th>
                        <th>DETAIL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}
                            </td>

                            <td>
                                Rp. {{ number_format($order->total_harga, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="label label-warning">
                                    {{ $order->status }}
                                </span>
                            </td>

                            <td>
                                {{-- <a href="{{ route('order.detail', $order->id) }}" 
                                   class="btn btn-danger btn-sm">
                                    LIHAT DETAIL
                                </a>

                                <a href="{{ route('order.invoice', $order->id) }}" 
                                   class="btn btn-default btn-sm">
                                    INVOICE
                                </a>
                                 --}}
                                <a href="" 
                                   class="btn btn-danger btn-sm">
                                    LIHAT DETAIL
                                </a>

                                <a href="" 
                                   class="btn btn-default btn-sm">
                                    INVOICE
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Tidak ada riwayat pesanan.</p>
        @endif

    </div>
</div>

@endsection