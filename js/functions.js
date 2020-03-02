//This file contains all of the functions used across the website

//Show privacy (about.php)
function showElementPrivacy(id) {
    if (id === "privacy") {
        hideElementPrivacy('show');
    }
    document.getElementById(id).style.display = "block";

    const privacy = document.getElementById('privacy');
    privacy.scrollIntoView();
}

//hide privacy (about.php)
function hideElementPrivacy(id) {
    //Check if the show button needs to be re-enabled
    if (document.getElementById("show").style.display === "none" && document.getElementById("privacy").style.display === "block") {
        document.getElementById("show").style.display = "block";
    }

    document.getElementById(id).style.display = "none";
}

//Show particular elements
function showElement(id) {
    if (id === "privacy") {
        hideElement('show');

        document.getElementById(id).style.display = "block";

        const privacy = document.getElementById('privacy');
        privacy.scrollIntoView();
    } else {
        //Show the relevant element
        if (id === "addEducation") {
            document.getElementById("addProjectTab").style.backgroundColor = '#d3d3d3';
            document.getElementById("addEducationTab").style.backgroundColor = '#eee';
            hideElement('addProject');
        } else if (id === 'addProject') {
            document.getElementById("addEducationTab").style.backgroundColor = '#d3d3d3';
            document.getElementById("addProjectTab").style.backgroundColor = '#eee';
            hideElement('addEducation');
        } else if (id === "editEducation") {
            document.getElementById("projectTab").style.backgroundColor = '#d3d3d3';
            document.getElementById("educationTab").style.backgroundColor = "white";
            hideElement('editProjects');
        } else if (id === 'editProjects') {
            document.getElementById("educationTab").style.backgroundColor = '#d3d3d3';
            document.getElementById("projectTab").style.backgroundColor = "white";
            hideElement('editEducation');
        } else if (id === "showNceaInput") {
            document.getElementById("newNceaInput").style.display = "block";
            hideElement("newGpaInput");
        } else if (id === "showGpaInput") {
            document.getElementById("newGpaInput").style.display = "block";
            hideElement("newNceaInput");
        }
        document.getElementById(id).style.display = "block";
    }
}

//Code used for the slide shows
const slideIndex = [];

//Pre-populate the list. Dynamically adjusts based on the number of instances of the grid class
const slideId = [];

//Called to populate the array for each slideshow
function populateSlideshow(className) {
    for (let i = 0; i < document.querySelectorAll("." + className).length; i++) {
        slideId.push("ssID" + (i + 1));
        slideIndex.push(1);
        showDivs(1, i);
    }
}

function plusDivs(n, no) {
    showDivs(slideIndex[no] += n, no);
}

//Used to show the images in the slideshow
function showDivs(n, no) {
    let i;
    const x = document.getElementsByClassName(slideId[no]);
    if (n > x.length) {
        slideIndex[no] = 1
    }
    if (n < 1) {
        slideIndex[no] = x.length;
    }
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    try {
        x[slideIndex[no] - 1].style.display = "block";
    } catch (e) {
        //
    }
}

//Shows and hides the link input in the examples update
function showUpdateLinkInput(id) {
    //Check if the element is already displayed
    if (document.getElementById(id).style.display === "block") {
        hideElement(id);
    } else {
        document.getElementById(id).style.display = "block";
    }

}

//Hides and shows the update div for education and examples
function showUpdateDiv(divName, key, showText, hideText, deleteID) {
    var x = document.getElementById(divName);

    //The element id hidden. Display it
    if (x.style.display === "none") {
        x.style.display = "block";

        //hide delete button
        document.getElementById(deleteID).style.display = "none";

        //Change the button text to hide
        updateButton(divName + 'button', hideText)
    } else {
        x.style.display = "none";
        updateButton(divName + 'button', showText);

        //show delete button
        document.getElementById(deleteID).style.display = "block";
    }

    //Load the autocomplete
    if (divName.includes("Education")) {
        loadAutocompleteForEducationUpdate(key);
    } else if (divName.includes("Example")) {
        loadAutocompleteForExamplesUpdate(key);
    }
}

//This function takes a button id and the desired text and updates it
function updateButton(id, text) {
    document.querySelector('#' + id).innerText = text;
}

//Hides the appropriate submit button and title
function hideSubmit(submitId, titleId) {
    //If the button is already hidden call a function to show it
    if (document.getElementById(submitId).style.display !== "none") {
        document.getElementById(submitId).style.display = "none";
        document.getElementById(titleId).style.display = "none";
    } else {
        showSubmit(submitId, titleId);
    }
}

//Shows the appropriate submit button
function showSubmit(submitId, titleId) {
    document.getElementById(submitId).style.display = "block";
    document.getElementById(titleId).style.display = "block";
}

//Shows a single occurrence of an element that may occur several times on the page
function showUniqueElement(element, button, showMsg, hideMsg) {
    //If the element is not visible
    if (document.getElementById(element).style.display === "none") {
        document.getElementById(element).style.display = "block";
        updateButton(button, hideMsg);
    } else {
        //Hide the element
        document.getElementById(element).style.display = "none";
        updateButton(button, showMsg);
    }

}

//hide the popup if required
function hidePopup(popupElement) {
    //hide the popup
    document.getElementById(popupElement).style.display = "none";

    //restore the opacity
    document.getElementById("pageGrid").style.opacity = "100%";
}

//Display the popup
function showPopup(popupElement) {
    document.getElementById("pageGrid").style.opacity = "30%";

    document.getElementById(popupElement).style.display = "grid";

}


//Hide the relevant element
function hideElement(id) {
    document.getElementById(id).style.display = "none";
}

//Takes a parent div and array of child inputs
function showCreditsGpa(show) {
    var id = 0;
    //Change div for updating
    if (show.includes("Update")) {
        if (show.includes("Credits")) {
            //isolate the id
            id = show.substring("showUpdateEducationCreditsDiv".length);

            //Show and hide the relevant divs
            document.getElementById("showUpdateEducationCreditsDiv" + id).style.display = 'block';
            hideElement("showUpdateEducationGpaDiv" + id);

        } else if (show.includes("Gpa")) {
            id = show.substring("showUpdateEducationGpaDiv".length);

            //Show and hide the relevant divs
            document.getElementById("showUpdateEducationGpaDiv" + id).style.display = 'block';
            hideElement("showUpdateEducationCreditsDiv" + id);
        }

        //Change div for new records
    } else if (show.includes("New")) {
        if (show.includes("Credits")) {
            //Show and hide the relevant divs
            document.getElementById("showNewCreditsDiv").style.display = 'block';
            hideElement("showNewGpaDiv");
        } else if (show.includes("Gpa")) {
            //Show and hide the relevant divs
            document.getElementById("showNewGpaDiv").style.display = 'block';
            hideElement("showNewCreditsDiv");
        }
    }

}

//Hide and show the users records
function showElementWithButton(id, buttonId, showMessage, hideMessage) {
    if (document.getElementById(id).style.display === "none") {
        //Show the element and change the button text
        document.getElementById(id).style.display = "block";
        document.getElementById(buttonId).innerHTML = hideMessage;
    } else if (document.getElementById(id).style.display === "block") {
        //Hide the element and change the button text
        document.getElementById(id).style.display = "none";
        document.getElementById(buttonId).innerHTML = showMessage;
    }
}

//Load the hamburger menu
function loadHamburger() {
    const x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
        x.className = "topnav";
    }
}

