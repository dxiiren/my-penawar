<!DOCTYPE html>
<html lang="en">
<head>
<?php $pageTitle = 'PHP Output Test'; include 'partials/head.php'; ?>
</head>
<body class="flex min-h-screen items-center justify-center bg-gradient-to-b from-teal-50/70 to-white font-sans text-slate-700 antialiased">
	<main class="card mx-4 w-full max-w-xl p-8">
		<h1 class="text-2xl font-extrabold tracking-tight text-slate-900"> PHP Output Test  </h1>
		<div class="mt-4 text-sm leading-6 text-slate-600">
	<?php
		$f1 = "This is my first time testing PHP <br><br>";
		$f2 = "Hello World <br>";
		$f3 = "Today I am learning how to output using \"<b>PHP</b>...\" <br> ";
		$f4 = "To insert a single line <i>comment</i> , use these : <br>";
		$f5 = "\\ Comment 1 <br>";
		$f6 = "# Comment 2 <br>";
		$f7 = "To create a variable, do this <br>";
		$f8 = "$variableName";

		echo $f1;
		echo $f2;
		echo $f3;
		echo $f4;
		echo $f5;
		echo $f6;
		echo $f7;
		echo $f8;



	?>
		</div>
		<a href="index.html" class="btn-outline mt-6"><i class="fa-solid fa-arrow-left"></i> Go to the home page</a>
	</main>
</body>
</html>
