<?php include_once "functions.php"; ?>
<!DOCTYPE html>
<html>
<head>
	<title>Search Script</title>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link href="css/main.css" rel="stylesheet" id="main-css">
	<link href="css/awesomplete.css" rel="stylesheet" id="awesomplete-css">

	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
	<script src="js/awesomplete.min.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div id="imaginary_container">
					<form id="form1">
						<div class="input-group stylish-input-group">
							<input type="text" id="searchInput1" class="form-control awesomplete" data-list="#myKeyWords" data-maxitems="5"  placeholder="Search"  />
							<span class="input-group-addon">
								<button type="submit" id="submitBtn1">
									<span class="glyphicon glyphicon-search"></span>
								</button>
							</span>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="suggestionList">
					<div class="loader">
						<img src="images/loader.gif" />
					</div>
					<div id="suggestionListData">
					</div>
				</div>
			</div>
		</div>

		<ul id="myKeyWords" style="display: none;">
		<?php $keywords = $mainSite->getSearchableKeywords(); ?>
		<?php foreach ($keywords as $key => $keyword) { ?>
			<?php if (strlen($keyword) > 2) { ?>
				<li><?php echo $keyword ?></li>
			<?php } ?>
		<?php } ?>
		</ul>
	</div>
</body>
</html>