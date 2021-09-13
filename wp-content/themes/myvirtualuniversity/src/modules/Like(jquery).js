import $ from 'jquery'

class Like {
    constructor () {
        this.events()
    }

    events () {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this))
    }

    //methods
    ourClickDispatcher (e) {
        const currentLikeBox = $(e.target).closest(".like-box") // building this so we don't have to hard-select the like-box. Whatever element within "like-box span" gets selected, this variable points to the closest ancestor named "like-box"
        if (currentLikeBox.attr('data-exists') == 'yes') {
            this.deleteLike(currentLikeBox)
        } else {
            this.createLike(currentLikeBox)
        }
    }

    createLike (currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)   // this is for authorization
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'POST',
            data: {
                'professorId': currentLikeBox.data('professor') // data-professor is the attr of html in single-professor.php that contains that ID of the current professor page in view
            },
            success: (response) => {    // response here is the like-post ID received by the "return wp_insert_post()" in func createLike() in 'like-route.php'
                currentLikeBox.attr('data-exists', 'yes')
                let likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10)   // 2nd arg of '10' makes it decimal system. If it is set to '2', it would be binary system.
                likeCount++ // the likeCount variable in js is necessary to update the frontend on the fly without having to refresh the page to get the updated data from the backend
                currentLikeBox.find(".like-count").html(likeCount)
                currentLikeBox.attr("data-like", response)  // 1st arg is the attr I want to update, and 2nd arg is what I want to set its value to
                console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
        })
    }

    deleteLike (currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce)   // this is for authorization
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            data: {
                'like': currentLikeBox.attr('data-like')
            },
            type: 'DELETE',
            success: (response) => {
                currentLikeBox.attr('data-exists', 'no')
                let likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10)
                likeCount--
                currentLikeBox.find(".like-count").html(likeCount)
                currentLikeBox.attr("data-like", '')
                console.log(response)
            },
            error: (response) => {
                console.log(response)
            }
        })
    }
}

export default Like