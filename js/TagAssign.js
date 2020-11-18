class TagAssignClass {

    constructor() {
        this.baseUrl = OC.generateUrl('/apps/contact_description');
        this.colorsArray = ["#2196f3", "#f44336", "#4caf50", "#03a9f4", "#9e9e9e", "#9c27b0", "#ff9800", "#ffc107", "#795548", "#607d8b", "#ff5722", "#009688", "#e91e63", "#00838f", "#b71c1c", "#ea80fc", "#00796b", "#388e3c", "#ad1457", "#1976d2", "#00bcd4", "#1b5e20", "#ffca28", "#bdbdbd", "#e64a19", "#78909c"]
        this.tagList = [];
        this.assignedId = [];
        this.autocompleteList = []
    }

    init() {

        this.initAutocomplete()
        this.initForm()

    }

    initAutocomplete() {
        var self = this;
        $.ajax({
            url: this.baseUrl + '/tag',
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            self.tagList = response;
            self.autocompleteList = []
            response.forEach(tag => {
                self.autocompleteList.push(tag.tagName)
            });
            autocomplete(document.getElementById("assign-tag"), self.autocompleteList);
        }).fail(function(response, code) {
            toast("An error occurred.", 4);
        });
    }

    initForm() {

        var self = this;
        $("#tag-assign-form").submit(function(event) {
            event.preventDefault()
            let input = $("#tag-assign-form #assign-tag").val()

            self.ifAssigned(input, function(tag) {

                self.create(tag.id, function() {
                    self.assignId(true, tag.id)
                    self.generateChip(tag)
                })
            }, function() {
                $("#tag-assign-form #assign-tag").val('')
            })

        });
    }

    generateChip(tag) {
        let n = this.autocompleteList.indexOf(tag.tagName)
        let color = this.colorsArray[n]
        $('#tag-assigned-list').append(`<div class="chip" id="chip-${tag.id}" style="background:${color}">
                                            <span>${tag.tagName}</span>
                                            <button class="icon-close"></button>
                                        </div>`)

        var self = this;
        $(`#chip-${tag.id} button`).click(function() {
            self.remove(tag.id, function() {
                self.assignId(false, tag.id)
                $(`#chip-${tag.id}`).remove().unbind();
            })
        })
    }

    assignId(assign, id) {
        if (assign) {
            this.assignedId.push(id)
        } else {
            this.assignedId.splice(this.assignedId.indexOf(id), 1);
        }
        console.log(this.assignedId)
    }

    ifAssigned(input, callBackYes, callBackNo) {
        var self = this;
        this.tagList.forEach(tag => {
            if (tag.tagName == input && !self.assignedId.includes(tag.id)) {
                callBackYes(tag)
            }
            if (tag.tagName == input || self.assignedId.includes(tag.id)) {
                callBackNo()
            }
        });
    }

    /****************************************** */


    show(contactId) {

        var self = this;
        $.ajax({
            url: this.baseUrl + '/tagassign/' + contactId,
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            response.forEach(contactTag => {
                self.tagList.forEach(tag => {
                    if (contactTag.tagId == tag.id) {
                        self.assignId(true, tag.id)
                        self.generateChip(tag)
                    }
                });
            });
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This contact already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }


    create(tagId, callBack) {

        let currentContactId = UserForm.getCurrentId()

        var self = this;
        $.ajax({
            url: this.baseUrl + '/tagassign',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ contactId: currentContactId, tagId })
        }).done(function(response) {
            callBack()
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This contact already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }

    remove(tagId, callBack) {

        let currentContactId = UserForm.getCurrentId()
        var self = this;
        confirmToast("Are you sure?", function() {
            $.ajax({
                url: self.baseUrl + '/tagassign',
                type: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify({ contactId: currentContactId, tagId })
            }).done(function(response) {
                callBack()
            }).fail(function(response, code) {
                toast("An error occurred.", 4);
            });
        })
    }


}

var TagAssign = new TagAssignClass();