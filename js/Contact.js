class ContactClass {
    constructor() {
        this.createMode = true;
        this.contactKey = [
            "id",
            "name",
            "lastName",
            "description",
            "work",
            "hobbies",
            "birth",
            "birthNotif",
        ];
        this.contactKeyRequirement = {
            name: "Name",
        };
        this.colorsArray = [
            "#2196f3",
            "#f44336",
            "#4caf50",
            "#03a9f4",
            "#9e9e9e",
            "#9c27b0",
            "#ff9800",
            "#ffc107",
            "#795548",
            "#607d8b",
            "#ff5722",
            "#009688",
            "#e91e63",
            "#00838f",
            "#b71c1c",
            "#ea80fc",
            "#00796b",
            "#388e3c",
            "#ad1457",
            "#1976d2",
            "#00bcd4",
            "#1b5e20",
            "#ffca28",
            "#bdbdbd",
            "#e64a19",
            "#78909c",
        ];
        this.currentId = null;
        this.formCreateMode = true;
    }

    init() {
        this.showAll();
        this.initPreviewMenu();
        this.initForm();
        this.initSearch();
    }

    getList() {
        let contactList = [];
        var self = this;
        $("#contact-list .app-content-list-item-line-one").each(function() {
            if (this.id != self.currentId)
                contactList.push($(this).html())
        });
        return contactList;
    }

    getContactIdByName(name) {
        if (name == "") {
            return null
        }
        let id = null;
        $("#contact-list .app-content-list-item-line-one").each(function() {
            if ($(this).html() == name)
                id = parseInt(this.id);
        });
        return id;
    }

    initPreviewMenu() {
        // display/hide preview menu
        $("#preview-setting").click((event) => {
            event.stopPropagation();
            $("#preview-menu").show();
        });
        $(window).click(() => {
            if ($("#preview-menu").css("display") != "none") {
                $("#preview-menu").hide();
            }
        });
        $("#preview-menu-edit").click(() => {
            this.show(this.currentId, (rep) => {
                this.addContactToForm(rep.contact);
            });
        });
        $("#preview-menu-remove").click(() => {
            this.remove(this.currentId);
        });
        $("#preview-menu-link").click(() => {
            this.displayLink()
        });
    }

    initForm() {
        $("#create-contact").click(() => {
            this.prepareCreateContactForm();
        });
        $("[id=contact-form-close]").click(() => {
            this.displayContactPreview(this.currentId);
        });
        $("#contact-form").submit((event) => {
            event.preventDefault();
            if (this.formCreateMode) this.create(this.getFormToContact());
            else this.update(this.getFormToContact());
        });
    }

    initSearch() {
        $("#contact-seach input").on("input", function() {
            let value = $(this).val().toLowerCase();
            if (value) {
                $("#contact-list a").each(function() {
                    if (
                        $(this)
                        .find(".app-content-list-item-line-one")
                        .html()
                        .toLowerCase()
                        .search(value) == -1
                    ) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            } else {
                $("#contact-list a").show();
            }
        });
    }

    searchByType(tagId) {
        if (tagId) {
            $("#contact-list a").each(function() {
                if ($(this).find(`#mini-chip-${tagId}`).length) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $("#contact-list a").show();
        }
    }

    /******************** currentId */

    getCurrentId() {
        return this.currentId;
    }

    /******************* form */

    addContactToPreview(json) {
        $("#contact-preview").show();
        $("#contact-form, #contact-link").hide();
        this.currentId = json.id;
        this.contactKey.forEach((element) => {
            if (json.hasOwnProperty(element)) {
                if (element == "description")
                    $(`#contact-preview #${element}`).html(parseMarkdown(json[element]));
                else $(`#contact-preview #${element}`).html(json[element] || "&nbsp;");
            }
        });
        $(".header .title").html(`${json.name} ${json.lastName}`)
        $(`#contact-preview #birthNotif-preview`).prop(
            "checked",
            json["birthNotif"] == 1 ? true : false
        );
    }

    addContactToForm(contact) {
        this.formCreateMode = false;
        $("#contact-form").show();
        $("#contact-preview, #contact-link").hide();
        $("#contact-form #submit").val(t(AppName, "Update contact"));
        this.contactKey.forEach((element) => {
            if (contact.hasOwnProperty(element)) {
                $(`#contact-form #${element}`).val(contact[element]);
            }
        });
        $(`#contact-form #birthNotif`).prop(
            "checked",
            contact["birthNotif"] == 1 ? true : false
        );
    }

    prepareCreateContactForm() {
        this.formCreateMode = true;
        $("#contact-form").show();
        $("#contact-preview, #contact-link").hide();
        $("#contact-form #submit").val(t(AppName, "Add new contact"));
        this.contactKey.forEach((element) => {
            $(`#contact-form #${element}`).val("");
        });
        $(`#contact-form #birthNotif`).prop("checked", true);
    }

    getFormToContact() {
        let json = {};
        this.contactKey.forEach((element) => {
            if (element != "date") {
                json[element] = this.capitalize($(`#contact-insert-form #${element}`).val());
            } else {
                json[element] = $(`#contact-insert-form #${element}`).val();
            }
        });
        json["birthNotif"] = $("#contact-insert-form #birthNotif").prop("checked") ? 1 : 0;
        return json;
    }

    capitalize(input) {
        return input.charAt(0).toUpperCase() + input.slice(1);
    }

    displayLink() {
        $("#contact-link #link-list").empty()
        $("#contact-link").show();
        $("#contact-preview, #contact-form").hide();
        Link.displayLinkSection(this.currentId)
    }

    /************************** display contact **** */

    generateContactList(contact) {
        let letter = contact.name.charAt(0);
        let n = parseInt((letter.toLowerCase().charCodeAt(0) - 97));
        let color = this.colorsArray[n] || this.colorsArray[0];
        return `<a href="#" class="app-content-list-item" id="contact-${contact.id}">
                    <div class="app-content-list-item-icon" style="background-color: ${color}">${letter}</div>
                    <div class="app-content-list-item-line-one" id="${contact.id}">${contact.name} ${contact.lastName}</div>
                    <div class="app-content-list-item-line-two mini-tag-block"></div>
                </a>`;
    }

    addContactListEvent(id) {
        $(`#contact-${id}`).click(() => {
            this.displayContactPreview(id);
        });
    }

    displayAllContactList(contacts) {
        $("#contact-list").empty()
        contacts.forEach((contact) => {
            this.displayContactList(contact);
        });
        this.displayContactCount(contacts.length)
    }

    displayContactCount(n) {
        let e = $("#all-tag .counter")
        let count = (e.html() == '' ? 0 : parseInt(e.html())) + n
        e.html(count < 0 ? 0 : count)
    }

    displayContactList(contact) {
        $("#contact-list").prepend(this.generateContactList(contact));
        this.addContactListEvent(contact.id);
    }

    updateContactList(contact) {
        let tags = $(`#contact-${contact.id} .mini-tag-block`).html();
        $(`#contact-${contact.id}`).replaceWith(this.generateContactList(contact));
        $(`#contact-${contact.id} .mini-tag-block`).html(tags).show();
        this.addContactListEvent(contact.id);
    }

    removeContactList(id) {
        $(`#contact-${id}`).remove().unbind();
    }

    displayContactPreview(id) {
        this.show(id, (rep) => {
            this.addContactToPreview(rep.contact);
            this.showTagAssignPreview(rep.assign);
            this.showLinkPreview(rep.link);
        });
    }

    displayFirstContact() {
        if ($("#contact-list .app-content-list-item").length) {
            $("#contact-list .app-content-list-item").first().click();
        } else {
            this.prepareCreateContactForm();
        }
    }

    /************************** show and CRUD **** */

    show(id, callback) {
        if (id == null) {
            return;
        }
        ajaxRequest("/contact/" + id, "GET", null, (contact) => {
            callback(contact);
        });
    }

    showAll() {
        ajaxRequest("/contact", "GET", null, (contacts) => {
            this.displayAllContactList(contacts.contact);
            this.sowAllContactListTag(contacts.assign);
            this.displayFirstContact();
        });
    }

    create(contact) {
        if (this.validContact(contact)) {
            return;
        }
        ajaxRequest(
            "/contact",
            "POST",
            JSON.stringify(contact),
            (contact) => {
                this.displayContactList(contact);
                this.displayFirstContact();
                this.displayContactCount(1)
            },
            (status) => {
                if (status == 400) {
                    toast(t(AppName, "This contact already exist."), 3);
                } else {
                    toast(t(AppName, "An error occurred."), 3);
                }
            }
        );
    }

    validContact(contact) {
        for (let k in this.contactKeyRequirement) {
            if (!contact[k].length) {
                let name = t(AppName, this.contactKeyRequirement[k]);
                toast(t(AppName, "You need to complete {name}.", { name }), 3);
                return 1;
            }
        }
    }

    update(contact) {
        ajaxRequest(
            "/contact/" + contact.id,
            "PUT",
            JSON.stringify(contact),
            (contact) => {
                this.displayContactPreview(contact.id);
                this.updateContactList(contact);
            },
            (status) => {
                if (status == 400) {
                    toast(t(AppName, "This contact already exist."), 3);
                } else {
                    toast(t(AppName, "An error occurred."), 3);
                }
            }
        );
    }

    remove(id) {
        confirmToast(t(AppName, "Are you sure?"), () => {
            ajaxRequest("/contact/" + id, "DELETE", null, () => {
                this.removeContactList(id);
                this.displayFirstContact();
                this.updateTagCount(-1)
                this.displayContactCount(-1)
            });
        });
    }

    /********************************* */

    showTagAssignPreview(tags) {
        TagAssign.displayAllPreviewTag(tags);
    }
    sowAllContactListTag(tags) {
        TagAssign.displayAllContactListTag(tags);
    }
    updateTagCount(n) {
        $("#tag-assigned-list .chip").each(function() {
            Tag.updateTagCount(parseInt($(this).attr("tagid")), n)
        });
    }
    showLinkPreview(links) {
        Link.showLinkPreview(links)
    }
}

var Contact = new ContactClass();