@extends('v_layouts.app')
@section('content')
 <!-- template -->
 <div class="col-md-12">
  <div class="order-summary clearfix">
   <div class="section-title">
    <p>KERANJANG</p>
    <h3 class="title">Keranjang Belanja</h3>
   </div>
   <!-- msgSuccess -->
   @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
     <button type="button" class="close" data-dismiss="alert" arialabel="Close"><span
       aria-hidden="true">&times;</span></button>
     <strong>{{ session('success') }}</strong>
    </div>
   @endif
   <!-- end msgSuccess -->
   <!-- msgError -->
   @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
     <button type="button" class="close" data-dismiss="alert" arialabel="Close"><span
       aria-hidden="true">&times;</span></button>
     <strong>{{ session('error') }}</strong>
    </div>
   @endif
   <!-- end msgError -->
   @if ($order && $order->orderItems->count() > 0)
    <table class="shopping-cart-table table">
     <thead>
      <tr>
       <th>Produk</th>
       <th></th>
       <th class="text-center">Harga</th>
       <th class="text-center">Quantity</th>
       <th class="text-center">Total</th>
       <th class="text-right"></th>
      </tr>
     </thead>
     <tbody>
      @php
       $totalHarga = 0;
       $totalBerat = 0;
      @endphp
      @foreach ($order->orderItems as $item)
       @php
        $totalHarga += $item->harga * $item->quantity;
        $totalBerat += $item->produk->berat * $item->quantity;
       @endphp
       <tr>
        <td class="thumb"><img src="{{ asset('storage/img-produk/thumb_sm_' . $item->produk->foto) }}" alt="">
        </td>
        <td class="details">
         <a>{{ $item->produk->nama_produk }}</a>
         <ul>
          <li><span>Berat: {{ $item->produk->berat }} Gram</span></li>
         </ul>
         <ul>
          <li><span>Stok: <b>{{ $item->produk->stok }}</b></span></li>
         </ul>
        </td>
        <td class="price text-center"><strong>Rp. {{ number_format($item->harga, 0, ',', '.') }}</strong></td>
        <td class="qty text-center">
         <form action="{{ route('order.updateCart', $item->id) }}" method="post">
          @csrf
          <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 60px;">
          <button type="submit" class="btn btn-sm btn-warning">Update</button>
         </form>
        </td>
        <td class="total text-center"><strong class="primary-color">Rp.
          {{ number_format($item->harga * $item->quantity, 0, ',', '.') }}</strong></td>
        <td class="text-right">
         <form action="{{ route('order.remove', $item->produk->id) }}" method="post">
          @csrf
          <button class="main-btn icon-btn"><i class="fa fa-close"></i></button>
         </form>
        </td>
       </tr>
      @endforeach

     </tbody>

     <tfoot>
      <tr>
       <th class="empty" colspan="3"></th>
       <th>SUBTOTAL</th>
       <th colspan="2" class="sub-total">Rp. {{ number_format($totalHarga, 0, ',', '.') }}</th>
      </tr>
      <tr>
       <th class="empty" colspan="3"></th>
       <th>Ongkos Kirim</th>
       <td colspan="2">
        Rp. {{ number_format($order->biaya_ongkir, 0, ',', '.') }} <br>
        {{ $order->kurir . '.' . $order->layanan_ongkir . ' *estimasi ' . $order->estimasi_ongkir . ' Hari' }}
       </td>
      </tr>
      <tr>
       <th class="empty" colspan="3"></th>
       <th>TOTAL BAYAR</th>
       <th colspan="2" class="total">Rp. {{ number_format($totalHarga + $order->biaya_ongkir, 0, ',', '.') }}</th>
      </tr>

     </tfoot>
    </table>


   <div class="pull-right">

    @if (!$order->biaya_ongkir)
        {{-- BELUM PILIH ONGKIR --}}
        <form action="{{ route('order.selectShipping') }}" method="post">
            @csrf
            <input type="hidden" name="total_price" value="{{ $totalHarga }}">
            <input type="hidden" name="total_weight" value="{{ $totalBerat }}">
            <button class="primary-btn">Pilih Pengiriman</button>
        </form>
    @else
        {{-- SUDAH PILIH ONGKIR --}}
            @if ($order->biaya_ongkir)
            <form action="{{ route('order.selectShipping') }}" method="post">
                @csrf
                <button class="btn btn-warning" style="margin-bottom: 5%; padding: 6% 6%;"><b>GANTI PENGIRIMAN</b></button>
            </form>

        @endif
        <form action="{{ route('selectpayment',$order->id) }}" method="post">
            @csrf
            <button class="primary-btn">Bayar Sekarang</button>
        </form>
    @endif

</div>
    <div class="pull-right" style="margin-right: 5%">

</div>
   @else
    <h4 class="text-uppercase text-center">Keranjang belanja kosong.</h4>
   @endif
  </div>
 </div>
 <!-- end template-->
@endsection
