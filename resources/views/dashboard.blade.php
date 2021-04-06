
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
                <th scope="col">當日收盤價</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
            </tr>
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
