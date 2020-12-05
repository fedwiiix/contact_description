class LinkClass {
    constructor() {
        this.types = ["Wedded", "Betrothed", "Child/Parent", "Brother/Sister", "Another"]
        this.typesMsg = ["Wedded with", "Betrothed with", "Child/Parent of", "Brother/Sister of", "In link with"]
    }

    init() {
        this.initAutocomplete();
    }

    initAutocomplete() {
        for (let k in this.types) {
            $("#link-select").append(`<option value="${k}">${t(AppName, this.types[k])}</option>`)
        }
        autocomplete(document.getElementById("link-assign"), Contact);

        $("#link-select").change(() => {
            this.updateLinkSelect()
        });

        $("#contact-link-add").click(() => {
            this.resetForm()
        });

        $("#link-birth").change(() => {
            $(`#link-birthNotif`).prop("checked", true);
        });

        $("#contact-link-form").submit((event) => {
            event.preventDefault();
            let inputName = $("#link-assign").val();
            let type = parseInt($("#link-select").val());
            let birth = "",
                birthNotif = 0;
            if (type == 0) {
                birth = $("#link-birth").val();
                birthNotif = $("#link-birthNotif").prop("checked") && birth != "" ? 1 : 0;
            }
            let json = {
                contactId: Contact.getCurrentId(),
                contactIdBis: Contact.getContactIdByName(inputName),
                type,
                birth,
                birthNotif
            }
            if (this.updateId < 0) {
                this.create(json);
            } else {
                this.update(json);
            }
        });
    }

    displayLinkSection(id) {
        this.showAll(id)
        this.resetForm()
    }

    resetForm() {
        $("#link-assign, #link-birth").val('');
        $("#link-select").val(0);
        $("#link-birthNotif").prop("checked", false)
        $("#contact-link #submit").val(t(AppName, "Add link"))
        this.updateLinkSelect()
        this.updateId = -1;
    }

    showLinkForm(link) {
        $("#link-assign").val(`${link.name} ${link.lastName}`);
        $("#link-select").val(link.type);
        $("#link-birth").val(link.birth);
        $(`#link-birthNotif`).prop("checked", link.birthNotif == 1 ? true : false);
        $("#contact-link #submit").val(t(AppName, "Update link"))
        this.updateLinkSelect()
        this.updateId = link.id;
    }

    updateLinkSelect() {
        if ($("#link-select").val() > 0) {
            $("#contact-link-form .birth").hide()
        } else {
            $("#contact-link-form .birth").show()
        }
    }

    /*********************************************** preview **** */

    showLinkPreview(links) {
        $("#contact-preview #link-list").empty()
        links.forEach((link) => {
            $("#contact-preview #link-list").prepend(this.generateLinkList(link, false));
            this.addPreviwLinkEvents(link)
        })
    }

    addPreviwLinkEvents(link) {
        $(`#preview-link-${link.id}`).click(() => {
            Contact.displayContactPreview(link.contactId);
        });
    }

    /*************************************************** */

    displayAllLinks(links) {
        $("#contact-link #link-list").empty()
        links.forEach((link) => {
            this.displayLink(link)
        })
    }

    displayLink(link) {
        $("#contact-link #link-list").prepend(this.generateLinkList(link, true));
        this.addLinkEvents(link)
    }

    updateLink(link) {
        $(`#link-${link.id}`).replaceWith(this.generateLinkList(link, true));
        this.addLinkEvents(link)
    }

    removeLink(id) {
        $(`#link-${id}`).remove();
    }

    generateLinkList(link, editMode) {
        let letter = link.name.charAt(0);
        let n = parseInt((letter.toLowerCase().charCodeAt(0) - 97));
        let color = Contact.colorsArray[n] || Contact.colorsArray[0];
        let describe = (link.type > 0 || link.birth == "" ? '' : link.birth + ' - ' + t(AppName, (link.birthNotif == 0 ? "Recall disable" : "Recall enable")))
        describe = describe ? `<div class="app-content-list-item-line-two">${describe}</div>` : ''
        let editContent = (editMode ? `<span class="icon-rename"></span>
                                        <span class="icon-delete"></span>` : '')
        let id = (editMode ? `link-${link.id}` : `preview-link-${link.id}`)

        return `<a href="#" class="app-content-list-item" id="${id}">
                <div class="app-content-list-item-icon" style="background-color: ${color}">${letter}</div>
                <div class="app-content-list-item-line-one" id="${link.id}">${t(AppName, this.typesMsg[link.type])} ${link.name} ${link.lastName}</div>
                ${describe}${editContent}
            </a>`;
    }

    addLinkEvents(link) {
        $(`#link-${link.id} .icon-delete`).click(() => {
            this.remove(link.id);
        });
        $(`#link-${link.id} .icon-rename`).click(() => {
            this.showLinkForm(link)
        });
    }

    /*************************************************** */

    showAll(id) {
        ajaxRequest("/link/" + id, "GET", null, (links) => {
            this.displayAllLinks(links);
        });
    }

    create(json) {
        if (json.contactIdBis === null || json.contactId == json.contactIdBis) {
            toast(t(AppName, "You must select valid contact."), 4);
            return;
        }
        ajaxRequest(
            "/link",
            "POST",
            JSON.stringify(json),
            (link) => {
                if ($(`#link-${link.id}`).length) {
                    this.updateLink(link)
                } else {
                    this.displayLink(link)
                }
                this.resetForm()
            },
            (status) => {
                if (status == 400) {
                    toast(t(AppName, "This link already exist."), 4);
                } else {
                    toast(t(AppName, "An error occurred."), 4);
                }
            }
        );
    }

    update(json) {
        if (!json.contactIdBis === null || json.contactId == json.contactIdBis) {
            toast(t(AppName, "You must select valid contact."), 4);
        }
        ajaxRequest(
            "/link/" + this.updateId,
            "PUT",
            JSON.stringify(json),
            (link) => {
                this.updateLink(link)
                this.resetForm();
            },
            (status) => {}
        );
    }

    remove(id) {
        confirmToast(t(AppName, "Are you sure?"), () => {
            ajaxRequest("/link/" + id, "DELETE", null, () => {
                this.removeLink(id)
            });
        });
    }
}

var Link = new LinkClass();