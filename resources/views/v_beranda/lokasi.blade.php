@extends('v_layouts.app')

@section('content')

<style>
    .location-section{
        width: 100%;
    }

    .location-header{
        text-align: center;
        margin-bottom: 30px;
    }

    .location-header h2{
        font-weight: bold;
        margin-bottom: 10px;
    }

    .location-header p{
        color: #777;
    }

    .map-container{
        width: 100%;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .map-container iframe{
        width: 100%;
        height: 500px;
        border: 0;
    }

    .location-info{
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .location-info h3{
        margin-bottom: 15px;
        font-weight: bold;
    }

    .location-info p{
        margin-bottom: 10px;
        color: #555;
    }

    @media (max-width: 768px){

        .map-container iframe{
            height: 350px;
        }

        .location-header h2{
            font-size: 24px;
        }

    }
</style>

<div class="location-section">

    <div class="location-header">
        <h2>Lokasi Toko</h2>
        <p>Temukan lokasi XYZ Store dengan mudah melalui Google Maps</p>
    </div>

    <div class="map-container">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d495.82765158146015!2d106.6988251988592!3d-6.181425402982838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f9001976fc4d%3A0xf03f66e851815ba!2sPORIS%20MANSION%20EXCLUSIVE%202!5e0!3m2!1sid!2sid!4v1724125805918!5m2!1sid!2sid"
            allowfullscreen=""
            loading="lazy">
        </iframe>
    </div>

    <div class="location-info">
        <h3>XYZ Store</h3>

        <p>
            <i class="fa fa-map-marker"></i>
            Poris Mansion Exclusive 2
        </p>

        <p>
            <i class="fa fa-clock-o"></i>
            Buka setiap hari 08:00 - 21:00
        </p>

        <p>
            <i class="fa fa-phone"></i>
            0821-1494-3996
        </p>
    </div>

</div>

@endsection