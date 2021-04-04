
@include('layouts.bootstrap_cdn')

<form class="row-form" action="{{"/register"}}" method="post">
    <div class="col-auto" >
        <label for="staticEmail2" class="visually-hidden">Name</label>
        <input type="text" class="form-control" id="staticEmail2" placeholder="Name" name="name">
    </div>
    <div class="col-auto">
        <label for="inputPassword2" class="visually-hidden">Email</label>
        <input type="text" class="form-control" id="inputPassword2" placeholder="Email" name="email">
    </div>
    <div class="col-auto">
        <label for="inputPassword2" class="visually-hidden">Password</label>
        <input type="password" class="form-control" id="inputPassword2" placeholder="Password" name="password">
    </div>
    <div class="col-auto">
        <label for="inputPassword2" class="visually-hidden">Password Confirmation</label>
        <input type="password" class="form-control" id="inputPassword2" placeholder="Password Confirmation" name="password_confirmation">
    </div>

    <div class="col-auto">
        <button type="submit" class="btn btn-primary mb-3">註冊</button>
    </div>
    <div class="cancel">
        <a href="/login" class="btn btn-light">取消</a>
    </div>
</form>

<style>
    .row-form {
        text-align: center;
        margin-top: 100px;
        margin-left: 20px;
    }
    .col-auto {
        margin-top: 10px;
        margin-left: 500px;
        margin-right: 500px;
    }
    .btn-primary {
        margin-left: -325px;
    }
    .cancel {
        margin-top: -54px;
        margin-left: -170px;
    }
</style>