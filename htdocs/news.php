<?php

include "includes/common.php";

//test comment
$skin = new skin("news.skn");

for($i = 0; $i < 3; $i++) {
	$skin->addRow("news", "Lorem Ipsum", "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque leo metus, sodales eget, faucibus a, ultrices fringilla, nulla. Donec eleifend, ligula eu feugiat egestas, enim tellus blandit nisl, ut tincidunt lacus diam in mauris. Proin semper ultricies eros. Vestibulum eu est. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque lectus. In placerat lectus gravida urna tempus vestibulum. Curabitur aliquam ultricies lectus. Pellentesque magna erat, aliquet nec, facilisis eget, bibendum a, dui. Aenean massa massa, faucibus nec, interdum quis, ultrices non, odio. Integer porttitor justo in nunc. Ut ac mi ut ante aliquam iaculis. Donec arcu. Phasellus condimentum. Praesent ultricies purus et lorem. Vivamus sodales. Duis quis neque ac leo iaculis aliquet. Nullam eu nulla. Cras luctus.");
}

$skin->flushRows("news");

$skin->dump();

?>
