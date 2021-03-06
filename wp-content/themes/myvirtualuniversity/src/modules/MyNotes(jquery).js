import $ from 'jquery'

class MyNotes {
    constructor () {
        this.events();
    }

    events () {
        $("#my-notes").on("click", ".delete-note", this.deleteNote.bind(this))  // the 2nd arg loops through the element's items and executes the 3rd arg if there is a match
        $("#my-notes").on("click", ".edit-note", this.editNote.bind(this))
        $("#my-notes").on("click", ".update-note", this.updateNote.bind(this))
        $(".submit-note").on("click", this.createNote.bind(this))
    }

    // Methods 
    editNote (e) {
        const thisNote = $(e.target).parents("li")  // e.target always points to the tag or element where the event took place.
        if (thisNote.data("state") == "editable") {
            this.makeNoteReadOnly(thisNote)
        } else {
            this.makeNoteEditable(thisNote)
        }
    }

    makeNoteEditable (thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"> Cancel</i>')
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field")
        thisNote.find(".update-note").addClass("update-note--visible")
        thisNote.data("state", "editable")  // in jquery method data(), 1st arg refers to or creates the attribute name "data-state", 2nd arg is the value
    }

    makeNoteReadOnly (thisNote) {
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"> Edit</i>')
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly")/*1st arg is the name, 2nd is the value*/.removeClass("note-active-field")
        thisNote.find(".update-note").removeClass("update-note--visible")
        thisNote.data("state", "cancel")
    }

    deleteNote (e) {
        const thisNote = $(e.target).parents("li")
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)   // this is for authorization
            },
            url: universityData.root_url /*this is a very useful API method written in functions.php => wp_localize_script()*/ + '/wp-json/wp/v2/note/' + thisNote.data('id'), // when you use jquery, you don't need to say 'data-id' even though that's the name of the <li> attr
            type: "DELETE",
            success: (response) => {
                thisNote.slideUp() // slideUp() is a jquery method
                console.log('success congrats')
                console.log(response)
                if (response.userNoteCount < 5) {
                    $(".note-limit-message").removeClass("active")
                }
            },
            error: (response) => {
                console.log('sorry error')
                console.log(response)
            }
        })
    }

    updateNote (e) {
        const thisNote = $(e.target).parents("li")

        const ourUpdatedPost = {
            'title': thisNote.find(".note-title-field").val(),
            'content': thisNote.find(".note-body-field").val()
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)   // this is for authorization
            },
            url: universityData.root_url /*this is a very useful API method written in functions.php => wp_localize_script()*/ + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type: "POST",
            data: ourUpdatedPost,
            success: (response) => {
                this.makeNoteReadOnly(thisNote)
                console.log('success congrats')
                console.log(response)
            },
            error: (response) => {
                console.log('sorry error')
                console.log(response)
            }
        })
    }

    createNote (e) {
        const ourNewPost = {
            'title': $(".new-note-title").val(),
            'content': $(".new-note-body").val(),
            'status': 'publish' // this is set by default to 'draft'. You can set it to 'private' as well, but the 1% malicious user can change this from the client-side, so we need a server-side enforcement.
        }

        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)   // this is for authorization
            },
            url: universityData.root_url /*this is a very useful API method written in functions.php => wp_localize_script()*/ + '/wp-json/wp/v2/note/',
            type: "POST",
            data: ourNewPost,
            success: (response) => {
                $(".new-note-title, .new-note-body").val('')
                $(`
                    <li data-id="${response.id}">
                        <input readonly class="note-title-field" value="${response.title.raw}">
                        <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"> Edit</i></span>
                        <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"> Delete</i></span>
                        <textarea readonly class="note-body-field" >${response.content.raw}</textarea>
                        <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"> Save</i></span>
                    </li>
                `).prependTo("#my-notes").hide().slideDown()
                console.log('success congrats')
                console.log(response)
            },
            error: (response) => {
                if (response.responseText == 'You have reached your note limit.') {
                    $(".note-limit-message").addClass("active")
                }
                console.log('sorry error')
                console.log(response)
            }
        })
    }
}

export default MyNotes;