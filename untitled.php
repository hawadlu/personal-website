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
			<a class="pageLink" href="<?php echo $ExperienceOutput['Link'];?>">
				<?php 
				echo $ExperienceOutput['Link'];
				?>
			</a>

		</p>
		<p>
			GitHub: 
			<a class="pageLink" href="<?php echo $ExperienceOutput['github'];?>">
				<?php
				if (($ExperienceOutput['github']) != NULL) {
					echo $ExperienceOutput['github'];
				}
				?>
			</a>
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