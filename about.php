<?php
require("header.php");
require("head.php");
?>
<html lang="English">
<!--Pulls in the head and other required pages-->
<body onload="showPrivacy()" class="background-img">
    <div class="page-grid-container">
        <div class="roundAll about-me-grid-container">
            <!--The grid which contains the main content of the page-->
            <div class="title">
                <h1>
                    About Me
                </h1>
            </div>
            <div class = "text">
                <img class="imgFloatLeft" src="images/Profile Pic.png" alt="Me">
                <p style="padding: 10px">
                    At 23 years old, I am a passionate software engineer, committed to both my professional and personal pursuits. My dedication to continuous learning and growth is evident in various aspects of my life, from my career to my hobbies. With a degree in Software Engineering from Victoria University of Wellington, I specialize in developing mobile applications using React Native. This website provides more information about my background, education, and interests, including my love of model aircraft flying, long-distance running, and small project development.
                </p>
                <h2 style="padding-left: 10px">Professional Experience</h2>
                <p style="padding: 10px">
                    <img class="imgFloatRight" src="images/fiq/fiq.png" alt="Me">
                    Currently, I am employed at FarmIQ Systems Limited, located in Wellington, New Zealand. Within the company, I am part of the team responsible for developing the <a href="#" onclick="window.location.href = chooseLink()">FarmIQ Mobile App</a>, a cross-platform application utilizing React Native with full offline capabilities. The FarmIQ app serves as a digital diary for farmers, enabling them to easily view and manage stock, mobs, paddocks, and mob movements. My primary focus has been on refining form elements, including the redevelopment of a date picker and drop-down selector.
                    <br><br>
                    Before transitioning to a full-time role, I worked part-time at FarmIQ while completing my final year of studies. My responsibilities during this period included bug fixes in our Java application and data fixes in the SQL database, ensuring the accuracy of our customers' information.
                    <br><br>
                    My journey with FarmIQ began with a four-month full-time internship in November 2022. During this time, I contributed to the development of the CRV myHerd app, focusing on API and form functionality.
                </p>
                <h2 style="padding: 10px">Education</h2>
                <p style="padding: 10px">
                    <img class="imgFloatLeft" src="images/planes/BushmuleProject.png" alt="Me">
                    Prior to my professional experience, I pursued a Bachelor of Engineering with Honours in Software Engineering at Victoria University of Wellington, graduating in May 2023. Throughout my academic journey, I worked on a diverse range of projects, such as the development of a mobile air quality monitoring system that could be attached to a drone for portable airborne air quality measurements. I also gained proficiency in various programming languages, including Java, PHP, React Native, React Ionic, C, C++, and Dafny.
                    <br><br>
                    In addition to my studies, I worked part-time at a Chinese restaurant, which enabled me to pay off my student loans and develop essential interpersonal skills, particularly when interacting with challenging customers.
                </p>
                <h2 style="padding: 10px">Interests & Hobbies</h2>
                <p style="padding: 10px">
                    <img class="imgFloatRight" src="images/planes/Extra.png" alt="Me">
                    In my leisure time, I enjoy the excitement of model aircraft flying, with a few notable models in my collection, such as a 1.3m Extra 300 aerobatic model aircraft and a 1.5m Bushmule twin-engine aircraft. These models offer unique challenges and thrilling experiences that I greatly appreciate.
                    <br><br>
                    <img class="imgFloatLeft" src="images/running/running.png" alt="Me">
                    Additionally, I have a keen interest in long-distance running, having completed several marathons in various locations, including Wellington, Auckland, Queenstown, and Melbourne. Running has not only allowed me to explore diverse landscapes but also helped me to develop the discipline and determination required to succeed in both personal and professional endeavors.
                    <br><br>
                    Outside of these hobbies, I enjoy working on small projects that expand my skill set and nurture my creativity. Two notable examples include a maze solver and a grades calculator app. The maze solver was built as an extension to a university project, and you can find more details on the examples page. The grades calculator app was created to address a pain point experienced at university, as it was challenging to determine the required grades on certain assignments and exams to achieve a specific GPA. These projects showcase my dedication to continuous learning and growth, and I encourage you to explore them to gain a better understanding of my personal interests and technical abilities.
                </p>
            </div>
            <div class = "more-info">
                <h3>Get in touch</h3>
                <p>Email: <a href="mailto:luke@lukehawinkels.com">luke@lukehawinkels.com</a></p>

                <h3>My CV</h3>
                <a class = "cvDownloadButton" href="CV/Luke Hawinkels CV.pdf" download>click here to download!</a>

                <button id="show" style="display: block;" class="hidePrivacy roundBottom"
                        onclick="showElementPrivacy('privacy')">Show my cookie and privacy policy
                </button>
                <!-- Privacy policy. Hidden by default-->
                <div id="privacy" onload="" style="display: none">
                    <h3 class="alignTextLeft">Cookies Policy</h3>
                    <p class="alignTextLeft">Last updated: (27/01/2020)</p>
                    <p class="alignTextLeft">lukehawinkels.com uses cookies on lukehawinkels.com. By using the Service, you consent to the use of cookies.</p>
                    <p class="alignTextLeft">Our Cookies Policy explains what cookies are, how we use cookies, how third-parties
                        we may partner with may use cookies on the Service, your choices regarding cookies and further
                        information about cookies.</p>
                    <strong><p class="alignTextLeft">What are cookies</p></strong>
                    <p class="alignTextLeft">Cookies are small pieces of text sent by your web browser by a website you visit. A
                        cookie file is stored in your web browser and allows the Service or a third-party to recognize you and
                        make your next visit easier and the Service more useful to you.</p>
                    <p class="alignTextLeft">Cookies can be "persistent" or "session" cookies.</p>
                    <strong><p class="alignTextLeft">How lukehawinkels.com uses cookies</p></strong>
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
                    <ul class = "alignTextLeft">
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
        </div>
        <script src="js/functions.js"></script>
        <script>
            function chooseLink() {
                const userAgent = navigator.userAgent.toLowerCase();

                if (userAgent.includes("iphone") || userAgent.includes("ipad")) {
                    return "https://apps.apple.com/nz/app/farmiq/id1551569914";
                } else if (userAgent.includes("android")) {
                    return "https://play.google.com/store/apps/details?id=nz.co.farmiq.modern&hl=en_US";
                } else {
                    return "https://farmiq.co.nz/";
                }
            }

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
