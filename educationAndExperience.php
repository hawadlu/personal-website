
<html>
<!--Pulls in the head and other required pages-->
<?php require("Head.php");
require("connect.php")?>  
<div class="page-grid-container">     
	<!--The first div of the page grid-->
	<div>
		<?php
		require("Header.php");
		require("Nav.php");
		?>
	</div>

	<!--The second div of the page grid-->
	<div>
		<!--The grid which contains the main content of the page-->
		<div class="tab">
			<div class="educationAndExperience-grid-container">
				<div>               
					<button class="tablinks" id="defaultOpen" onclick="showEducationAndExperience(event, 'Education')">Education</button>
				</div>
				<div>
					<button class="tablinks" onclick="showEducationAndExperience(event, 'Experience')">Experience</button>
				</div>
			</div>


		</div>

		<div id="Education" class="tabcontent">
			<?php
			//Setting a varibale so that the institution is only printed once
			$institution = '';

			//Variabke that allows alternating background colours
			$count = 0;

        	//The query which shows the education history
			$EducationQuery = ("SELECT `Education`.`subjectFK`, `Subject`.`subject`, `Education`.`institutionFK`, `Institution`.`institution`, `Education`.`gradeFk`, `Grade`.`grade`, `Education`.`subjectLevelFK`, `subjectLevel`.`subjectLevel`, `Education`.`credits`, `Education`.`classYearFK`, `relevantYear`.`relevantYear`, `Education`.`subjectAbbreviationFK`, `subjectAbbreviation`.`subjectAbbreviation`, `Education`.`endorsementFK`, `Endorsement`.`endorsement`
				FROM `Education` 
				LEFT JOIN `Subject` ON `Education`.`subjectFK` = `Subject`.`subjectPK` 
				LEFT JOIN `Institution` ON `Education`.`institutionFK` = `Institution`.`institutionPK` 
				LEFT JOIN `Grade` ON `Education`.`gradeFk` = `Grade`.`gradePK` 
				LEFT JOIN `subjectLevel` ON `Education`.`subjectLevelFK` = `subjectLevel`.`subjectLevelPK` 
				LEFT JOIN `relevantYear` ON `Education`.`classYearFK` = `relevantYear`.`relevantYearPK` 
				LEFT JOIN `subjectAbbreviation` ON `Education`.`subjectAbbreviationFK` = `subjectAbbreviation`.`subjectAbbreviationPK` 
				LEFT JOIN `Endorsement` ON `Education`.`endorsementFK` = `Endorsement`.`endorsementPK`
				ORDER BY `relevantYear`.`relevantYear` DESC;
				");

			$EducationResult = mysqli_query($con,$EducationQuery);
			while($EducationOutput=mysqli_fetch_array($EducationResult)) {
				//Changing the background colour
				if ($count % 2 == 0) {
					//even
					$colour = 'grey';
					$count += 1;
				} else {
					//odd
					$colour = 'white';
					$count += 1;
				}

				//checks for university than college
				if (($EducationOutput['institutionFK']) == '0') {


				?>
							<center>
							<h1>
								<?php
								if (($EducationOutput['institution'])!=($institution)) {
									echo $EducationOutput['institution'];
									$institution = $EducationOutput['institution'];

								}

								?>
							</h1>
						</center>			
				<div style = "background-color: <?php echo $colour; ?>" class="education-grid-container-uni">
					<div class="education-Subject-uni">
						<center>
							<p>
								<strong>
									Subject: 
									<?php
										echo '(' . $EducationOutput['subjectAbbreviation'] . ')';
									?>
								</strong>
								<?php
									echo $EducationOutput['subject'];
								?>
							</p>
						</center>
					</div>
					<div class="education-Grade-uni">
						<center>
							<p>
								Grade: 
								<?php
									echo $EducationOutput['grade'];
								?>
							</p>
						</center>
					</div>
					<div class="education-subjectLevel-uni">
						<center>
							<p>
								Subject Level: 
								<?php
									echo $EducationOutput['subjectLevel'];
								?>
							</p>
						</center>
					</div>
					<div class="education-Year-uni">
						<center>
							<p>
								Year: 
								<?php
									echo $EducationOutput['relevantYear'];
								?>
							</p>
						</center>
					</div>
				</div>
				<?php
			} else {
				//printing results for tawa college
				?>
				<center>
							<h1>
								<?php
								if (($EducationOutput['institution'])!=($institution)) {
									echo $EducationOutput['institution'];
									$institution = $EducationOutput['institution'];

								}

								?>
							</h1>
						</center>
				<div style = "background-color: <?php echo $colour; ?>" class="education-grid-container-col">
					<div class="education-Subject-col">
						<center>
							<p>
								<strong>
									Subject: 
									<?php
										echo '(' . $EducationOutput['subjectAbbreviation'] . ')';
									?>
								</strong>
								<?php
									echo $EducationOutput['subject'];
								?>
							</p>
						</center>
					</div>
					<div class="education-Endorsement-col">
						<center>
							<p>
								Endorsement: 
								<?php
									echo $EducationOutput['endorsement'];
								?>
							</p>
						</center>
					</div>
					<div class="education-Credits-col">
						<center>
							<p>
								Credits: 
								<?php
									echo $EducationOutput['credits'];
								?>
							</p>
						</center>
					</div>
					<div class="education-subjectLevel-col">
						<center>
							<p>
								Subject Level: 
								<?php
									echo $EducationOutput['subjectLevel'];
								?>
							</p>
						</center>
					</div>
					<div class="education-Year-col">
						<center>
							<p>
								Year: 
								<?php
									echo $EducationOutput['relevantYear'];
								?>
							</p>
						</center>
					</div>
				</div>
				<?php
			}
		} 
			?>

		</div>

		<div id="Experience" class="tabcontent">
			<div class="experience-examples-grid-container">
				<div class="experience-examples-name">
					<center>
						<h1>
							Experience Name
						</h1>
					</center>
				</div>
				<div class="experience-examples-year">
					<center>
						<p>
							Year: 2019
						</p>
					</center>
				</div>
				<div class="experience-examples-langauges">
					<center>
						<p>
							language(s): HTML, CSS, JavaScript
						</p>
					</center>
				</div>
				<div class="experience-examples-link">
					<center>
						<p>
							link: abcdef
						</p>
					</center>
				</div>
				<div class="experience-examples-description">
					<center>
						<p>
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac elementum tellus. Pellentesque vehicula metus eu posuere vulputate. Aliquam erat volutpat. Sed metus nibh, malesuada consectetur dolor a, commodo convallis sapien. Vestibulum ex magna, laoreet vitae nibh feugiat, sodales vestibulum eros. Nullam gravida ultricies magna, quis fringilla est posuere nec. Integer congue cursus eros, quis maximus orci. In hac habitasse platea dictumst. Nam tincidunt, nisl eget eleifend posuere, nisi ex gravida ex, vitae blandit felis erat a urna. Sed id quam odio. Sed suscipit id nunc ut porttitor. Suspendisse feugiat eget erat sit amet dapibus. Donec auctor velit vitae quam lobortis dignissim.
						</p>
					</center>
				</div>
			</div>
		</div>

		<script>
			function showEducationAndExperience(evt, cityName) {
				var i, tabcontent, tablinks;
				tabcontent = document.getElementsByClassName("tabcontent");
				for (i = 0; i < tabcontent.length; i++) {
					tabcontent[i].style.display = "none";
				}
				tablinks = document.getElementsByClassName("tablinks");
				for (i = 0; i < tablinks.length; i++) {
					tablinks[i].className = tablinks[i].className.replace(" active", "");
				}
				document.getElementById(cityName).style.display = "block";
				evt.currentTarget.className += " active";
			}
    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>

</div>



</div>
<!-- Footer -->
<?php
//Pull information from the footer page
require("Footer.php");//'Require is 100% needed for this site to run
?>
</html>
