class TagClass {
    constructor() {
        this.tagList = [];
    }

    init() {
        this.showAll();
        this.initCreateTag();

        $(window).click(() => {
            this.hideEntryMenu();
        });

        $("#all-tag").click(function() {
            User.searchByType();
        });
    }

    getTagList() {
        return this.tagList;
    }

    /************************************************ */

    displayAllTag(tags) {
        $("#tag-list, #tag-favorite").empty()
        tags.forEach((tag) => {
            this.displayTag(tag);
        });
        this.tagList = tags;
    }

    displayTag(tag) {
        this.addTagToList(tag);

        var html = `<li id="tag-${tag.id}">
            <div class="app-navigation-entry-bullet" id="tag-bullet-${
              tag.id
            }" style="background:${tag.color}"></div>
            <a href="#"><span id="tag-label-${tag.id}">${tag.name}</span>
                <div class="app-navigation-entry-utils">
                    <ul>
                        <li class="app-navigation-entry-utils-counter">${
                          tag.count > 0 ? tag.count : ""
                        }</li>
                        <li class="app-navigation-entry-utils-menu-button" id="tag-open-menu-${
                          tag.id
                        }">
                            <button></button>
                        </li>
                    </ul>
                </div>
            </a>
            <div class="app-navigation-entry-menu" id="tag-menu-${tag.id}">
                <ul>
                    <li>
                        <a href="#" id="favorite-tag-${tag.id}">
                            <span class="icon-add"></span>
                            <span>${
                              tag.favorite == 1 ? "Remove" : "Add"
                            } to favorite</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="remove-tag-${tag.id}">
                            <span class="icon-delete"></span>
                            <span>Remove</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" id="edit-tag-${tag.id}">
                            <span class="icon-rename"></span>
                            <span>Edit</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>`;

        $(`#tag-${tag.favorite == 1 ? "favorite" : "list"}`).prepend(html);

        $(`#tag-${tag.id}`).click(() => {
            User.searchByType(tag.id);
        });

        $(`#tag-open-menu-${tag.id}`).click((event) => {
            event.stopPropagation();
            this.hideEntryMenu();
            $(`#tag-menu-${tag.id}`).show();
        });

        $(`#tag-menu-${tag.id}`).click((event) => {
            event.stopPropagation();
        });

        $(`#favorite-tag-${tag.id}`).click(() => {
            this.addToFavorite(tag.id, tag.favorite == 1 ? 0 : 1);
        });

        $(`#remove-tag-${tag.id}`).click(() => {
            this.remove(tag.id);
        });

        $(`#edit-tag-${tag.id}`).click((event) => {
            this.generateTagForm(`#tag-menu-${tag.id}`, tag);
        });
    }

    hideEntryMenu() {
        if ($(".app-navigation-entry-menu").is(":visible")) {
            $(".app-navigation-entry-menu").hide();
        }
        if ($("#tag-form").length) {
            $("#tag-form").remove();
        }
    }

    updateTag(tag) {
        $(`#tag-label-${tag.id}`).html(tag.name);
        $(`#tag-bullet-${tag.id}`).css("background", tag.color);

        $(`#edit-tag-${tag.id}`)
            .unbind()
            .click((event) => {
                this.generateTagForm(`#tag-menu-${tag.id}`, tag);
            });
        this.removeTagFromList(tag.id);
        this.addTagToList(tag);
    }

    removeTag(id) {
        $(`#tag-${id}`).remove().unbind();
        this.removeTagFromList(id);
    }

    updateTagCount(tagId, n) {
        let el = $(`#tag-${tagId} .app-navigation-entry-utils-counter`);
        let val = el.html() == "" ? 0 : parseInt(el.html());
        el.html(val <= 1 && n == -1 ? "" : val + n);
    }

    /*************************************** */

    addTagToList(tag) {
        this.tagList.push(tag);
    }

    removeTagFromList(id) {
        let n = 0;
        this.tagList.forEach((tag) => {
            if (tag.id == id) {
                return;
            }
            n++;
        });
        this.tagList.splice(n, 1);
    }

    /********************************************* */

