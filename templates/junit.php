<?xml version="1.0" encoding="UTF-8"?>

<testsuites duration= "<?=$this->e($duration)?>">
    <testsuite name="comparison test" failures= "<?=$this->e($failures)?>" tests= "<?=$this->e($tests)?>" skipped= "<?=$this->e($skips)?>" errors= "<?=$this->e($errors)?>">
        <?php echo $testcases; ?>

    </testsuite>
</testsuites>