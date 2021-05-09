
@include('layouts.bootstrap_cdn')

<body style="background-color: #F2F2F2"></body>

<div style="text-align: center; margin-top: 20px">
    <img src="https://thumbs2.imgbox.com/da/b5/cypiFI7g_t.png">
</div>
<div id="authBox">
    <div class="logo">
        <div class="login">
            <div class="title">Member Login</div>
            <div>
                <form class="row-form" action="{{"/login"}}" method="post">
                    @csrf
                    <div class="col-auto" >
                        <label for="staticEmail2" class="visually-hidden">Email</label>
                        <input type="text" class="form-control" id="staticEmail2" placeholder="Email" name="email">
                    </div>
                    <div class="col-auto">
                        <label for="inputPassword2" class="visually-hidden">Password</label>
                        <input type="password" class="form-control" id="inputPassword2" placeholder="Password" name="password">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-3" style="background-color: #60B390; border-color: #60B390">登入帳號</button>
                    </div>
                    <div class="link">
                        <a href="register" class="btn btn-link" style="color: #60B390">註冊帳號</a>
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

    .col-auto {
        margin-top: 20px;
        width: 100%;
    }
    .link {
        text-align: right;
        margin-top: -55px;
    }
</style>
