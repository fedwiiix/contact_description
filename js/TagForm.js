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
            url: this.baseUrl + '/taglist',
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            if (response.length) {
                response.forEach(element => {
                    self.displayTag(element)
                });
            }
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }


    displayTag(element) {
        var html = `<li id="tag-${element.id}">
            <div class="app-navigation-entry-bullet" id="tag-bullet-${element.id}" style="background:${element.color}"></div>
            <a href="#"><span id="tag-label-${element.id}">${element.name}</span>
                <div class="app-navigation-entry-utils">
                    <ul>
                        <li class="app-navigation-entry-utils-counter">${element.count>0?element.count:''}</li>
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
                        </a>
                    </li>
                    <li>
                        <a href="#" id="remove-tag-${element.id}">
                            <span class="icon-delete"></span>
                            <span>Remove</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="edit-tag-${element.id}">
                            <span class="icon-rename"></span>
                            <span>Edit</span>
                        </a>
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

        $(`#edit-tag-${element.id}`).click(function(event) {

            self.generateTagForm(`#tag-menu-${element.id}`, element)
        });
    }

    hideEntryMenu() {
        if ($('.app-navigation-entry-menu').is(":visible")) {
            $('.app-navigation-entry-menu').hide();
        }
        if ($('#tag-form').length) {
            $('#tag-form').remove()
        }

    }

    updateTag(tag) {
        $(`#tag-label-${tag.id}`).html(tag.name)
        $(`#tag-bullet-${tag.id}`).css('background', tag.color)
        let self = this;
        $(`#edit-tag-${tag.id}`).unbind().click(function(event) {
            self.generateTagForm(`#tag-menu-${tag.id}`, tag)
        });
    }

    removeTag(id) {
        $(`#tag-${id}`).remove().unbind();
    }

    /********************************************* */

    initCreateTag() {

        $('#create-tag').click(function(event) {
            event.stopPropagation();
        });

        let self = this;
        $('#create-tag-bp').click(function(event) {
            event.stopPropagation();
            self.hideEntryMenu();
            $('#create-tag').show();
            self.generateTagForm('#create-tag')
        })
    }

    generateTagForm(target, tag) {

        $(target).append(`<form id="tag-form">
            <input id="input-create-tag" type="text" placeholder="New tag" value="${tag?tag.name:''}" autocomplete="off">
            <input type="submit" value=" " class="icon-confirm">
            <div class="colorpicker">
                <ul class="colorpicker-list">
                    <li style="background-color: rgb(49, 204, 124);"></li><li style="background-color: rgb(49, 124, 204);"></li><li style="background-color: rgb(255, 122, 102);"></li><li style="background-color: rgb(241, 219, 80);"></li><li style="background-color: rgb(124, 49, 204);"></li><li style="background-color: rgb(204, 49, 124);"></li><li style="background-color: rgb(58, 59, 61);"></li><li style="background-color: rgb(202, 203, 205);"></li><!--
                    --><label class="color-selector-label selected" style="background-color:${tag?tag.color:''}"><input type="color" class="color-selector"></label>
                </ul>
            </div>
        </form>`)

        $('.colorpicker li, .colorpicker label').click(function() {
            $('.colorpicker li, .colorpicker label').removeClass('selected')
            $(this).addClass('selected')
        })

        $('.colorpicker input').change(function() {
            $(this).parent('label').css('background-color', $(this).val())
        })

        let self = this;
        $("#tag-form").submit(function(event) {
            event.preventDefault()
            let name = self.capitalize($('#input-create-tag').val());
            let color = $('.colorpicker .selected').css('background-color')
            if (tag) {
                self.update(tag.id, name, color);
            } else {
                self.create(name, color);
            }
        });
    }

    capitalize(input) {
        return input.charAt(0).toUpperCase() + input.slice(1) //.toLowerCase()
    }

    /******************************************** */

    create(name, color) {

        if (!name.length) { return; }
        var self = this;
        $.ajax({
            url: this.baseUrl + '/tag',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ name, color })
        }).done(function(response) {
            self.hideEntryMenu();
            self.displayTag(response)
            self.updateTagAssignAutocomplete()
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This tag already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }

    update(id, name, color) {
        var self = this;
        if (!name.length) { return; }
        $.ajax({
            url: this.baseUrl + '/tag/' + id,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ name, color })
        }).done(function(tag) {
            self.hideEntryMenu();
            self.updateTag(tag)
            self.updateTagAssignAutocomplete()
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
                self.updateTagAssignAutocomplete()
            }).fail(function(response, code) {
                toast("An error occurred.", 4);
            });
        })
    }


    updateTagAssignAutocomplete() {
        TagAssign.initAutocomplete()
    }
}

var TagForm = new TagFormClass();