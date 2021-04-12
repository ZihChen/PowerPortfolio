
@include('layouts.bootstrap_cdn')
<div class="main">
    <div class="input-group">
        <form class="form-inline my-2 my-lg-0" role="search" method="get" action="/" target="_blank">
            <div class='search-bar-group'>
                <div class='input-text'>
                    <input type="text" class="form-control" name="keyword" placeholder="search">
                </div>
                <div class='submit-btn'>
                    <button type="submit" class="btn btn-secondary">搜尋</button>
                </div>
            </div>
        </form>
    </div>
    <div class="table-group">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">代號</th>
                <th scope="col">名稱</th>
                <th scope="col">股票類型</th>
                <th scope="col">收盤價</th>
                <th scope="col">股價漲跌幅</th>
                <th scope="col">K值</th>
                <th scope="col">D值</th>
                <th scope="col">RSI</th>
                <th scope="col">更新日期</th>
                <th scope="col">投資總額</th>
                <th scope="col">目標倉位</th>
                <th scope="col">平均開倉價</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stocks as $key => $stock)
                <tr>
                    <th scope="row">{{$key + 1}}</th>
                    <td>{{$stock['symbol']}}</td>
                    <td>{{$stock['name']}}</td>
                    <td>{{$stock['type']}}</td>
                    <td>{{$stock['close_price']}}</td>
                    <td>{{$stock['change_percent']}}%</td>
                    <td>{{$stock['stochastic_k']}}</td>
                    <td>{{$stock['stochastic_d']}}</td>
                    <td>{{$stock['rsi']}}</td>
                    <td>{{$stock['date']}}</td>
                    <td>${{$stock['invested']}}</td>
                    <td>{{$stock['target_position']}}%</td>
                    <td>${{$stock['avg_open']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .form-inline {
        position:fixed;
        margin-left: 20px;
    }
    .main {
        margin-top: 10px;
    }
    .table-group {
        width: 150%;
        margin-top: 50px;
    }
    .search-bar-group {
        width: 500px;
    }
    .input-text, .submit-btn {
        display: inline-block;
        width: 200px;
        height: 50px;
    }
</style>
