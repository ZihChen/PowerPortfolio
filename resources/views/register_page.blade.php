
@include('layouts.bootstrap_cdn')

<body style="background-color: #F2F2F2"></body>

<div style="text-align: center; margin-top: 20px">
    <img src="https://thumbs2.imgbox.com/da/b5/cypiFI7g_t.png">
</div>
<div id="authBox">
    <div class="logo">
        <div class="login">
            <div class="title">Register</div>
                <div>
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
                            <button type="submit" class="btn btn-primary mb-3" style="background-color: #60B390; border-color: #60B390">註冊</button>
                            <div class="cancel">
                                <a href="/login" class="btn btn-light" style="color: #212529">取消</a>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>

<style>
    #authBox .loginBox .login {
        margin-top: 16px;
    }
    #authBox .title {
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        color: #000;
    }
    #authBox {
        max-width: 480px;
        margin: 10px auto 64px auto;
        padding: 24px 64px;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 30%);
    }
    #authBox .logo {
        text-align: center;
    }
    /*.row-form {*/
        /*position:fixed;*/
    /*}*/
    .col-auto {
        margin-top: 20px;
    }
    .cancel {
        float: right;
        margin-left: -100px;
    }
</style>
