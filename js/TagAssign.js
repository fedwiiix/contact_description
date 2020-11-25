class TagAssignClass {

    constructor() {
        this.baseUrl = OC.generateUrl('/apps/contact_description');
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
                self.autocompleteList.push(tag.name)
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
            let inputTagName = $("#tag-assign-form #assign-tag").val()

            self.isAssigned(inputTagName, function(tagListElement) {
                self.create(tagListElement.id, function(tag) {
                    // give name
                    tag.name = tagListElement.name
                    tag.color = tagListElement.color
                    self.assignId(true, tag.tagId)
                    self.displayChip(tag)
                })
            }, function() {
                $("#tag-assign-form #assign-tag").val('')
            })

        });
    }

    isAssigned(input, callBackYes, callBackNo) {
        var self = this;
        this.tagList.forEach(tagListElement => {
            if (tagListElement.name == input && !self.assignedId.includes(tagListElement.id)) {
                callBackYes(tagListElement)
            }
            if (tagListElement.name == input || self.assignedId.includes(tagListElement.id)) {
                callBackNo()
            }
        });
    }

    assignId(assign, tagId) {
        if (assign) {
            this.assignedId.push(tagId)
        } else {
            this.assignedId.splice(this.assignedId.indexOf(tagId), 1);
        }
        console.log(this.assignedId)
    }

    cleanAssignedId() {
        this.assignedId = [];
    }

    /****************************************** */

    displayChip(tag) {
        $('#tag-assigned-list').append(this.generateChip(tag))

        var self = this;
        $(`#chip-${tag.id} button`).click(function() {
            self.remove(tag.id, function() {
                self.assignId(false, tag.id)
                $(`#chip-${tag.id}`).remove().unbind();
            })
        })
    }

    generateChip(tag) {
        let n = this.autocompleteList.indexOf(tag.name)
        return `<div class="chip" id="chip-${tag.id}" style="background:${tag.color}">
                    <span>${tag.name}</span>
                    <button class="icon-close"></button>
                </div>`
    }

    cleanChips() {
        this.cleanAssignedId()
        $('#tag-assigned-list').empty()
    }

    /****************************************** */


    show(contactId) {

        var self = this;
        $.ajax({
            url: this.baseUrl + '/tagassign/' + contactId,
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            self.cleanChips()
            response.forEach(tag => {
                //self.tagList.forEach(tag => {
                //if (contactTag.tagId == tag.id) {
                self.assignId(true, tag.tagId)
                self.displayChip(tag)
                    //}
                    //});
            });
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This contact already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }

    showAll(callback) {

        var self = this;
        $.ajax({
            url: this.baseUrl + '/tagassign',
            type: 'GET',
            contentType: 'application/json',
        }).done(function(response) {
            callback(response)
        }).fail(function(response, code) {});
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
            callBack(response)
        }).fail(function(response, code) {
            if (response.status == 400) {
                toast("This contact already exist.", 4);
            } else {
                toast("An error occurred.", 4);
            }
        });
    }

    remove(id, callBack) {

        let currentContactId = UserForm.getCurrentId()
        var self = this;
        confirmToast("Are you sure?", function() {
            $.ajax({
                url: self.baseUrl + '/tagassign/' + id,
                type: 'DELETE',
                contentType: 'application/json',
            }).done(function(response) {
                callBack()
            }).fail(function(response, code) {
                toast("An error occurred.", 4);
            });
        })
    }


}

var TagAssign = new TagAssignClass();