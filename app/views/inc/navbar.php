<nav class="navbar navbar-expand-lg bg-body-secondary" data-bs-theme="dark">
    <div class="container container-fluid">
            <?php if(Logged::in()): ?>
                <a class="navbar-brand" href="<?= URLROOT ?>/welcome/<?= Session::get('email') ?>">myMVC</a>
            <?php else: ?>
                <a class="navbar-brand" href="<?= URLROOT ?>">myMVC</a>
            <?php endif; ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if(!Logged::in()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URLROOT ?>/login">Account</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT ?>/about">About</a>
                    </li>
                </ul>
                <?php if(Logged::in()): ?>
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URLROOT .'/profile/'. Session::get('email') ?>"><?= ucwords(Session::get('name')) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URLROOT ?>/logout">Logout</a>
                        </li>
                    </ul>
                    <?php endif; ?>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">
                      Search
                    </button>
                </form>
            </div>
        </div>
</nav>