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
        <button type="submit" class="btn btn-primary mb-3">Confirm identity</button>
    </div>
</form>

@include('layouts.bootstrap_cdn')

<style>
    .row-form {
        margin-top: 20px;
        margin-left: 20px;
        width: 25%;
    }
    .col-auto {
        margin-top: 10px;
    }
</style>
