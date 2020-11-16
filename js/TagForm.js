class TagFormClass {

    constructor() {
        this.baseUrl = OC.generateUrl('/apps/contact_description');
    }

    init() {


        this.getAndDisplayTags();
        this.initCreateTag();

        var self = this;
        $(window).click(function() {
            self.hideEntryMenu();
        });

    }

    getAndDisplayTags() {
        var self = this;
        $.ajax({
            url: this.baseUrl + '/tag',
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            if (response.length) {
                response.forEach(element => {
                    self.displayTag(element)
                });
            }
        }).fail(function(response, code) {});
    }


    displayTag(element) {
        var html = `<li id="tag-${element.id}">
            <a href="#"><span id="label-tag-${element.id}">${element.tagName}</span>
                <div class="app-navigation-entry-utils">
                    <ul>
                        <li class="app-navigation-entry-utils-counter">1</li>
                        <li class="app-navigation-entry-utils-menu-button" id="tag-open-menu-${element.id}">
                            <button></button>
                        </li>
                    </ul>
                </div>
            </a>
            <div class="app-navigation-entry-menu" id="tag-menu-${element.id}">
                <ul>
                    <li>
                        <a href="#" id="favorite-tag-${element.id}">
                            <span class="icon-add"></span>
                            <span>${(element.favorite==1?'Remove':'Add')} to favorite</span>
                            <form class="hidden">
                                <input id="input-folder" type="text" value="New tag">
                                <input type="submit" value=" " class="icon-confirm">
                            </form>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="remove-tag-${element.id}">
                            <span class="icon-delete"></span>
                            <span>Remove</span>
                        </a>
                    </li>
                    <li>
                        <form class="tag-edit-form" id="edit-tag-${element.id}">
                            <input id="input-tag-${element.id}" type="text" value="${element.tagName}">
                            <input type="submit" value=" " class="icon-confirm">
                        </form>
                    </li>
                </ul>
            </div>
        </li>`

        $(`#tag-${(element.favorite==1?'favorite':'list')}`).prepend(html)

        var self = this;
        $(`#tag-open-menu-${element.id}`).click(function(event) {
            event.stopPropagation();
            self.hideEntryMenu();
            $(`#tag-menu-${element.id}`).show();
        });

        $(`#tag-menu-${element.id}`).click(function(event) {
            event.stopPropagation();
        });

        $(`#favorite-tag-${element.id}`).click(function() {
            self.addToFavorite(element.id, element.favorite == 1 ? 0 : 1)
        });

        $(`#remove-tag-${element.id}`).click(function() {
            self.remove(element.id)
        });

        $(`#edit-tag-${element.id}`).submit(function(event) {
            event.preventDefault()
            let tagName = $(`#input-tag-${element.id}`).val();
            self.update(element.id, tagName);
        });



    }

    hideEntryMenu() {
        if ($('.app-navigation-entry-menu').is(":visible")) {
            $('.app-navigation-entry-menu').hide();
        }
    }

    /********************************************* */

    initCreateTag() {

        let self = this;
        $("#tag-create-form").submit(function(event) {
            event.preventDefault()
            let tagName = $('#input-create-tag').val();
            self.createTag(tagName);
        });

        $('#create-tag').click(function(event) {
            event.stopPropagation();
        });

        $('#create-tag-bp').click(function(event) {
            event.stopPropagation();
            self.hideEntryMenu();
            self.showCreateTagMenu()
        })

        $('#input-create-tag').mouseover(function() {
            $(this).focus()
        });
    }

    showCreateTagMenu() {
        $('#create-tag').show();
        $('#input-create-tag').focus().val('');
    }

    createTag(tagName) {

        if (tagName.length < 3) { return; }
        var self = this;
        $.ajax({
            url: this.baseUrl + '/tag',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ tagName })
        }).done(function(response) {
            self.hideEntryMenu();
            self.displayTag(response)
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This tag already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }

    update(id, tagName) {
        var self = this;
        $.ajax({
            url: this.baseUrl + '/tag/' + id,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ tagName })
        }).done(function(response) {
            self.hideEntryMenu();
            $(`#label-tag-${response.id}`).html(response.tagName)
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }

    addToFavorite(id, favorite) {
        var self = this;
        $.ajax({
            url: this.baseUrl + '/tagfavorite/' + id,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ favorite })
        }).done(function(response) {
            self.hideEntryMenu();
            self.removeTag(id);
            self.displayTag(response);
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }

    remove(id) {
        var self = this;
        confirmToast("Are you sure?", function() {
            $.ajax({
                url: self.baseUrl + '/tag/' + id,
                type: 'DELETE',
                contentType: 'application/json',
            }).done(function(response) {
                self.hideEntryMenu();
                self.removeTag(id)
            }).fail(function(response, code) {
                toast("An error occurred.", 4);
            });
        })
    }

    removeTag(id) {
        $(`#tag-${id}`).remove()
    }

}

var TagForm = new TagFormClass();