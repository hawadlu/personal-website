
<html>
<!--Pulls in the head and other required pages-->
<?php 
require("Head.php");
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
					$colour = '#D3D3D3';
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
			<?php
			//The query which shows the education history
			$ExpeienceQuery = ("SELECT `Experience`.`uniqueKey`, `Experience`.`name`, `Experience`.`experienceYearFK`, `relevantYear`.`relevantYear`, `Experience`.`experienceDescription`, `Experience`.`Link`, `Experience`.`github`
				FROM `Experience` 
				LEFT JOIN `relevantYear` ON `Experience`.`experienceYearFK` = `relevantYear`.`relevantYearPK`
				ORDER BY `relevantYear`.`relevantYear` DESC
				");

			$ExperienceResult = mysqli_query($con,$ExpeienceQuery);
			while($ExperienceOutput=mysqli_fetch_array($ExperienceResult)) {
				//Changing the background colour
				if ($count % 2 == 0) {
					//even
					$colour = '#D3D3D3';
					$count += 1;
				} else {
					//odd
					$colour = 'white';
					$count += 1;
				}
				?>
				<div style = "background-color: <?php echo $colour; ?>" class="experience-examples-grid-container">
					<div class="experience-examples-name">
						<center>
							<h1>
								<?php
								echo $ExperienceOutput['name'];
								?>
							</h1>
						</center>
					</div>
<div class="experience-examples-year">
						<center>
							<p>
								Year: 
								<?php
								echo $ExperienceOutput['relevantYear'];
								?>
							</p>
						</center>
					</div>
					
					
					<div class="experience-examples-langauges">
						<?php
							//Setting session variables for the uniqueKey

						$key = $ExperienceOutput['uniqueKey'];

							//Running queries to get the languages
						$LanguageOneQuery = ("SELECT Languages.language
							FROM Experience
							LEFT JOIN Languages ON Experience.languageOneFK = Languages.languagePK
							WHERE Experience.uniqueKey LIKE $key
							");

						$LanguageOneResult = mysqli_query($con,$LanguageOneQuery);
						$LanguageOneOutput=mysqli_fetch_row($LanguageOneResult);
						$LanguageOne = implode(" ", $LanguageOneOutput);

						$LanguageTwoQuery = ("SELECT Languages.language
							FROM Experience
							LEFT JOIN Languages ON Experience.languageTwoFK = Languages.languagePK
							WHERE Experience.uniqueKey LIKE $key
							");

						$LanguageTwoResult = mysqli_query($con,$LanguageTwoQuery);
						$LanguageTwoOutput=mysqli_fetch_row($LanguageTwoResult);
						$LanguageTwo = implode(" ", $LanguageTwoOutput);


						$LanguageThreeQuery = ("SELECT Languages.language
							FROM Experience
							LEFT JOIN Languages ON Experience.languageThreeFK = Languages.languagePK
							WHERE Experience.uniqueKey LIKE $key
							");

						$LanguageThreeResult = mysqli_query($con,$LanguageThreeQuery);
						$LanguageThreeOutput=mysqli_fetch_row($LanguageThreeResult);
						$LanguageThree = implode(" ", $LanguageThreeOutput);


						$LanguageFourQuery = ("SELECT Languages.language
							FROM Experience
							LEFT JOIN Languages ON Experience.languageFourFK = Languages.languagePK
							WHERE Experience.uniqueKey LIKE $key
							");

						$LanguageFourResult = mysqli_query($con,$LanguageFourQuery);
						$LanguageFourOutput=mysqli_fetch_row($LanguageFourResult);
						$LanguageFour = implode(" ", $LanguageFourOutput);


						$LanguageFiveQuery = ("SELECT Languages.language
							FROM Experience
							LEFT JOIN Languages ON Experience.languageFiveFK = Languages.languagePK
							WHERE Experience.uniqueKey LIKE $key
							");

						$LanguageFiveResult = mysqli_query($con,$LanguageFiveQuery);
						$LanguageFiveOutput=mysqli_fetch_row($LanguageFiveResult);
						$LanguageFive = implode(" ", $LanguageFiveOutput);
						

						?>
						<center>
							<p>
								language(s): 
								<?php
								if ($LanguageOne != 'NA') {
									echo $LanguageOne;
								}
								if ($LanguageTwo != 'NA') {
									echo (', ' . $LanguageTwo);
								}
								if ($LanguageThree != 'NA') {
									echo (', ' . $LanguageThree);
								}
								if ($LanguageFour != 'NA') {
									echo (', ' . $LanguageFour);
								}
								if ($LanguageFive != 'NA') {
									echo (', ' . $LanguageFive);
								}
								?>
							</p>
						</center>						
					</div>
					<div class="experience-examples-link">
						<center>
							<p>
								link: 
								<a class="pageLink" href="<?php ExperienceOutput['Link'];?>">
									<?php 
									echo $ExperienceOutput['Link'];
									?>
								</a>

							</p>
							<p>
								GitHub: 
								<a class="pageLink" href="<?php ExperienceOutput['github'];?>">
									<?php
									if (($ExperienceOutput['github']) != NULL) {
										echo $ExperienceOutput['github'];
									}
									?>
								</p>
							</center>
						
						</div>
						<div class="experience-examples-link">
							<center>
								<p>
									link: 
									<a class="pageLink" href="<?php ExperienceOutput['Lin'];?>">
										<?php 
										echo $ExperienceOutput['Link'];
										?>
									</a>

								</p>
								<p>
									GitHub: 
									<a class="pageLink" href="<?php ExperienceOutput['github'];?>">
									<?php
										if (($ExperienceOutput['github']) != NULL) {
											echo $ExperienceOutput['github'];
										}
									?>
								</p>
							</center>
						</div>
						<div class="experience-examples-description">
							<center>
								<p>
									<?php
									echo $ExperienceOutput['experienceDescription'];
									?>
								</p>
							</center>
						</div>
						<div class="experience-examples-image">
						<?php
						$image = $ExperienceOutput['name'];
						$image = "Images/Project and Experience/Experience/" . $image . ".png";
						if (file_exists($image)) {

						} else {
                            //If the image does not exist, this is the default file path.
							$image = "Images/Project and Experience/No Image.png";
						}
						?>
						<center>
							<img src = "<?php echo $image;?>">
						</center>
					</div>
					</div>
				<?php } ?> 
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
