import axios from "axios"

class Search {
    // 1. describe and create/initiate our object
    constructor () {
        this.addSearchHTML()     // this appends the entire block of HTML for the search-overlay, instead of permanently putting it in place inside footer.php
        this.resultsDiv = document.querySelector("#search-overlay__results")
        this.openButton = document.querySelectorAll(".js-search-trigger")
        this.closeButton = document.querySelector(".search-overlay__close")
        this.searchOverlay = document.querySelector(".search-overlay")
        this.searchField = document.querySelector("#search-term")
        this.isOverlayOpen = false  // reading from DOM is much slower compared to reading from JS property
        this.isSpinnerVisible = false
        this.previousValue
        this.typingTimer
        this.events()
    }

    // 2. events
    events () {
        this.openButton.forEach(el => {
            el.addEventListener("click", e => {
                e.preventDefault()
                this.openOverlay()
            })
        })

        this.closeButton.addEventListener("click", () => this.closeOverlay())
        document.addEventListener("keydown", e => this.keyPressDispatcher(e))
        this.searchField.addEventListener("keyup", () => this.typingLogic())    // "keydown" is so fast that it doesn't even give typingLogic() the chance to evaluate if() condition is true or not, hence "keyup" is used
    }

    // 3. methods (function, action...)
    typingLogic () {
        if (this.searchField.value != this.previousValue) { // this condition allows the Spinnerwheel to keep running only when there has been changes to the search field.
            clearTimeout(this.typingTimer)

            if (this.searchField.value) {
                if (!this.isSpinnerVisible) {   // this condition controls the property of whether the Spinner is visible or not, so the spinner wheel doesn't restart everytime there is a keystroke 
                    this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>'  // Brad had already defined  "spinner-loader" in CSS
                    this.isSpinnerVisible = true
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 750)
            } else {
                this.resultsDiv.innerHTML = ""
                this.isSpinnerVisible = false
            }
        }

        this.previousValue = this.searchField.value
    }

    async getResults () {
        // WP REST API makes it possible to work with(use CRUD operation) WP using languages other than PHP (like JS, Java, or Objective-C)
        try {
            const response = await axios.get(universityData.root_url  /*this is a very useful API method written in functions.php => wp_localize_script()*/
                + "/wp-json/university/v1/search?term=" + this.searchField.value)
            const results = response.data
            this.resultsDiv.innerHTML = `
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">
                            General Information
                        </h2>
                        ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
                            ${results.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType == 'post' ? 'by ' + item.authorName : ''}</li>`).join('')}
                        ${results.generalInfo.length ? '</ul>' : ''}
                    </div>

                    <div class="one-third">
                        <h2 class="search-overlay__section-title">
                            Programs
                        </h2>
                        ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No programs match that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
                            ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                        ${results.programs.length ? '</ul>' : ''}
                        
                        <h2 class="search-overlay__section-title">
                            Professors
                        </h2>
                        ${results.professors.length ? '<ul class="professor-cards">' : `<p>No professors match that search.</p>`}
                            ${results.professors.map(item => `
                                <li class="professor-card__list-item">
                                    <a class="professor-card" href="${item.permalink}">
                                        <img class="professor-card__image" src="${item.image}">
                                        <span class="professor-card__name">${item.title}</span>
                                    </a>
                                </li>
                            `).join('')}
                        ${results.professors.length ? '</ul>' : ''}
                    </div>

                    <div class="one-third">
                        <h2 class="search-overlay__section-title">
                            Campuses
                        </h2>
                        ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No campuses match that search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
                            ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                        ${results.campuses.length ? '</ul>' : ''}

                        <h2 class="search-overlay__section-title">
                            Events
                        </h2>
                            ${results.events.length ? '' : `<p>No events match that search. <a href="${universityData.root_url}/events">View all events</a></p>`}
                            ${results.events.map(item => `
                                <div class="event-summary">
                                    <a class="event-summary__date t-center" href="${item.permalink}">
                                        <span class="event-summary__month">
                                            ${item.month}
                                        </span>
                                        <span class="event-summary__day">
                                            ${item.day}
                                        </span>
                                    </a>
                                    <div class="event-summary__content">
                                        <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                                        <p> 
                                            ${item.description}
                                            <a href="${item.permalink}" class="nu gray">Learn more</a>
                                        </p>
                                    </div>
                                </div>
                            `).join('')}
                    </div>

                </div>
            `
            this.isSpinnerVisible = false
        } catch (e) {
            console.log(e)
        }
    }

    keyPressDispatcher (e) {
        if (e.keyCode == 83 && !this.isOverlayOpen && document.activeElement.tagName != "INPUT" && document.activeElement.tagName != "TEXTAREA") {
            // e.keyCode gives you the id of the keyboard key. 83 is the id for "s". 
            // The 3rd and 4th conditions allow "s" key to open Overlay only if any input field is not in focus
            this.openOverlay()
        }

        if (e.keyCode == 27 && this.isOverlayOpen) {
            this.closeOverlay()
        }
    }

    openOverlay () {
        this.searchOverlay.classList.add("search-overlay--active")
        document.body.classList.add("body-no-scroll") // Brad had already written the css class "body-no-scroll"
        this.searchField.value = "" // this resets the search-field to be empty everytime search-overlay is newly opened
        setTimeout(() => this.searchField.focus(), 301) // after waiting 301ms for the search-overlay to be fully loaded, this automatically puts the search cursor in the field instead of having to click with a mouse
        console.log("our open method just ran!")
        this.isOverlayOpen = true
        return false
    }

    closeOverlay () {
        this.searchOverlay.classList.remove("search-overlay--active")
        document.body.classList.remove("body-no-scroll")
        console.log("our close method just ran!")
        this.isOverlayOpen = false
    }

    addSearchHTML () {
        document.body.insertAdjacentHTML(
            "beforeend",
            `
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
        
        <div class="container">
          <div id="search-overlay__results"></div>
        </div>

      </div>
    `
        )
    }
}

export default Search
