<?php

class Upavadi_Shortcode_FamilyForm extends Upavadi_Shortcode_AbstractShortcode
{
    const SHORTCODE = 'upavadi_pages_familyform';

    //do shortcode Add Family form
    public function show()
    {
        $personId = filter_input(INPUT_GET, 'personId', FILTER_SANITIZE_SPECIAL_CHARS);
        $context = array();
        $context['personId'] = $personId;
        return $this->templates->render('familyform.html', $context);
    }
}