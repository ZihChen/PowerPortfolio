@include('layouts.bootstrap_cdn')

<body style="background-color: #F2F2F2"></body>

<div class="topnav">
    <a class="active" href="#home">Home</a>
    <a href="#news">News</a>
    <a href="#contact">Contact</a>
    <a href="#about">About</a>
</div>

<table class="table table-hover" style="width: 75%">
    <tr>
        <th rowspan="1">
        </th>
        <th colspan="4" style="text-align: center">{{$symbol}} - {{$name}}</th>
    </tr>
    <tr>
        <th>Latest Quarter</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <th>{{$fiscal_overview->latest_refresh}}</th>
        @endforeach
    </tr>
    <tr>
        <th>EPS</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->eps}}</td>
        @endforeach
    </tr>
    <tr>
        <th>P/E</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->pe_ratio}}</td>
        @endforeach
    </tr>
    <tr>
        <th>ROA(TTM)</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->roa_ttm * 100}} %</td>
        @endforeach
    </tr>
    <tr>
        <th>ROE(TTM)</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->roe_ttm * 100}} %</td>
        @endforeach
    </tr>
    <tr>
        <th>Profit Margin</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->profit_margin * 100}} %</td>
        @endforeach
    </tr>
    <tr>
        <th>Operating Margin(TTM)</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->operating_margin * 100}} %</td>
        @endforeach
    </tr>
    <tr>
        <th>EV/Revenue</th>
        @foreach($fiscal_overviews as $fiscal_overview)
            <td>{{$fiscal_overview->ev_to_revenue}}</td>
        @endforeach
    </tr>
</table>


<style>
    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    .topnav {
        overflow: hidden;
        background-color: #333;
    }

    .topnav a {
        float: left;
        color: #f2f2f2;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        font-size: 17px;
    }

    .topnav a:hover {
        background-color: #ddd;
        color: #00352C;
    }

    .topnav a.active {
        background-color: #60B390;
        color: white;
    }
    .table {
        font-size: small;
        table-layout : fixed;
    }
</style>
