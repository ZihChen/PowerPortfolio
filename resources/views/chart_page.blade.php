
@include('layouts.bootstrap_cdn')

<div class="layui-col-xs12 layui-col-sm6 layui-col-md6" style="width: 80%">
    {!! $quote_chart->container() !!}
    {!! $quote_chart->script() !!}
</div>

<div class="layui-col-xs12 layui-col-sm6 layui-col-md6" style="width: 80%">
    {!! $kd_chart->container() !!}
    {!! $kd_chart->script() !!}
</div>

<div class="layui-col-xs12 layui-col-sm6 layui-col-md6" style="width: 80%">
    {!! $rsi_chart->container() !!}
    {!! $rsi_chart->script() !!}
</div>


