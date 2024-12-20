<?php require_once APPROOT.'/views/inc/header.php'; ?>
<?php require_once APPROOT.'/views/inc/navbar.php'; ?>

<div class="row justify-content-center" >
    <div class="card col-9 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-xxl-4 mt-5" data-bs-theme="light">
        <div class="card-body">
            <div class="mb-3" >
                <div class="alert alert-info" role="alert">
                    <a class="alert-link">Full Name:</a> <?= ucwords($data->name) ?>
                </div>
                <div class="alert alert-info" role="alert">
                    <a class="alert-link">Email:</a> <?= $data->email ?>
                </div>
                <div class="alert alert-info" role="alert">
                    <a class="alert-link">Joined on:</a> <?= Data::date($data->created_at) ?>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php require_once APPROOT.'/views/inc/footer.php'; ?>
