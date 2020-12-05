<div class="app-content-list">
    <div id="contact-seach">
        <input type="search" placeholder="<?php echo p($l->t('Search')); ?>">
    </div>
    <div id="contact-list"></div>
</div>

<section id="contact-preview">
    <div class="header">
        <span class="title"></span>
        <span class="icon-more" id="preview-setting"></span>
        <div>
            <div class="popovermenu" id="preview-menu">
                <ul>
                    <li id="preview-menu-edit">
                        <a>
                            <span class="icon-rename"></span>
                            <span><?php echo p($l->t('Edit')); ?></span>
                        </a>
                    </li>
                    <li id="preview-menu-link">
                        <a>
                            <span class="icon-link"></span>
                            <span><?php echo p($l->t('Links')); ?></span>
                        </a>
                    </li>
                    <li id="preview-menu-remove">
                        <a>
                            <span class="icon-delete"></span>
                            <span><?php echo p($l->t('Remove')); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tag-preview">
        <div id="tag-assigned-list"></div>
        <form id="tag-assign-form" class="autocomplete">
            <input id="tag-assign" type="text" placeholder="tag" autocomplete="off">
            <input type="submit" value="" class="icon-confirm">
        </form>
    </div>
    <div class="col-6">
        <label><?php echo p($l->t('Work')); ?></label>
        <p id="work"></p>
        <label><?php echo p($l->t('Birth')); ?></label>
        <p id="birth"></p>
    </div>
    <div class="col-6">
        <label><?php echo p($l->t('Hobbies')); ?></label>
        <p id="hobbies"></p>
        <div id="checkboxNotif">
            <input type="checkbox" id="birthNotif-preview" class="checkbox" disabled>
            <label for="birthNotif-preview"><?php echo p($l->t('Birthday notif')); ?></label>
        </div>
    </div>
    <div class="col-12">
        <label><?php echo p($l->t('Address')); ?></label>
        <p id="address"></p>
        <label><?php echo p($l->t('Description')); ?></label>
        <p id="description" class="markdown"></p>
    </div>
    <div class="link-preview">

        <div class="app-content-list link">
            <div id="link-list"></div>
        </div>
    </div>
</section>

<section id="contact-form">
    <div class="header">
        <span class="title"></span>
        <span class="icon-close" id="contact-form-close"></span>
    </div>
    <form id="contact-insert-form" action="">
        <div class="col-6">
            <input type="text" id="id" hidden>
            <label><?php echo p($l->t('Name')); ?></label>
            <input type="text" id="name" require>
        </div>
        <div class="col-6">
            <label><?php echo p($l->t('Last name')); ?></label>
            <input type="text" id="lastName">
        </div>
        <div class="col-6">
            <label><?php echo p($l->t('Work')); ?></label>
            <input type="text" id="work">
        </div>
        <div class="col-6">
            <label><?php echo p($l->t('Hobbies')); ?></label>
            <input type="text" id="hobbies">
        </div>
        <div class="col-6">
            <label><?php echo p($l->t('Birth')); ?></label>
            <input type="date" id="birth">
        </div>
        <div class="col-6">
            <div style="margin:30px 20px;">
                <input type="checkbox" id="birthNotif" class="checkbox">
                <label for="birthNotif"><?php echo p($l->t('Recall birth')); ?></label>
            </div>
        </div>
        <div class="col-12">
            <label><?php echo p($l->t('Address')); ?></label>
            <input type="text" id="address">
            </div>
            <div class="col-12">
            <label><?php echo p($l->t('Description')); ?></label>
            <textarea id="description" rows="15"></textarea>
        </div>
        <input type="submit" id="submit" value="">
    </form>
</section>

<section id="contact-link" class="col-12">
    <div class="header">
        <span class="title"></span>
        <span class="icon-close" id="contact-form-close"></span>
        <span class="icon-history" id="contact-link-add"></span>
    </div>
    <form id="contact-link-form" class="autocomplete">
        <div class="col-6">
            <label><?php echo p($l->t('Contact')); ?></label>
            <input type="text" id="link-assign" placeholder="<?php echo p($l->t('Contact')); ?>" autocomplete="off">
        </div>
        <div class="col-6">
            <label><?php echo p($l->t('Type')); ?></label>
            <select id="link-select"></select>
        </div>
        <div class="col-6 birth">
            <label><?php echo p($l->t('Birth')); ?></label>
            <input type="date" id="link-birth">
        </div>
        <div class="col-6 birth">
            <div style="margin:30px 20px;">
                <input type="checkbox" id="link-birthNotif" class="checkbox">
                <label for="link-birthNotif"><?php echo p($l->t('Recall birth')); ?></label>
            </div>
        </div>
        <input type="submit" id="submit" value="">
    </form>
    <div class="app-content-list link">
        <div id="link-list"></div>
    </div>
</section>

<div id="toast"></div>