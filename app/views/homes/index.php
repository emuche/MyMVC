<?php require_once APPROOT.'/views/inc/header.php'; ?>
<?php require_once APPROOT.'/views/inc/navbar.php'; ?>

<div class="row justify-content-center" >
    <div class="card col-9 col-sm-8 col-md-7 col-lg-6 col-xl-5 col-xxl-4 mt-5" data-bs-theme="light">
        <div class="card-body">
            <div class="mb-3" >
              <figure class="text-end">
                    <blockquote class="blockquote">
                        <p>
                            <h1 class="display-4">myMVC</h1>
                        </p>
                    </blockquote>
                    <figcaption class="blockquote-footer mt-4">
                        Welcome to myMVC <cite title="Source Title">Version <?=APPVERSION?> &copy; 2024</cite>
                    </figcaption>
                </figure>
            </div>
            
        </div>
    </div>
</div>
<?php require_once APPROOT.'/views/inc/footer.php'; ?>