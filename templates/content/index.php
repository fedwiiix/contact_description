<div class="app-content-list">
    <div id="contact-seach">
        <input type="search" placeholder="Search">
    </div>
    <div id="contact-list"></div>
</div>

<section id="contact-preview">

  

    <form id="tag-assign-form" class="autocomplete">
        <input id="assign-tag" type="text" placeholder="tag" autocomplete="off">
        <input type="submit" value="" class="icon-confirm">
    </form>
  
    <div id="tag-assigned-list">
        
        
    </div>





    <div>
        <span class="icon-more" id="preview-setting"></span>
        <div class="popovermenu" id="preview-menu">
            <ul>
                <li id="preview-menu-edit">
                    <a>
                        <span class="icon-rename"></span>
                        <span>Edit</span>
                    </a>
                </li>
                <li id="preview-menu-remove">
                    <a>
                        <span class="icon-delete"></span>
                        <span>Remove</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-6">
        <label>Name</label>
        <p id="name"></p>
        <label>Work</label>
        <p id="work"></p>
        <label>Birth</label>
        <p id="birth"></p>
    </div>
    <div class="col-6">
        <label>Last name</label>
        <p id="lastName"></p>
        <label>Hobbies</label>
        <p id="hobbies"></p>
        <div id="checkboxNotif">
            <input type="checkbox" id="birthNotif-preview" class="checkbox" disabled>
            <label for="birthNotif-preview">Birthday notif</label>
        </div>
    </div>
    <div class="col-12">
        <label>Description</label>
        <p id="description" class="markdown"></p>
    </div>
</section>

<section id="contact-form">
    <span class="icon-close" id="contact-form-close"></span>
    <form id="contact-insert-form" action="">
        <div class="col-6">
            <input type="text" id="id" hidden>
            <label>Name</label>
            <input type="text" id="name" require>
        </div>
        <div class="col-6">
            <label>Last name</label>
            <input type="text" id="lastName">
        </div>
        <div class="col-6">
            <label>Work</label>
            <input type="text" id="work">
        </div>
        <div class="col-6">
            <label>Hobbies</label>
            <input type="text" id="hobbies">
        </div>
        <div class="col-6">
            <label>Birth</label>
            <input type="date" id="birth">
        </div>
        <div class="col-6">
            <div style="margin:30px 20px;">
                <input type="checkbox" id="birthNotif" class="checkbox">
                <label for="birthNotif">Birthday notif</label>
            </div>
        </div>
        <div class="col-12">
            <label>Description</label>
            <textarea id="description" rows="15"></textarea>
        </div>
        <input type="submit" id="submit" value="Add Contact">
    </form>
</section>

<div id="toast"></div>