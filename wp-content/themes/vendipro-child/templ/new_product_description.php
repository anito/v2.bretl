<?php

$attachement = get_attachment_by_post_name("dg_dummy_document_file");

if($attachement)
    $id = $attachement->ID;
else
    $id = "";

?>
<h3>Produkt Highlights</h3>
<div class="">
    <ul>
        <li>Punkt 1</li>
        <li>Punkt 2</li>
        <li>Punkt 3</li>
    </ul>
</div>
<h6>Datenblatt:</h6>
<p>[dg attachment_pg="false" new_window="true" paginate="true" ids="<?php echo $id; ?>"]</p>
<h6 class="info">Information:</h6>
<div>
    <ul>
        <li>Punkt 1</li>
        <li>Punkt 2</li>
        <li>Punkt 3</li>
    </ul>
</div>