@extends('v_layouts.app')
@section('content')
<style>
.order-btn{
    width: 180px;
    text-align: center;
    margin-bottom: 10px;
}
</style>
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
          @if ($order->status =='pending_payment')
             <span class="label label-primary">
              Menunggu Pembayaran
            </span>
            @elseif ($order->status =='paid')
                         <span class="label label-success">
              Pembayaran Selesai
            </span>
          @endif
        </td>

        <td>
      

         @if ($order->status == 'pending_payment')
          <form action="{{ route('selectpayment', $order->id) }}" method="POST">
           @csrf
            <button type="submit" class="btn primary-btn order-btn">Bayar Sekarang</button>
          </form>
          @elseif ($order->status == 'pending')
              <a class="btn btn-default" href="{{ route('order.cart') }}">Keranjang</a>
         @else
<a href="{{ route('invoice', $order->id )}}" class="btn primary-btn order-btn">
    INVOICE
</a>

<a href="" class="btn primary-btn order-btn">
    Detail Order
</a>


         @endif
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
