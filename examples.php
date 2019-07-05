
<html>
<!--Pulls in the head and other required pages-->
<?php 
require("Head.php");
require("connect.php")
?>  
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

		</div>
		<?php
			//The query which shows the education history
			$ExpeienceQuery = ("SELECT `Examples`.`uniqueKey`, `Examples`.`name`, `Examples`.`exampleYearFK`, `relevantYear`.`relevantYear`, `Examples`.`examplesDescription`, `Examples`.`Link`, `Examples`.`github`
				FROM `Examples` 
				LEFT JOIN `relevantYear` ON `Examples`.`exampleYearFK` = `relevantYear`.`relevantYearPK`
				ORDER BY `relevantYear`.`relevantYear` DESC
				");

			$ExamplesResult = mysqli_query($con,$ExpeienceQuery);
			$count = 0;
			while($ExamplesOutput=mysqli_fetch_array($ExamplesResult)) {
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
					<div class="experience-examples-image">
						<?php
						$image = $ExamplesOutput['name'];
						$image = "Images/Examples/" . $image . ".png";
						if (file_exists($image)) {

						} else {
                            //If the image does not exist, this is the default file path.
							$image = "Images/Examples/No Image.png";
						}
						?>
						<center>
							<img src = "<?php echo $image;?>">
						</center>
					</div>
					<div class="experience-examples-name">
						<center>
							<h1>
								<?php
								echo $ExamplesOutput['name'];
								?>
							</h1>
						</center>
					</div>
					<div class="experience-examples-year">
						<center>
							<p>
								Year: 
								<?php
								echo $ExamplesOutput['relevantYear'];
								?>
							</p>
						</center>
					</div>
					<div class=".experience-examples-langauges">
						<?php
							//Setting session variables for the uniqueKey

						$key = $ExamplesOutput['uniqueKey'];

							//Running queries to get the languages
						$LanguageOneQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageOneFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

						$LanguageOneResult = mysqli_query($con,$LanguageOneQuery);
						$LanguageOneOutput=mysqli_fetch_row($LanguageOneResult);
						$LanguageOne = implode(" ", $LanguageOneOutput);

						$LanguageTwoQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageTwoFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

						$LanguageTwoResult = mysqli_query($con,$LanguageTwoQuery);
						$LanguageTwoOutput=mysqli_fetch_row($LanguageTwoResult);
						$LanguageTwo = implode(" ", $LanguageTwoOutput);


						$LanguageThreeQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageThreeFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

						$LanguageThreeResult = mysqli_query($con,$LanguageThreeQuery);
						$LanguageThreeOutput=mysqli_fetch_row($LanguageThreeResult);
						$LanguageThree = implode(" ", $LanguageThreeOutput);


						$LanguageFourQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageFourFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
							");

						$LanguageFourResult = mysqli_query($con,$LanguageFourQuery);
						$LanguageFourOutput=mysqli_fetch_row($LanguageFourResult);
						$LanguageFour = implode(" ", $LanguageFourOutput);


						$LanguageFiveQuery = ("SELECT Languages.language
							FROM Examples
							LEFT JOIN Languages ON Examples.languageFiveFK = Languages.languagePK
							WHERE Examples.uniqueKey LIKE $key
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
								<?php 
								//Only displays if there is a link to display
								if ($ExamplesOutput['Link'] != '0') {
								?> 
									GitHub: 
									<a class="pageLink" href="<?php echo $ExamplesOutput['Link'];?>">
										<?php
											echo $ExamplesOutput['Link'];
										?>
									</a>
									<?php
								}
								?>

							</p>
							<p>
								<?php 
								//Only displays if there is a link to display
								if ($ExamplesOutput['github'] != '0') {


								?> 
									GitHub: 
									<a class="pageLink" href="<?php echo $ExamplesOutput['github'];?>">
										<?php
											echo $ExamplesOutput['github'];
										?>
									</a>
									<?php
								}
								?>
							</p>
						</center>
					</div>
					<div class="experience-examples-description">
						<center>
							<p>
								<?php
								echo $ExamplesOutput['examplesDescription'];
								?>
							</p>
						</center>
					</div>
				</div>
			<?php } ?> 


</div>



</div>
<!-- Footer -->
<?php
//Pull information from the footer page
require("Footer.php");//'Require is 100% needed for this site to run
?>
</html>
