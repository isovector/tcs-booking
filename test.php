<form action="test.php" method="post">
<input type="text" name="name[]" />
<input type="text" name="email[]" /><br />
<input type="text" name="name[]" />
<input type="text" name="email[]" /><br />
<input type="submit" />
</form>

<?php

print_r($_POST);

?>