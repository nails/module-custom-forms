<div class="form-group <?=!empty($error) ? 'has-error' : ''?>">
    <?=$html?>
    <?=!empty($error) ? '<p class="help-block">You failed the captcha test.</p>' : ''?>
</div>