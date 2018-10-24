@extends('wx.layout')
@section('title')
    购物车
@endsection
@section('content')
    <div class="ShoppingCart">
        <img src="/wx/images/shoppingcart.png">
        <h3 class="info">购物车竟然是空的！</h3>
        <p>再忙，也要记得买点什么犒劳自己~ </p>
        <a class="btnOrange button" href="/">去首页看看</a>
    </div>
@section('bottom_bar')
    @include('wx.bottom_bar')
@endsection

@endsection
@section('javascript')

@endsection