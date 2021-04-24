
@include('layouts.bootstrap_cdn')

<div class="main">
    <div class="sort-btn">
        <input type="submit" class="btn btn-primary mb-3" value="確認" onclick="sort()">
    </div>
    <div class="right">
        <select id="option-order" class="form-select" aria-label="Default select example" name="order">
            <option value="asc">由低到高</option>
            <option value="desc">由高到低</option>
        </select>
    </div>
    <div class="left">
        <select id="option-column" class="form-select" aria-label="Default select example" name="column">
            <option value="symbol">代號</option>
            <option value="rsi">RSI</option>
            <option value="change_percent">漲跌幅</option>
        </select>
    </div>
    <form autocomplete="off" action="{{"/stocks"}}" method="post">
        <div class="autocomplete" style="width:250px;">
            <input id="myInput" type="text" name="symbol" placeholder="代號">
        </div>
        <input style="color: white" type="submit" name="click">
    </form>
    <div class="table-group">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col">##</th>
                <th scope="col">代號</th>
                <th scope="col">收盤價</th>
                <th scope="col">漲跌幅</th>
                <th scope="col">K值</th>
                <th scope="col">D值</th>
                <th scope="col">RSI</th>
                <th scope="col">目標倉位</th>
                <th scope="col">投資單位</th>
                <th scope="col">平均開倉價</th>
                <th scope="col">投資總額</th>
                <th scope="col">損益百分比</th>
                <th scope="col">損益總額</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($stocks as $key => $stock)
                <tr>
                    <th scope="row">
                        @if ($stock['is_update'] == true)
                            <i class="fas fa-check" style="color: yellowgreen"></i>
                        @else
                            <i class="fas fa-times" style="color: #ff413c"></i>
                        @endif
                    </th>
                    <td>@if ($stock['type'] == 'Equity')
                        <i class="fas fa-cube"></i>
                        @else
                        <i class="fas fa-cubes"></i>
                        @endif
                        <strong>
                            {{$stock['symbol']}}
                        </strong><br>
                        {{$stock['name']}}
                    </td>
                    <td>{{$stock['close_price']}}</td>
                        @if($stock['change_percent'] >= 0)
                        <td style="color: yellowgreen">{{$stock['change_percent']}}%</td>
                        @else
                        <td style="color: #ff413c">{{$stock['change_percent']}}%</td>
                        @endif
                    <td>{{$stock['stochastic_k']}}</td>
                    <td>{{$stock['stochastic_d']}}</td>
                    <td>{{$stock['rsi']}}</td>
                    <td>{{$stock['target_position']}}%</td>
                    <td>{{$stock['units']}}</td>
                    <td>${{$stock['avg_open']}}</td>
                    <td>${{$stock['invested']}}</td>
                    <td>{{$stock['profit_loss_percent']}}%</td>
                    <td>${{$stock['profit_loss_value']}}</td>
                    <td>
                        <a style="color: lightskyblue" href="/stocks/{{$stock['id']}}/delete" data-method="delete">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <a href="dashboard?page={{$current_page - 1}}">&laquo;</a>
        @for($i = 1 ; $i <= $total_pages ; $i++)
            @if ($current_page == $i)
                <a class="active" href="#">{{$i}}</a>
            @else
                <a href="dashboard?page={{$i}}">{{$i}}</a>
            @endif
        @endfor
        <a href="dashboard?page={{$current_page + 1}}">&raquo;</a>
    </div>
</div>

<style>
    .main {
        margin-top: 20px;
        margin-left: 20px;
        margin-right: 20px;
    }
    .left {
        height: 40px;
        width: 150px;
        margin-left: 10px;
        float: right;
    }
    .right {
        height: 40px;
        width: 150px;
        margin-left: 10px;
        float: right;
    }
    .sort-btn {
        height: 40px;
        width: 100px;
        margin-left: 10px;
        float: right;
    }
    .table {
        font-size: smaller;
    }
    .table-group {
        width: 100%;
        margin-top: 20px;
    }
    * { box-sizing: border-box; }
    body {
        font: 16px Arial;
    }
    .autocomplete {
        position: relative;
        display: inline-block;
    }
    input {
        border: 1px solid transparent;
        background-color: #f1f1f1;
        padding: 10px;
        font-size: 16px;
    }
    input[type=text] {
        background-color: #f1f1f1;
        width: 100%;
    }
    input[type=submit] {
        background-color: lightskyblue;
        border-color: lightskyblue;
        /*color: #fff;*/
    }
    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        top: 100%;
        left: 0;
        right: 0;
    }
    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }
    .pagination {
        display: inline-block;
        float: right;
    }

    .pagination a {
        color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
    }

    .pagination a.active {
        background-color: lightskyblue;
        color: white;
    }

    .pagination a:hover:not(.active) {background-color: #ddd;}
</style>

<script>
    function sort() {

        var o = document.getElementById("option-order");
        var order_value = o.value;

        var c = document.getElementById("option-column");
        var column_value = c.value;

        window.location.href = '/dashboard?' + 'column=' + column_value + '&' + 'order=' + order_value;
    }

    function autocomplete(inp) {
        var currentFocus;
        inp.addEventListener("input", function(e) {

            var a, b, i, val = this.value;

            closeAllLists();
            if (!val) { return false;}
            currentFocus = -1;
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");

            this.parentNode.appendChild(a);
            const match_stock = fetch('stocks/search?'+ new URLSearchParams({
                keyword: this.value,
            }))
                .then(function(response) {

                    return response.json();

                })
                .then(function(myJson) {

                    this.data = myJson

                    return this.data
                });

            Promise.resolve(match_stock).then(function(result) {

                for (i = 0; i <= result.length - 1; i++) {

                    b = document.createElement("DIV");

                    b.innerHTML += "<strong style='font-size: small'>" + result[i].symbol + "</br>"
                    b.innerHTML += "<a style='font-size: smaller'>"+ result[i].name;
                    b.innerHTML += "<input style='font-size: small' type='hidden' value='" + result[i].symbol + "'>";

                    b.addEventListener("click", function(e) {
                        inp.value = this.getElementsByTagName("input")[0].value;
                        closeAllLists();
                    });

                    a.appendChild(b);
                }
            })

        });

        inp.addEventListener("keydown", function(e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                currentFocus++;
                addActive(x);
            } else if (e.keyCode == 38) {
                currentFocus--;
                addActive(x);
            } else if (e.keyCode == 13) {
                e.preventDefault();
                if (currentFocus > -1) {

                    if (x) x[currentFocus].click();
                }
            }
        });

        function addActive(x) {
            if (!x) return false;
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            x[currentFocus].classList.add("autocomplete-active");
        }

        function removeActive(x) {
            for (var i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }

        function closeAllLists(elmnt) {
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }

        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
    }

    autocomplete(document.getElementById("myInput"));
</script>
