<html lang="English">
<!--Pulls in the head and other required pages-->
<?php
require("head.php");
?>
<body onload="showPrivacy()" class="background-img">
    <div class="page-grid-container">
        <div class="roundAll" style="background-color: white; opacity: 80%">
            <!--The grid which contains the main content of the page-->
            <div style="text-align: center;">
                <h1>
                    About Me
                </h1>
            </div>
            <p style="padding: 10px">
                <img class="aboutMeImg" src="images/Profile Pic.png" alt="Me">

                I have chosen to go down the software development route because I feel that the teamwork environment that
                the industry provides is a natural fit for my personality. I enjoy working as part of a team (especially
                sports and development teams).
                <br><br>
                I find coding a program with constraints to be an excellent learning opportunity and an enjoyable challenge.
                I have been programming for five-plus years. This has included learning languages such as python, PHP, SQL,
                CSS and HTML.
                <br><br>
                I am currently at University and practising the languages C++, Python and Java. I can bring an element of
                leadership and teamwork
                as well a hard-working, motivated personality to any role.
                <br><br>
                While in the New Zealand Cadet Corp I was a Flight Sargent which meant that I regularly had to deliver
                forty-five-minute lessons to thirteen and fourteen-year-olds. This involved planning the lessons while
                working around the tasks that I have from University and a part-time job as a waiter. This has taught me
                valuable leadership, communication and time management skills which I think would be a great fit for any
                company.</p>
            <div style="text-align: center">
                <h3>Get in touch</h3>
                <p>Email: <a href="mailto:hawadlu@outlook.com">hawadlu@outlook.com</a></p>

                <h3>My CV</h3>
                <a class = "cvDownloadButton" href="CV/CV.pdf" download>click here to download!</a>

            </div>
            <button id="show" style="display: block;" class="hidePrivacy roundBottom"
                    onclick="showElementPrivacy('privacy')">Show my cookie and privacy policy
            </button>
            <!-- Privacy policy. Hidden by default-->
            <div id="privacy" onload="" style="display: none">
                <h3 class="alignTextLeft">Cookies Policy</h3>
                <p class="alignTextLeft">Last updated: (27/01/2020)</p>
                <p class="alignTextLeft">luke.dx.am uses cookies on luke.dx.am. By using the Service, you consent to the use of cookies.</p>
                <p class="alignTextLeft">Our Cookies Policy explains what cookies are, how we use cookies, how third-parties
                    we may partner with may use cookies on the Service, your choices regarding cookies and further
                    information about cookies.</p>
                <strong><p class="alignTextLeft">What are cookies</p></strong>
                <p class="alignTextLeft">Cookies are small pieces of text sent by your web browser by a website you visit. A
                    cookie file is stored in your web browser and allows the Service or a third-party to recognize you and
                    make your next visit easier and the Service more useful to you.</p>
                <p class="alignTextLeft">Cookies can be "persistent" or "session" cookies.</p>
                <strong><p class="alignTextLeft">How luke.dx.am uses cookies</p></strong>
                <p class="alignTextLeft">When you use and access the Service, we may place a number of cookies files in your web browser.</p>
                <p class="alignTextLeft">We use cookies for the following purposes: to enable certain functions of the
                    Service, to provide analytics, to store your preferences, to enable advertisements delivery, including behavioural advertising.</p>
                <p class="alignTextLeft">We use both session and persistent cookies on the Service and we use different types of cookies to run the Service:</p>
                <p class="alignTextLeft">- Essential cookies. We may use essential cookies to authenticate users and prevent fraudulent use of user accounts.</p>
                <strong><p class="alignTextLeft">Third-party cookies</p></strong>
                <p class="alignTextLeft">In addition to our own cookies, we may also use various third-parties cookies to
                    report usage statistics of the Service, deliver advertisements on and through the Service, and so on.</p>
                <strong><p class="alignTextLeft">What are your choices regarding cookies.</p></strong>
                <p class="alignTextLeft">If you'd like to delete cookies or instruct your web browser to delete or refuse
                    cookies, please visit the help pages of your web browser.</p>
                <p class="alignTextLeft">Please note, however, that if you delete cookies or refuse to accept them, you
                    might not be able to use all of the features we offer, you may not be able to store your preferences,
                    and some of our pages might not display properly.</p>
                <p class="alignTextLeft">You can learn more about cookies on the following third-party websites.</p>
                <!--Links to external websites where the user can learn more-->
                <ul>
                    <li>
                        <a href="http://www.allaboutcookies.org/" class="onHover" style="font-size: 12px;">AllAboutCookies</a>
                    </li>
                    <li>
                        <a href="http://www.networkadvertising.org/" class="onHover" style="font-size: 12px;">Network Advertising Initiative</a>
                    </li>
                </ul>
                <h3 class="alignTextLeft">Analytics</h3>
                <p class="alignTextLeft">You can learn more about Google Analytics <a href="https://policies.google.com/privacy?hl=en-US">here.</a></p>
                <br>
                <button onclick="hideElementPrivacy('privacy')" class="roundBottom hidePrivacy">Hide</button>
            </div>
        </div>
        <script src="js/functions.js"></script>
        <script>
            //Shows the privacy if necessary
            function showPrivacy() {
                const urlString = window.location.href;
                const url = new URL(urlString);
                const c = url.searchParams.get('privacy');

                console.log(URL.searchParams);

                if (c) {
                    showElementPrivacy("privacy");
                    document.getElementById('privacy').scrollIntoView();
                }
            }
        </script>
    </div>
</body>
<!--Called last so that it renders at the top-->
<?php
require("header.php");
//Pull information from the footer page
require("footer.php");
?>
</html>
