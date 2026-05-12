@extends('v_layouts.app')
@section('content')
 <div class="col-md-12">
  <div class="order-summary clearfix">

   <div class="section-title">
    <h3 class="title">PILIH PENGIRIMAN</h3>
   </div>

   {{-- PROVINSI --}}
   <div class="form-group">
    <label>Provinsi</label>
    <select name="province_id" id="province" class="form-control">
     <option value="">-- Pilih Provinsi --</option>
     @foreach ($provinces as $province)
      <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
     @endforeach
    </select>
   </div>

   {{-- KOTA --}}
   <div class="form-group">
    <label>Kota</label>
    <select name="city_id" id="city" class="form-control">
     <option value="">-- Pilih Kota --</option>
    </select>
   </div>

   {{-- KECAMATAN --}}
   <div class="form-group">
    <label>Kecamatan</label>
    <select name="district_id" id="district" class="form-control">
     <option value="">-- Pilih Kecamatan --</option>
    </select>
   </div>

   {{-- KURIR --}}
   <div class="form-group">
    <label>Kurir</label>
    <select id="courier" class="form-control" name="kurir">
     <option value="jne">JNE</option>
     <option value="tiki">TIKI</option>
     <option value="pos">POS</option>
    </select>
   </div>

   <button class="btn btn-primary btn-check">CEK ONGKIR</button>

   <br><br>

   {{-- RESULT --}}
   <table class="table">
    <thead>
     <tr>
      <th>Layanan</th>
      <th>Biaya</th>
      <th>Estimasi</th>
      <th>Total Berat</th>
      <th></th>
     </tr>
    </thead>
    <tbody id="resultOngkir"></tbody>
   </table>

  </div>
 </div>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>

 <script>
  $('#district').on('change', function() {
   console.log('District ID:', $(this).val());
  });

  $(document).ready(function() {

   // 🔹 CITY
   $('#province').on('change', function() {
    let provinceId = $(this).val();

    if (provinceId) {
     $.get(`/cities/${provinceId}`, function(response) {
      $('#city').html('<option value="">-- Pilih Kota --</option>');

      $.each(response, function(i, val) {
       $('#city').append(`<option value="${val.id}">${val.name}</option>`);
      });
     });
    }
   });

   // 🔹 DISTRICT
   $('#city').on('change', function() {
    let cityId = $(this).val();

    if (cityId) {
     $.get(`/districts/${cityId}`, function(response) {
      $('#district').html('<option value="">-- Pilih Kecamatan --</option>');

      $.each(response, function(i, val) {
       $('#district').append(`<option value="${val.id}">${val.name}</option>`);
      });
     });
    }
   });

   // 🔹 CEK ONGKIR

   $('.btn-check').click(function(e) {
    e.preventDefault();

    let token = "{{ csrf_token() }}";
    let district_id = $('#district').val();
    let courier = $('#courier').val();
    let weight = {{ $totalWeight }};

    if (!district_id || !courier) {
     alert('Lengkapi data dulu');
     return;
    }

    $.ajax({
     url: "/cost",
     type: "POST",
     dataType: "JSON",
     data: {
      _token: token,
      district_id: district_id,
      courier: courier,
      weight: weight
     },
     success: function(response) {

      let html = '';

      $.each(response, function(i, val) {

       // FILTER cuma service tertentu
       if (val.service === 'REG' || val.service === 'OKE' || val.service === 'YES') {

        html += `
<tr>
    <td>${val.service}</td>
    <td>Rp ${val.cost}</td>
    <td>${val.etd}</td>
    <td>{{ $totalWeight }} gram</td>
    <td>
        <form method="POST" action="{{ route('order.chooseShipping') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="kurir" value="${$('#courier').val()}">
            <input type="hidden" name="service" value="${val.service}">
            <input type="hidden" name="cost" value="${val.cost}">
            <input type="hidden" name="etd" value="${val.etd}">
            <input type="hidden" name="weight" value="{{ $totalWeight }}">
            <button class="btn btn-danger">PILIH</button>
        </form>
    </td>
</tr>
`;
       }

      });

      $('#resultOngkir').html(html);
     }
    });
   });

  });
 </script>
@endsection
