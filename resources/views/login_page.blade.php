
@include('layouts.bootstrap_cdn')
<div>
    <form class="row-form" action="{{"/login"}}" method="post">
        <div class="col-auto" >
            <label for="staticEmail2" class="visually-hidden">Email</label>
            <input type="text" class="form-control" id="staticEmail2" placeholder="Email" name="email">
        </div>
        <div class="col-auto">
            <label for="inputPassword2" class="visually-hidden">Password</label>
            <input type="password" class="form-control" id="inputPassword2" placeholder="Password" name="password">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary mb-3">登入帳號</button>
        </div>
        <div class="link">
            <a href="register" class="btn btn-link">註冊帳號</a>
        </div>
    </form>
</div>


<a></a>

<style>
    .row-form {
        position:fixed;
    }
    .col-auto {
        margin-top: 20px;
        margin-left: 20px;
        width: 100%;
    }
    .link {
        text-align: right;
        margin-top: -55px;
    }
</style>
