import $ from 'jquery';

class Search {
    // 1. describe and create/initiate our object
    constructor () {
        this.addSearchHTML();   // this appends the entire block of HTML for the search-overlay, instead of permanently putting it in place inside footer.php
        this.resultsDiv = $("#search-overlay__results");
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.events();
        this.isOverlayOpen = false; // reading from DOM is much slower compared to reading from JS property
        this.isSpinnerVisible = false;
        this.previousValue;
        this.typingTimer;
    }

    // 2. events
    events () {
        this.openButton.on("click", this.openOverlay.bind(this)); // by default, "on" method sets the value of "this" to whatever element it's triggering at. bind(Search) sets "this.searchOverlay" to point to the "Seach"
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));
        this.searchField.on("keyup", this.typingLogic.bind(this));  // "keydown" is so fast that it doesn't even give typingLogic() the chance to evaluate if() condition is true or not, hence "keyup" is used
    }

    // 3. methods (function, action...)
    typingLogic () {
        if (this.searchField.val() != this.previousValue) {   // this condition allows the Spinnerwheel to keep running only when there has been changes to the search field.
            clearTimeout(this.typingTimer);
            if (this.searchField.val()) {
                if (!this.isSpinnerVisible) {   // this condition controls the property of whether the Spinner is visible or not, so the spinner wheel doesn't restart everytime there is a keystroke 
                    this.resultsDiv.html('<div class="spinner-loader"></div>'); // Brad had already defined  "spinner-loader" in CSS
                    this.isSpinnerVisible = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 750);
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }
        this.previousValue = this.searchField.val();
    }

    getResults () {
        // WP REST API makes it possible to work with(use CRUD operation) WP using languages other than PHP (like JS, Java, or Objective-C)
        $.getJSON(universityData.root_url /*this is a very useful API method written in functions.php*/
            + "/wp-json/university/v1/search?term=" + this.searchField.val(), (results) => {
                this.resultsDiv.html(`
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
                `);
                this.isSpinnerVisible = false;
            })


        // $.when( // this jquery method waits until all the args are asynchronously complete
        //     $.getJSON(universityData.root_url /*this is a very useful API method written in functions.php*/ + "/wp-json/wp/v2/posts?search=" + this.searchField.val()),
        //     $.getJSON(universityData.root_url + "/wp-json/wp/v2/pages?search=" + this.searchField.val())
        // ).then((posts/*this corresponds to the first arg in when() */, pages/*this corresponds to the second arg in when() */) => {
        //     const combinedResults = posts[0].concat(pages[0]);
        //     this.resultsDiv.html(`
        //             <h2 class="search-overlay__section-title">General Information</h2>
        //             ${combinedResults.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search</p>'}
        //                 ${combinedResults.map(item => `<li><a href="${item.link}">${item.title.rendered}</a> ${item.type == 'post' ? 'by ' + item.authorName : ''}</li>`).join('')}
        //             ${combinedResults.length ? '</ul>' : ''}
        //         `);
        //     this.isSpinnerVisible = false;
        // }, () => {
        //     this.resultsDiv.html('<p>Unexpected error; please try again.</p>');
        // });
    }



    keyPressDispatcher (e) {
        if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) {
            // e.keyCode gives you the id of the keyboard key. 83 is the id for "s". 
            // The third condition allows "s" key to open Overlay only if any input field is not in focus
            this.openOverlay();
        }

        if (e.keyCode == 27 && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    openOverlay () {
        this.searchOverlay.addClass("search-overlay--active");
        $('body').addClass("body-no-scroll"); // Brad had already written the css class "body-no-scroll"
        this.searchField.val('');   // this resets the search-field to be empty everytime search-overlay is newly opened
        setTimeout(() => this.searchField.focus(), 301);    // after waiting 301ms for the search-overlay to be fully loaded, this automatically puts the search cursor in the field instead of having to click with a mouse
        console.log("our open method just ran");
        this.isOverlayOpen = true;
    }

    closeOverlay () {
        this.searchOverlay.removeClass("search-overlay--active");
        $('body').removeClass("body-no-scroll");
        console.log("our close method just ran");
        this.isOverlayOpen = false;
    }

    addSearchHTML () {
        $("body").append(`
            <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                    <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                    <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term" autocomplete="off">
                    <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                </div>
            </div>

            <div class="container">
                <div id="search-overlay__results">
                    
                </div>
            </div>
        </div>
        `);
    }
}

export default Search