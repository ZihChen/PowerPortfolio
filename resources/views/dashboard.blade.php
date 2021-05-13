
@include('layouts.bootstrap_cdn')

<body style="background-color: #F2F2F2"></body>
<div class="main" style="">
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
                <th scope="col">
                    <i class="fas fa-sync-alt"></i>
                </th>
                <th scope="col">代號</th>
                <th scope="col">收盤價</th>
                <th scope="col">KD</th>
                <th scope="col">RSI</th>
                <th scope="col">目標價</th>
                <th scope="col">投資紀錄</th>
                <th scope="col">市值</th>
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
                            <a href="/stocks/{{$stock['id']}}/overview" style="text-decoration: none; color: #00352C">{{$stock['symbol']}}</a>
                        </strong><br>
                        {{$stock['name']}}
                    </td>
                    <td>{{$stock['close_price']}}<br>
                    @if($stock['change_percent'] >= 0)
                        <a style="color: yellowgreen; font-size: smaller">{{$stock['change_percent']}}%</a>
                    @else
                        <a style="color: #ff413c; font-size: smaller">{{$stock['change_percent']}}%</a>
                    @endif
                    </td>
                    <td>
                        <div style="position: relative; float:left ; top: 8px">
                            @foreach($stock['kd_diffs'] as $diff_value)

                                @if ($diff_value >= 1.3)
                                    <i class="fas fa-square" style="color: #14B45A"></i>
                                @elseif($diff_value < 1.3 && $diff_value >= 1.15)
                                    <i class="fas fa-square" style="color: #64D76E"></i>
                                @elseif($diff_value < 1.15 && $diff_value >= 1.05)
                                    <i class="fas fa-square" style="color: #AFFF8C"></i>
                                @elseif($diff_value < 1.05 && $diff_value >= 1)
                                    <i class="fas fa-square" style="color: #D2FFBE"></i>
                                @elseif($diff_value < 1 && $diff_value >= 0.95)
                                    <i class="fas fa-square" style="color: #FFDCDC"></i>
                                @elseif($diff_value < 0.95 && $diff_value >= 0.85)
                                    <i class="fas fa-square" style="color: #FFBEBE"></i>
                                @elseif($diff_value < 0.85 && $diff_value >= 0.7)
                                    <i class="fas fa-square" style="color: #FF9191"></i>
                                @else
                                    <i class="fas fa-square" style="color: #FF6E6E"></i>
                                @endif
                            @endforeach
                        </div>
                        <div style="position: relative; float:left ; top: 3px ; left: 5px">
                            <a style="font-size: smaller">{{$stock['stochastic_k']}}</a><br>
                            <a style="font-size: smaller">{{$stock['stochastic_d']}}</a>
                        </div>
                    </td>
                    <td>
                        <div class="rsi-column-left">
                            @foreach($stock['rsi_records'] as $record)

                                @if ($record >= 70 && $record <= 100)
                                    <i class="fas fa-square" style="color: #14B45A"></i>
                                @elseif($record < 70 && $record >= 60)
                                    <i class="fas fa-square" style="color: #64D76E"></i>
                                @elseif($record < 60 && $record >= 55)
                                    <i class="fas fa-square" style="color: #AFFF8C"></i>
                                @elseif($record < 55 && $record >= 50)
                                    <i class="fas fa-square" style="color: #D2FFBE"></i>
                                @elseif($record < 50 && $record >= 45)
                                    <i class="fas fa-square" style="color: #FFDCDC"></i>
                                @elseif($record < 45 && $record >= 40)
                                    <i class="fas fa-square" style="color: #FFBEBE"></i>
                                @elseif($record < 40 && $record >= 30)
                                    <i class="fas fa-square" style="color: #FF9191"></i>
                                @else
                                    <i class="fas fa-square" style="color: #FF6E6E"></i>
                                @endif
                            @endforeach
                        </div>
                        <div class="rsi-column-right">
                            <a style="font-size: smaller">{{$stock['latest_rsi']}}</a>
                        </div>
                    </td>
                    <td>
                        <i class="fab fa-envira"></i>{{$stock['rsi_fifty_target_price']}}<br>
                        <i class="fas fa-seedling"></i>{{$stock['rsi_thirty_target_price']}}
                    </td>
                    <td>
                        <button type="button" class="collapsible" style="width: 85px; text-align: center">
                            <i class="fas fa-edit"></i>
                        </button>
                        <div class="content">
                            <p style="font-size: smaller">
                                目標倉位<a style="font-size: x-small">(%)</a><br>
                                <input class = "position-input" type="text" id="target-position-{{$key}}" name="fname" style="width: 79px" value="{{$stock['target_position']}}" onclick="storeTargetPosition({{$key}}, {{$stock['id']}})">
                            </p>
                            <p style="font-size: smaller">
                                平均開倉價<a style="font-size: x-small">($)</a><br>
                                <input class = "position-input" type="text" id="avg-open-{{$key}}" name="fname" style="width: 79px" value="{{$stock['avg_open']}}" onclick="storeAvgOpen({{$key}}, {{$stock['id']}})">
                            </p>
                            <p style="font-size: smaller">
                                投資單位<a style="font-size: x-small">(股)</a><br>
                                <input class = "position-input" type="text" id="units-{{$key}}" name="fname" style="width: 79px" value="{{$stock['units']}}" onclick="storeUnits({{$key}}, {{$stock['id']}})">
                            </p>
                        </div>
                    </td>
                    <td>${{$stock['invested']}}</td>
                    <td>${{$stock['profit_loss_value']}}<br>
                        @if($stock['profit_loss_percent'] >= 0)
                            <a style="color: yellowgreen; font-size: smaller">{{$stock['profit_loss_percent']}}%</a>
                        @else
                            <a style="color: #ff413c; font-size: smaller">{{$stock['profit_loss_percent']}}%</a>
                        @endif
                    </td>
                    <td>
                        <a style="color: #60B390" onclick="remove({{$stock['id']}})">
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
    .position-input {
        text-align: center;
        padding: 3px;
        font-size: small;
        margin-left: 3px;
        margin-right: 3px;
        color: #218BC3;
    }
    .collapsible {
        background-color: #60B390;
        color: white;
        cursor: pointer;
        padding: 5px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
    }

    .active, .collapsible:hover {
        background-color: #60B390;
    }

    .content {
        width: 85px;
        margin-bottom: 0;
        display: none;
        overflow: hidden;
        background-color: white;
    }
    .rsi-column-left {
        position: relative;
        float:left;
        top: 8px;
    }
    .rsi-column-right {
        position: relative;
        float:left;
        top: 8px;
        left: 5px;
    }
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
        background-color: white;
        padding: 10px;
        font-size: 16px;
    }
    input[type=text] {
        background-color: white;
        width: 100%;
    }
    input[type=submit] {
        background-color: #60B390;
        border-color: #60B390;
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
        background-color: #60B390;
        color: white;
    }

    .pagination a:hover:not(.active) {background-color: #ddd;}
</style>

<script>

    function storeTargetPosition(key, id) {

        const targetElement = document.getElementById('target-position-' + key)
        const avgOpenElement = document.getElementById('avg-open-' + key)
        const unitsElement = document.getElementById('units-' + key)

        targetElement.addEventListener('focusout', function (event) {

            target_position = targetElement.value
            avg_open = avgOpenElement.value
            units = unitsElement.value

            let formData = new FormData();

            formData.append('target_position', target_position);
            formData.append('avg_open', avg_open);
            formData.append('units', units);

            fetch('stocks/' + id + '/position', {
                body: formData,
                method: 'post'
            }).then(function (response) {

                return response.json();

            }).then(function (myJson) {

                this.data = myJson

                return this.data
            });
        });
    }

    function storeAvgOpen(key, id) {

        const targetElement = document.getElementById('target-position-' + key)
        const avgOpenElement = document.getElementById('avg-open-' + key)
        const unitsElement = document.getElementById('units-' + key)

        avgOpenElement.addEventListener('focusout', function (event) {

            target_position = targetElement.value
            avg_open = avgOpenElement.value
            units = unitsElement.value

            let formData = new FormData();

            formData.append('target_position', target_position);
            formData.append('avg_open', avg_open);
            formData.append('units', units);

            fetch('stocks/' + id + '/position', {
                body: formData,
                method: 'post'
            }).then(function (response) {

                return response.json();

            }).then(function (myJson) {

                this.data = myJson

                return this.data
            });
        });
    }

    function storeUnits(key, id) {

        const targetElement = document.getElementById('target-position-' + key)
        const avgOpenElement = document.getElementById('avg-open-' + key)
        const unitsElement = document.getElementById('units-' + key)

        unitsElement.addEventListener('focusout', function (event) {

            target_position = targetElement.value
            avg_open = avgOpenElement.value
            units = unitsElement.value

            let formData = new FormData();

            formData.append('target_position', target_position);
            formData.append('avg_open', avg_open);
            formData.append('units', units);

            fetch('stocks/' + id + '/position', {
                body: formData,
                method: 'post'
            }).then(function (response) {

                return response.json();

            }).then(function (myJson) {

                this.data = myJson

                return this.data
            });
        });
    }

    function remove(id) {

        fetch('stocks/' + id + '/delete', {
            method: 'delete'
        }).then(function (response) {

            return response.json();

        }).then(function (myJson) {

            this.data = myJson

            return this.data
        });

        parent.document.location.reload();
    }

    function sort() {

        var o = document.getElementById("option-order");
        var order_value = o.value;

        var c = document.getElementById("option-column");
        var column_value = c.value;

        window.location.href = '/dashboard?' + 'column=' + column_value + '&' + 'order=' + order_value;
    }

    function autocomplete(inp) {
        var currentFocus;
        inp.addEventListener("input", function (e) {

            var a, b, i, val = this.value;

            closeAllLists();
            if (!val) {
                return false;
            }
            currentFocus = -1;
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");

            this.parentNode.appendChild(a);
            const match_stock = fetch('stocks/search?' + new URLSearchParams({
                keyword: this.value,
            }))
                .then(function (response) {

                    return response.json();

                })
                .then(function (myJson) {

                    this.data = myJson

                    return this.data
                });

            Promise.resolve(match_stock).then(function (result) {

                for (i = 0; i <= result.length - 1; i++) {

                    b = document.createElement("DIV");

                    b.innerHTML += "<strong style='font-size: small'>" + result[i].symbol + "</br>"
                    b.innerHTML += "<a style='font-size: smaller'>" + result[i].name;
                    b.innerHTML += "<input style='font-size: small' type='hidden' value='" + result[i].symbol + "'>";

                    b.addEventListener("click", function (e) {
                        inp.value = this.getElementsByTagName("input")[0].value;
                        closeAllLists();
                    });

                    a.appendChild(b);
                }
            })

        });

        inp.addEventListener("keydown", function (e) {
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

<script>
    var coll = document.getElementsByClassName("collapsible");
    var i;

    for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }
</script>
