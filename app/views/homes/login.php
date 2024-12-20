<?php require_once APPROOT.'/views/inc/header.php'; ?>
<?php require_once APPROOT.'/views/inc/navbar.php'; ?>

<div class="row justify-content-center">
    <div class="card col-9 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-xxl-4 mt-5">
    <div class="card-body">
        <?php if(Session::exists('registered')): ?>
            <div class="alert alert-success" role="alert">
                <?= Session::flash('registered') ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($err->cred)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $err->cred ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" novalidate>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" class="form-control <?= !empty($err->email) ? 'is-invalid' : '' ?>" name="email" value="<?= $data->email ?>" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                <div class="invalid-feedback"> <?= $err->email ?> </div>
            </div>
            <div class="mb-3"> 
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control <?= !empty($err->password) ? 'is-invalid' : '' ?>" name="password" id="exampleInputPassword1" required>
                <div class="invalid-feedback"> <?= $err->password ?> </div>
            </div>
       
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Submit</button>
                <a class="btn btn-secondary" type="button" href="<?= URLROOT ?>register">Dont have Account? Register</a>
            </div>
        </form>  

    </div>
    </div>
    
</div>

<?php require_once APPROOT.'/views/inc/footer.php'; ?>