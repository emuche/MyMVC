<?php require_once APPROOT.'/views/inc/header.php'; ?>
<?php require_once APPROOT.'/views/inc/navbar.php'; ?>
    
<div class="row justify-content-center">
<div class="card col-9 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-xxl-4 mt-5">
    <div class="card-body">
                
        <form method="POST" action="" novalidate>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Full Name</label>
            <input type="text" class="form-control <?= !empty($data->err->name) ? 'is-invalid' : '' ?>" name="name" value="<?= $data->name ?>" id="exampleInputEmail1" aria-describedby="emailHelp" required>
            <div class="invalid-feedback"> <?= $data->err->name ?> </div>
        </div>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" class="form-control <?= !empty($data->err->email) ? 'is-invalid' : '' ?>" name="email" value="<?= $data->email ?>" id="exampleInputEmail1" aria-describedby="emailHelp" required>
            <div class="invalid-feedback"><?= $data->err->email ?> </div>
        </div>
        <div class="mb-3"> 
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control <?= !empty($data->err->password) ? 'is-invalid' : '' ?>" name="password" id="exampleInputPassword1" required>
            <div class="invalid-feedback"> <?= $data->err->password ?> </div>
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
            <input type="password" class="form-control <?= !empty($data->err->confirm_password) ? 'is-invalid' : '' ?>" name="confirm_password" id="exampleInputPassword1" required>
            <div class="invalid-feedback"> <?= $data->err->confirm_password ?> </div>
        </div>
        <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Register</button>
                <a class="btn btn-secondary" type="button" href="<?= URLROOT ?>login">Have Account? Login</a>
            </div>
        </form>  

    </div>
    </div>
    
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>