    initCreateTag() {
        $("#create-tag").click(function(event) {
            event.stopPropagation();
        });

        $("#create-tag-bp").click((event) => {
            event.stopPropagation();
            this.hideEntryMenu();
            $("#create-tag").show();
            this.generateTagForm("#create-tag");
        });
    }

    generateTagForm(target, tag) {
        let colorPicker = "";
        let colors = [
            "#31cc7c",
            "#317ccc",
            "#ff7a66",
            "#ffdc00",
            "#7c31cc",
            "#cc317c",
            "#3a3b3d",
            "#cacbcd",
        ];
        colors.forEach((color) => {
            colorPicker += `<li style="background-color:${color}"></li>`;
        });

        $(target).append(`<form id="tag-form">
            <input id="input-create-tag" type="text" placeholder="New tag" value="${
              tag ? tag.name : ""
            }" autocomplete="off">
            <input type="submit" value=" " class="icon-confirm">
            <div class="colorpicker">
                <ul class="colorpicker-list">
                  ${colorPicker}<!--
                  --><label class="color-selector-label selected" style="background-color:${
                    tag ? tag.color : "#0082c9"
                  }"><input type="color" class="color-selector"></label>
                </ul>
            </div>
        </form>`);

        $(".colorpicker li, .colorpicker label").click(function() {
            $(".colorpicker li, .colorpicker label").removeClass("selected");
            $(this).addClass("selected");
        });

        $(".colorpicker input").change(function() {
            $(this).parent("label").css("background-color", $(this).val());
        });

        $("#tag-form").submit((event) => {
            event.preventDefault();
            let name = this.capitalize($("#input-create-tag").val());
            let color = this.rgbToHex(
                $(".colorpicker .selected").css("background-color")
            );
            if (tag) {
                this.update(tag.id, name, color);
            } else {
                this.create(name, color);
            }
        });
    }

    capitalize(input) {
        return input.charAt(0).toUpperCase() + input.slice(1);
    }

    rgbToHex(rgb) {
        let rgbArr = rgb.match(/[0-9]{1,3}/g);
        return (
            "#" +
            (
                (1 << 24) +
                (parseInt(rgbArr[0]) << 16) +
                (parseInt(rgbArr[1]) << 8) +
                parseInt(rgbArr[2])
            )
            .toString(16)
            .slice(1)
        );
    }

    /******************************************** */

    showAll() {
        ajaxRequest("/tag", "GET", null, (tags) => {
            this.displayAllTag(tags);
        });
    }

    create(name, color) {
        if (!name.length) {
            return;
        }
        ajaxRequest(
            "/tag",
            "POST",
            JSON.stringify({ name, color }),
            (tag) => {
                this.hideEntryMenu();
                this.displayTag(tag);
            },
            (status) => {
                if (status == 400) {
                    toast("This tag already exist.", 4);
                } else {
                    toast("An error occurred.", 4);
                }
            }
        );
    }

    update(id, name, color) {
        if (!name.length) {
            return;
        }
        ajaxRequest(
            "/tag/" + id,
            "PUT",
            JSON.stringify({ name, color }),
            (tag) => {
                this.hideEntryMenu();
                this.updateTag(tag);
                this.updateTagAssign(tag);
            },
            (status) => {
                if (status == 400) {
                    toast("This tag already exist.", 4);
                } else {
                    toast("An error occurred.", 4);
                }
            }
        );
    }

    addToFavorite(id, favorite) {
        ajaxRequest(
            "/tagfavorite/" + id,
            "PUT",
            JSON.stringify({ favorite }),
            (tag) => {
                this.hideEntryMenu();
                this.removeTag(id);
                this.displayTag(tag);
            }
        );
    }

    remove(id) {
        confirmToast("Are you sure?", () => {
            ajaxRequest("/tag/" + id, "DELETE", null, () => {
                this.hideEntryMenu();
                this.removeTag(id);
                this.removeAllTagAssign(id);
            });
        });
    }

    updateTagAssign(tag) {
        tag.tagId = tag.id;
        TagAssign.updatePreviewTag(tag);
        TagAssign.updateAllContactListTag(tag);
    }
    removeAllTagAssign(tagId) {
        TagAssign.removePreviewTag(tagId);
        TagAssign.removeAllContactListTag(tagId);
    }
}

var Tag = new TagClass();