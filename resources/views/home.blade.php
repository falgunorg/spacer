@extends('layouts.master')

@section('top')
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <!-- Log on to codeastro.com for more projects! -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ \App\User::count() }}</h3>

                <p>System Users</p>
            </div>
            <div class="icon">
                <i class="fa fa-user-secret"></i>
            </div>
            <a href="{{ route('user.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ \App\Location::count() }}<sup style="font-size: 20px"></sup></h3>

                <p>Locations</p>
            </div>
            <div class="icon">
                <i class="fa fa-list"></i>
            </div>
            <a href="" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ \App\Cabinet::count() }}</h3>

                <p>Cabinets</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('cabinets.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ \App\Item::count() }}</h3>
                <p>Items</p>
            </div>
            <div class="icon">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('items.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    
    <!-- ./col -->
</div>
<!-- Log on to codeastro.com for more projects! -->


   
   
    <!-- ./col -->
    <div id="container" class=" col-xs-6"></div>


@endsection

@section('top')
@endsection
