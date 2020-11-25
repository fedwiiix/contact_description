class UserFormClass {

    constructor() {
        this.baseUrl = OC.generateUrl('/apps/contact_description');
        this.createMode = true;
        this.contactKey = ["id", "name", "lastName", "description", "work", "hobbies", "birth", "birthNotif"];
        this.contactKeyRequirement = { "name": "name", "description": "contact description" }
        this.colorsArray = ["#2196f3", "#f44336", "#4caf50", "#03a9f4", "#9e9e9e", "#9c27b0", "#ff9800", "#ffc107", "#795548", "#607d8b", "#ff5722", "#009688", "#e91e63", "#00838f", "#b71c1c", "#ea80fc", "#00796b", "#388e3c", "#ad1457", "#1976d2", "#00bcd4", "#1b5e20", "#ffca28", "#bdbdbd", "#e64a19", "#78909c"]
        this.currentId = null;
        this.formCreateMode = true;
    }

    init() {

        this.showAll()
        this.initPreviewMenu()
        this.initForm()
        this.initSearch()
    }

    initPreviewMenu() {
        $('#preview-setting').click(function(event) {
            event.stopPropagation();
            $('#preview-menu').show()
        })
        $(window).click(function() {
            if ($('#preview-menu').is(":visible")) {
                $('#preview-menu').hide();
            }
        });
        var self = this;
        $('#preview-menu-edit').click(function() {
            self.show(self.currentId, function(contact) {
                self.addContactToForm(contact)
            })
        })
        $('#preview-menu-remove').click(function() {
            self.remove(self.currentId)
        })
    }

    initForm() {
        var self = this;
        $('#create-contact').click(function() {
            self.prepareCreateContactForm()
        })
        $('#contact-form-close').click(function() {
            self.show(self.currentId, function(contact) {
                self.addContactToPreview(contact)
            })
        })
        $("#contact-form").submit(function(event) {
            event.preventDefault()
            if (self.formCreateMode)
                self.create(self.getFormToContact())
            else
                self.update(self.getFormToContact())
        });
    }

    initSearch() {
        $('#contact-seach input').on('input', function() {

            let value = $(this).val().toLowerCase();
            if (value) {
                $("#contact-list a").each(function() {
                    if ($(this).find('.app-content-list-item-line-one').html().toLowerCase().indexOf(value) == -1) {
                        $(this).hide()
                    } else {
                        $(this).show()
                    }
                });
            } else {
                $("#contact-list a").show()
            }
        });

    }

    /******************** currentId */

    getCurrentId() {
        return this.currentId;
    }

    /******************* form */

    addContactToPreview(json) {

        $('#contact-preview').show()
        $('#contact-form').hide()
        this.currentId = json.id
        this.contactKey.forEach(element => {
            if (json.hasOwnProperty(element)) {
                if (element == "description")
                    $(`#contact-preview #${element}`).html(parseMarkdown(json[element]))
                else
                    $(`#contact-preview #${element}`).html(json[element] || '&nbsp;')

            }
        });
        $(`#contact-preview #birthNotif-preview`).prop("checked", json["birthNotif"] == 1 ? true : false);
    }

    addContactToForm(json) {

        this.formCreateMode = false;
        $('#contact-form').show()
        $('#contact-preview').hide()
        $("#contact-form #submit").val("update contact")
        this.contactKey.forEach(element => {
            if (json.hasOwnProperty(element)) {
                $(`#contact-form #${element}`).val(json[element])
            }
        });
        $(`#contact-form #birthNotif`).prop("checked", json["birthNotif"] == 1 ? true : false);
    }

    prepareCreateContactForm() {

        this.formCreateMode = true;
        $('#contact-form').show()
        $('#contact-preview').hide()
        $("#contact-form #submit").val("add new contact")
        this.contactKey.forEach(element => {
            $(`#contact-form #${element}`).val('')
        });
        $(`#contact-form #birthNotif`).prop("checked", false);
    }

    getFormToContact() {
        let json = {};
        this.contactKey.forEach(element => {
            json[element] = $(`#contact-insert-form #${element}`).val();
        });
        json['birthNotif'] = $('#contact-insert-form #birthNotif').prop("checked") ? 1 : 0;
        return json
    }

    /************************** display contact **** */

    showAll() {
        var self = this;
        $.ajax({
            url: this.baseUrl + '/contact',
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            if (response.length) {
                response.forEach(element => {
                    self.displayContactList(element)
                });
                self.sowAllTag()
                self.displayFirstContact()
            }
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }

    generateContactList(contact) {
        let letter = contact.name.charAt(0)
        let n = parseInt((letter.toLowerCase().charCodeAt(0) - 97 + (contact.lastName.charAt(0).toLowerCase().charCodeAt(0) - 97 || 12)) / 2);
        let color = this.colorsArray[n]
        return `<a href="#" class="app-content-list-item" id="contact-${contact.id}">
                    <div class="app-content-list-item-icon" style="background-color: ${color}">${letter}</div>
                    <div class="app-content-list-item-line-one">${contact.name} ${contact.lastName}</div>
                    <div class="app-content-list-item-line-two mini-tag-block"></div>
                </a>`
    }

    addContactListEvent(id) {
        var self = this;
        $(`#contact-${id}`).click(function() {
            self.displayContactPreview(id)
        })
    }

    displayContactList(contact) {
        $('#contact-list').prepend(this.generateContactList(contact))
        this.addContactListEvent(contact.id)
    }

    updateContactList(contact) {
        $(`#contact-${contact.id}`).replaceWith(this.generateContactList(contact))
        this.addContactListEvent(contact.id)
    }

    displayContactPreview(id) {
        var self = this;
        this.show(id, function(contact) {
            self.addContactToPreview(contact)
        })
        this.showTags(id)
    }

    displayFirstContact() {
        if ($("#contact-list .app-content-list-item").length)
            $("#contact-list .app-content-list-item").first().click()
        else
            prepareCreateContactForm()
    }

    show(id, callback) {
        if (id == null) { return; }
        $.ajax({
            url: this.baseUrl + '/contact/' + id,
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            callback(response)
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }

    /************************** CRUD **** */

    create(contact) {
        if (this.validContact(contact)) { return; }
        var self = this;
        $.ajax({
            url: this.baseUrl + '/contact',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(contact)
        }).done(function(response) {
            self.displayContactList(response)
            self.displayFirstContact()
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This contact already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }

    validContact(contact) {
        for (let k in this.contactKeyRequirement) {
            if (!contact[k].length) {
                toast(`You need to complete ${this.contactKeyRequirement[k]}.`, 3);
                return 1;
            }
        }
    }

    update(contact) {
        var self = this;
        $.ajax({
            url: this.baseUrl + '/contact/' + contact.id,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(contact)
        }).done(function(response) {
            self.displayContactPreview(response.id)
            self.updateContactList(contact)
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }

    remove(id) {
        var self = this;
        confirmToast("Are you sure?", function() {
            $.ajax({
                url: self.baseUrl + '/contact/' + id,
                type: 'DELETE',
                contentType: 'application/json',
            }).done(function(response) {
                self.displayFirstContact()
                self.removeContact(id)
            }).fail(function(response, code) {
                toast("An error occurred.", 4);
            });
        })
    }

    removeContact(id) {
        $(`#contact-${id}`).remove().unbind();
    }

    /********************************* */

    displayContactListTag(tagAssign) {
        $(`#contact-${tagAssign.contactId} .mini-tag-block`)
            .show()
            .append(`<div class="mini-chip" id="mini-chip-${tagAssign.color}" style="background:${tagAssign.color}">${tagAssign.name}</div>`)
    }

    /********************************* */

    showTags(id) {
        TagAssign.show(id)
    }

    sowAllTag() {
        var self = this;
        TagAssign.showAll(function(response) {
            response.forEach(element => {
                self.displayContactListTag(element)
            });
        })
    }

}

var UserForm = new UserFormClass();