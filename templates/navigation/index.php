<div class="app-navigation-new">
    <button type="button" class="icon-add" id="create-contact">
        <?php echo p($l->t('Add new contact')); ?>
    </button>
</div>
<ul>
    <li>
        <a href="#" class="icon-toggle-filelist" id="all-tag"><?php echo p($l->t('All Contacts')); ?>
            <div class="app-navigation-entry-utils">
                <ul>
                    <li class="app-navigation-entry-utils-counter counter"></li>
                    <li class="app-navigation-entry-utils-menu-button" id="create-tag-bp">
                        <button class="icon-add"></button>
                    </li>
                </ul>
            </div>
        </a>
        <div class="app-navigation-entry-menu" id="create-tag"></div>
    </li>
    <li>
        <a href="#" class="icon-favorite"><?php echo p($l->t('Favorites tags')); ?></a>
        <ul id="tag-favorite"></ul>
    </li>
    <li>
        <a href="#" class="icon-tag"><?php echo p($l->t('Tags')); ?></a>
        <ul id="tag-list"></ul>
    </li>
</ul>